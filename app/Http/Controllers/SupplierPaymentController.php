<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPaymentController extends Controller
{
    private const STATUTS = ['Inst', 'Payé', 'Report', 'Imp', 'Dévalidé'];

    public function index(): JsonResponse
    {
        $payments = SupplierPayment::query()
            ->with(['supplier', 'allocations.purchaseOrder'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (SupplierPayment $payment) => $this->formatPayment($payment))
            ->values();

        $next = (SupplierPayment::max('id') ?? 0) + 1;

        return response()->json([
            'data' => $payments,
            'meta' => [
                'next_ref' => $this->referenceFor($next),
                'date' => now()->format('Y-m-d'),
                'suppliers' => Supplier::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (Supplier $supplier) => [
                        'id' => $supplier->id,
                        'code' => $supplier->code,
                        'name' => $supplier->name,
                    ])
                    ->values(),
            ],
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ]);

        $orders = PurchaseOrder::query()
            ->where('supplier_id', $data['supplier_id'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (PurchaseOrder $order) => $this->formatOrder($order))
            ->values();

        $total = round($orders->sum(fn (array $row) => (float) $row['montant_bon']), 2);
        $solde = round($orders->sum(fn (array $row) => (float) $row['solde']), 2);

        return response()->json([
            'data' => $orders,
            'meta' => [
                'total_ttc' => $total,
                'solde_ttc' => $solde,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $payment = DB::transaction(function () use ($data) {
            return $this->persist(null, $data);
        });

        return response()->json($this->formatPayment($payment), 201);
    }

    public function update(Request $request, SupplierPayment $supplierPayment): JsonResponse
    {
        $data = $this->validated($request);

        $payment = DB::transaction(function () use ($supplierPayment, $data) {
            $this->rollbackPaid($supplierPayment);
            $supplierPayment->allocations()->delete();

            return $this->persist($supplierPayment, $data);
        });

        return response()->json($this->formatPayment($payment));
    }

    public function destroy(SupplierPayment $supplierPayment): JsonResponse
    {
        DB::transaction(function () use ($supplierPayment) {
            $this->rollbackPaid($supplierPayment);
            $supplierPayment->allocations()->delete();
            $supplierPayment->delete();
        });

        return response()->json(['message' => 'Règlement supprimé']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'reglement' => ['nullable', 'in:Esp,Chq,Eff,Vir,Vers'],
            'numero' => ['nullable', 'string', 'max:50'],
            'banque' => ['nullable', 'string', 'max:100'],
            'nom_tire' => ['nullable', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'date_decaissement' => ['nullable', 'date'],
            'remarque' => ['nullable', 'string', 'max:1000'],
            'statut' => ['nullable', 'in:'.implode(',', self::STATUTS)],
            'allocations' => ['required', 'array', 'min:1'],
            'allocations.*.purchase_order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'allocations.*.amount' => ['nullable', 'numeric', 'min:0'],
            'allocations.*.action' => ['nullable', 'in:'.implode(',', self::STATUTS)],
        ]);

        $data['reglement'] = trim((string) ($data['reglement'] ?? '')) ?: null;
        $data['numero'] = trim((string) ($data['numero'] ?? '')) ?: null;
        $data['banque'] = trim((string) ($data['banque'] ?? '')) ?: null;
        $data['nom_tire'] = trim((string) ($data['nom_tire'] ?? '')) ?: null;
        $data['remarque'] = trim((string) ($data['remarque'] ?? '')) ?: null;

        if (($data['reglement'] ?? null) === 'Esp') {
            $data['numero'] = null;
            $data['banque'] = null;
            $data['nom_tire'] = null;
        }

        return $data;
    }

    private function persist(?SupplierPayment $existing, array $data): SupplierPayment
    {
        $orderIds = collect($data['allocations'])->pluck('purchase_order_id')->all();
        $orders = PurchaseOrder::query()
            ->whereIn('id', $orderIds)
            ->where('supplier_id', $data['supplier_id'])
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($orders->count() !== count(array_unique($orderIds))) {
            throw ValidationException::withMessages([
                'allocations' => 'Certaines commandes sont invalides pour ce fournisseur.',
            ]);
        }

        $paymentAmount = round((float) $data['montant'], 2);
        $remaining = $paymentAmount;
        $built = [];

        foreach ($data['allocations'] as $row) {
            $order = $orders->get($row['purchase_order_id']);
            $action = $row['action'] ?? 'Inst';
            $due = round(max((float) $order->total - (float) ($order->montant_paye ?? 0), 0), 2);
            $explicit = array_key_exists('amount', $row) && $row['amount'] !== null && $row['amount'] !== ''
                ? round((float) $row['amount'], 2)
                : null;

            if ($explicit !== null) {
                $applied = $explicit;
            } elseif ($action === 'Payé') {
                $applied = round(min($due, $remaining), 2);
            } else {
                $applied = 0.0;
            }

            if ($applied > 0) {
                $remaining = round($remaining - $applied, 2);
            }

            $built[] = [
                'order' => $order,
                'amount' => max($applied, 0),
                'action' => $action,
            ];
        }

        if ($remaining > 0.009) {
            foreach ($built as &$row) {
                if ($remaining <= 0) {
                    break;
                }
                $due = round(max((float) $row['order']->total - (float) ($row['order']->montant_paye ?? 0) - $row['amount'], 0), 2);
                if ($due <= 0) {
                    continue;
                }
                $extra = round(min($due, $remaining), 2);
                $row['amount'] = round($row['amount'] + $extra, 2);
                $remaining = round($remaining - $extra, 2);
            }
            unset($row);
        }

        $allocated = round(collect($built)->sum('amount'), 2);
        if ($allocated - $paymentAmount > 0.009) {
            throw ValidationException::withMessages([
                'montant' => 'Le montant alloué dépasse le montant du règlement.',
            ]);
        }

        $totalTtc = round($orders->sum(fn (PurchaseOrder $order) => (float) $order->total), 2);
        $soldeAvant = round($orders->sum(fn (PurchaseOrder $order) => max((float) $order->total - (float) ($order->montant_paye ?? 0), 0)), 2);

        $statut = $data['statut'] ?? null;
        if ($statut === null || $statut === '') {
            $actions = collect($built)->pluck('action')->filter()->unique()->values();
            $statut = $actions->count() === 1 ? $actions->first() : ($actions->first() ?? 'Inst');
        }

        $payload = [
            'payment_date' => $data['payment_date'],
            'supplier_id' => $data['supplier_id'],
            'reglement' => $data['reglement'],
            'numero' => $data['numero'],
            'banque' => $data['banque'],
            'nom_tire' => $data['nom_tire'],
            'montant' => $paymentAmount,
            'date_decaissement' => $data['date_decaissement'] ?? null,
            'remarque' => $data['remarque'],
            'total_ttc' => $totalTtc,
            'solde_ttc' => round($soldeAvant - $paymentAmount, 2),
            'statut' => $statut,
        ];

        if ($existing) {
            $existing->update($payload);
            $payment = $existing;
        } else {
            $next = (SupplierPayment::max('id') ?? 0) + 1;
            $payload['reference'] = $this->referenceFor($next);
            $payment = SupplierPayment::create($payload);
        }

        foreach ($built as $row) {
            $payment->allocations()->create([
                'purchase_order_id' => $row['order']->id,
                'amount' => $row['amount'],
                'action' => $row['action'],
            ]);
            $row['order']->update([
                'montant_paye' => round((float) ($row['order']->montant_paye ?? 0) + $row['amount'], 2),
                'payment_action' => $row['action'],
            ]);
        }

        return $payment->fresh(['supplier', 'allocations.purchaseOrder']);
    }

    private function rollbackPaid(SupplierPayment $payment): void
    {
        $payment->loadMissing('allocations.purchaseOrder');
        foreach ($payment->allocations as $allocation) {
            $order = $allocation->purchaseOrder;
            if (! $order) {
                continue;
            }
            $order->update([
                'montant_paye' => round(max((float) ($order->montant_paye ?? 0) - (float) $allocation->amount, 0), 2),
            ]);
        }
    }

    private function referenceFor(int $id): string
    {
        return 'RF-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    private function formatOrder(PurchaseOrder $order): array
    {
        $bon = round((float) $order->total, 2);
        $paid = round((float) ($order->montant_paye ?? 0), 2);

        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'order_date' => $order->order_date?->format('Y-m-d'),
            'supplier_id' => $order->supplier_id,
            'fournisseur' => $order->supplier_name,
            'montant_bon' => $bon,
            'montant_paye' => $paid,
            'solde' => round($bon - $paid, 2),
            'payment_action' => $order->payment_action ?: 'Inst',
        ];
    }

    private function formatPayment(SupplierPayment $payment): array
    {
        $payment->loadMissing(['supplier', 'allocations.purchaseOrder']);

        return [
            'id' => $payment->id,
            'reference' => $payment->reference,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_date_fr' => $payment->payment_date?->format('d/m/Y'),
            'supplier_id' => $payment->supplier_id,
            'fournisseur' => $payment->supplier?->name ?? '—',
            'reglement' => $payment->reglement,
            'numero' => $payment->numero,
            'banque' => $payment->banque,
            'nom_tire' => $payment->nom_tire,
            'montant' => round((float) $payment->montant, 2),
            'date_decaissement' => $payment->date_decaissement?->format('Y-m-d'),
            'date_decaissement_fr' => $payment->date_decaissement?->format('d/m/Y'),
            'remarque' => $payment->remarque,
            'total_ttc' => round((float) $payment->total_ttc, 2),
            'solde_ttc' => round((float) $payment->solde_ttc, 2),
            'statut' => $payment->statut ?: 'Inst',
            'month' => $payment->payment_date ? (int) $payment->payment_date->format('n') : null,
            'allocations' => $payment->allocations->map(fn ($allocation) => [
                'id' => $allocation->id,
                'purchase_order_id' => $allocation->purchase_order_id,
                'bon' => $allocation->purchaseOrder?->reference,
                'amount' => round((float) $allocation->amount, 2),
                'action' => $allocation->action,
            ])->values(),
        ];
    }
}
