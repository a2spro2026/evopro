<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierStatementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        ]);

        $supplierIdsWithOrders = PurchaseOrder::query()
            ->whereNotNull('supplier_id')
            ->distinct()
            ->pluck('supplier_id')
            ->filter()
            ->values()
            ->all();

        $supplierIdsWithPayments = SupplierPayment::query()
            ->whereNotNull('supplier_id')
            ->distinct()
            ->pluck('supplier_id')
            ->filter()
            ->values()
            ->all();

        $activeIds = array_values(array_unique(array_merge($supplierIdsWithOrders, $supplierIdsWithPayments)));

        $suppliers = Supplier::query()
            ->whereIn('id', $activeIds ?: [0])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Supplier $supplier) => [
                'id' => $supplier->id,
                'code' => $supplier->code,
                'name' => $supplier->name,
                'has_activity' => true,
            ])
            ->values();

        $supplierId = isset($data['supplier_id']) ? (int) $data['supplier_id'] : null;
        $supplier = $supplierId ? Supplier::query()->findOrFail($supplierId) : null;

        $ordersQuery = PurchaseOrder::query()->orderBy('order_date')->orderBy('id');
        $paymentsQuery = SupplierPayment::query()
            ->with(['supplier', 'allocations.purchaseOrder'])
            ->orderBy('payment_date')
            ->orderBy('id');

        if ($supplier) {
            $ordersQuery->where(function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
                $code = trim((string) $supplier->code);
                $name = trim((string) $supplier->name);
                if ($code !== '') {
                    $query->orWhereRaw('LOWER(supplier_code) = ?', [mb_strtolower($code)]);
                }
                if ($name !== '') {
                    $query->orWhereRaw('LOWER(supplier_name) = ?', [mb_strtolower($name)]);
                }
            });
            $paymentsQuery->where('supplier_id', $supplier->id);
        }

        $events = collect();

        $ordersQuery->get()->each(function (PurchaseOrder $order) use ($events) {
            $events->push([
                'sort_date' => $order->order_date?->format('Y-m-d') ?? '1970-01-01',
                'sort_id' => $order->id,
                'operation' => 'Achat',
                'date' => $order->order_date?->format('Y-m-d'),
                'nom' => $order->supplier_name ?: '—',
                'bon' => $order->reference,
                'qte' => round((float) $order->qty_total, 3),
                'debit' => round((float) $order->total, 2),
                'credit' => 0.0,
                'type_reg' => null,
                'num_reg' => null,
                'nom_tire' => null,
                'date_encaiss' => null,
                'action' => null,
            ]);
        });

        $paymentsQuery->get()->each(function (SupplierPayment $payment) use ($events) {
            $action = trim((string) ($payment->statut ?? ''));
            $events->push([
                'sort_date' => $payment->payment_date?->format('Y-m-d') ?? '1970-01-01',
                'sort_id' => 1000000 + $payment->id,
                'operation' => 'Rég',
                'date' => $payment->payment_date?->format('Y-m-d'),
                'nom' => $payment->supplier?->name ?: '—',
                'bon' => null,
                'qte' => null,
                'debit' => 0.0,
                'credit' => round((float) $payment->montant, 2),
                'type_reg' => $payment->reglement,
                'num_reg' => $payment->numero ?: $payment->reference,
                'nom_tire' => $payment->nom_tire,
                'date_encaiss' => $payment->date_decaissement?->format('Y-m-d'),
                'action' => $action !== '' ? $action : null,
            ]);
        });

        $sorted = $events->sortBy([
            ['sort_date', 'asc'],
            ['sort_id', 'asc'],
        ])->values();

        $running = 0.0;
        $rows = $sorted->map(function (array $row) use (&$running) {
            $running = round($running + (float) $row['debit'] - (float) $row['credit'], 2);
            $action = trim((string) ($row['action'] ?? ''));

            return [
                'operation' => $row['operation'],
                'date' => $row['date'],
                'date_fr' => $row['date'] ? $this->frDate($row['date']) : '—',
                'nom' => $row['nom'] ?: '—',
                'bon' => $row['bon'],
                'qte' => $row['qte'],
                'debit' => round((float) $row['debit'], 2),
                'credit' => round((float) $row['credit'], 2),
                'solde' => $running,
                'type_reg' => $row['type_reg'],
                'num_reg' => $row['num_reg'],
                'nom_tire' => $row['nom_tire'],
                'date_encaiss' => $row['date_encaiss'],
                'date_encaiss_fr' => $row['date_encaiss'] ? $this->frDate($row['date_encaiss']) : '—',
                'paye' => $action === 'Payé',
                'devalide' => $action === 'Dévalidé',
                'impaye' => $action === 'Imp',
                'reporte' => $action === 'Report',
            ];
        })->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'suppliers' => $suppliers,
                'supplier' => $supplier ? [
                    'id' => $supplier->id,
                    'code' => $supplier->code,
                    'name' => $supplier->name,
                ] : null,
                'total_debit' => round($rows->sum('debit'), 2),
                'total_credit' => round($rows->sum('credit'), 2),
                'solde' => $running,
            ],
        ]);
    }

    private function frDate(string $value): string
    {
        $parts = explode('-', $value);

        return count($parts) === 3 ? $parts[2].'/'.$parts[1].'/'.$parts[0] : $value;
    }
}
