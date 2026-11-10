<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\DeliveryOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientPaymentController extends Controller
{
    private const STATUTS = ['Inst', 'Payé', 'Report', 'Imp', 'Dévalidé'];

    public function index(): JsonResponse
    {
        $payments = ClientPayment::query()
            ->with(['client', 'allocations.deliveryOrder'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ClientPayment $payment) => $this->formatPayment($payment))
            ->values();

        $next = (ClientPayment::max('id') ?? 0) + 1;

        return response()->json([
            'data' => $payments,
            'meta' => [
                'next_ref' => $this->referenceFor($next),
                'date' => now()->format('Y-m-d'),
                'clients' => Client::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (Client $client) => [
                        'id' => $client->id,
                        'code' => $client->code,
                        'name' => $client->name,
                    ])
                    ->values(),
            ],
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ]);

        $client = Client::query()->findOrFail($data['client_id']);
        $orders = DeliveryOrder::query()
            ->where(function ($query) use ($client) {
                $query->where('client_id', $client->id)
                    ->orWhereRaw('LOWER(client_code) = ?', [mb_strtolower($client->code)])
                    ->orWhereRaw('LOWER(client_name) = ?', [mb_strtolower(trim((string) $client->name))]);
            })
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (DeliveryOrder $order) => $this->formatOrder($order))
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

    public function update(Request $request, ClientPayment $clientPayment): JsonResponse
    {
        $data = $this->validated($request);

        $payment = DB::transaction(function () use ($clientPayment, $data) {
            $this->rollbackPaid($clientPayment);
            $clientPayment->allocations()->delete();

            return $this->persist($clientPayment, $data);
        });

        return response()->json($this->formatPayment($payment));
    }

    public function destroy(ClientPayment $clientPayment): JsonResponse
    {
        DB::transaction(function () use ($clientPayment) {
            $this->rollbackPaid($clientPayment);
            $clientPayment->allocations()->delete();
            $clientPayment->delete();
        });

        return response()->json(['message' => 'Règlement supprimé']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'reglement' => ['nullable', 'in:Esp,Chq,Eff,Vir,Vers'],
            'numero' => ['nullable', 'string', 'max:50'],
            'banque' => ['nullable', 'string', 'max:100'],
            'nom_tire' => ['nullable', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'date_decaissement' => ['nullable', 'date'],
            'remarque' => ['nullable', 'string', 'max:1000'],
            'statut' => ['nullable', 'in:'.implode(',', self::STATUTS)],
            'allocations' => ['required', 'array', 'min:1'],
            'allocations.*.delivery_order_id' => ['required', 'integer', 'exists:delivery_orders,id'],
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

    private function persist(?ClientPayment $existing, array $data): ClientPayment
    {
        $orderIds = collect($data['allocations'])->pluck('delivery_order_id')->all();
        $orders = DeliveryOrder::query()
            ->whereIn('id', $orderIds)
            ->where('client_id', $data['client_id'])
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($orders->count() !== count(array_unique($orderIds))) {
            throw ValidationException::withMessages([
                'allocations' => 'Certaines livraisons sont invalides pour ce client.',
            ]);
        }

        $paymentAmount = round((float) $data['montant'], 2);
        $remaining = $paymentAmount;
        $built = [];

        foreach ($data['allocations'] as $row) {
            $order = $orders->get($row['delivery_order_id']);
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

        $totalTtc = round($orders->sum(fn (DeliveryOrder $order) => (float) $order->total), 2);
        $soldeAvant = round($orders->sum(fn (DeliveryOrder $order) => max((float) $order->total - (float) ($order->montant_paye ?? 0), 0)), 2);

        $statut = $data['statut'] ?? null;
        if ($statut === null || $statut === '') {
            $actions = collect($built)->pluck('action')->filter()->unique()->values();
            $statut = $actions->count() === 1 ? $actions->first() : ($actions->first() ?? 'Inst');
        }

        $payload = [
            'payment_date' => $data['payment_date'],
            'client_id' => $data['client_id'],
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
            $next = (ClientPayment::max('id') ?? 0) + 1;
            $payload['reference'] = $this->referenceFor($next);
            $payment = ClientPayment::create($payload);
        }

        foreach ($built as $row) {
            $payment->allocations()->create([
                'delivery_order_id' => $row['order']->id,
                'amount' => $row['amount'],
                'action' => $row['action'],
            ]);
            $row['order']->update([
                'montant_paye' => round((float) ($row['order']->montant_paye ?? 0) + $row['amount'], 2),
                'payment_action' => $row['action'],
            ]);
        }

        return $payment->fresh(['client', 'allocations.deliveryOrder']);
    }

    private function rollbackPaid(ClientPayment $payment): void
    {
        $payment->loadMissing('allocations.deliveryOrder');
        foreach ($payment->allocations as $allocation) {
            $order = $allocation->deliveryOrder;
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
        return 'RC-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    private function formatOrder(DeliveryOrder $order): array
    {
        $bon = round((float) $order->total, 2);
        $paid = round((float) ($order->montant_paye ?? 0), 2);

        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'order_date' => $order->order_date?->format('Y-m-d'),
            'client_id' => $order->client_id,
            'client' => $order->client_name,
            'montant_bon' => $bon,
            'montant_paye' => $paid,
            'solde' => round($bon - $paid, 2),
            'payment_action' => $order->payment_action ?: 'Inst',
        ];
    }

    private function formatPayment(ClientPayment $payment): array
    {
        $payment->loadMissing(['client', 'allocations.deliveryOrder']);

        return [
            'id' => $payment->id,
            'reference' => $payment->reference,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_date_fr' => $payment->payment_date?->format('d/m/Y'),
            'client_id' => $payment->client_id,
            'client' => $payment->client?->name ?? '—',
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
                'delivery_order_id' => $allocation->delivery_order_id,
                'bon' => $allocation->deliveryOrder?->reference,
                'amount' => round((float) $allocation->amount, 2),
                'action' => $allocation->action,
            ])->values(),
        ];
    }
}
