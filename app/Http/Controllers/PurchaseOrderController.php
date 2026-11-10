<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = PurchaseOrder::query()
            ->with('items')
            ->orderByDesc('id')
            ->get()
            ->map->toFicheArray()
            ->values();

        $next = (PurchaseOrder::max('id') ?? 0) + 1;
        $totalAchats = round((float) $rows->sum('total'), 2);
        $totalPaye = round((float) $rows->sum('paid'), 2);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_ref' => 'BA-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'total_achats' => $totalAchats,
                'total_paye' => $totalPaye,
                'solde' => round($totalAchats - $totalPaye, 2),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $order = DB::transaction(function () use ($data) {
            [$lines, $totals] = $this->buildLines($data);
            $order = PurchaseOrder::create([
                'order_date' => $data['order_date'],
                'reference' => $data['reference'],
                'supplier_id' => $this->supplierId($data['supplier_name']),
                'supplier_code' => $data['supplier_code'] ?: null,
                'supplier_name' => $data['supplier_name'],
                'payment_type' => $data['payment_type'] ?: null,
                'due_term' => $data['due_term'] ?: null,
                'qty_total' => $totals['qty_total'],
                'total' => $totals['total'],
            ]);
            foreach ($lines as $line) {
                $line['purchase_order_id'] = $order->id;
                PurchaseOrderItem::create($line);
            }

            return $order->load('items');
        });

        return response()->json($order->toFicheArray(), 201);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $data = $this->validated($request, $purchaseOrder);
        $order = DB::transaction(function () use ($data, $purchaseOrder) {
            [$lines, $totals] = $this->buildLines($data);
            $purchaseOrder->update([
                'order_date' => $data['order_date'],
                'reference' => $data['reference'],
                'supplier_id' => $this->supplierId($data['supplier_name']),
                'supplier_code' => $data['supplier_code'] ?: null,
                'supplier_name' => $data['supplier_name'],
                'payment_type' => $data['payment_type'] ?: null,
                'due_term' => $data['due_term'] ?: null,
                'qty_total' => $totals['qty_total'],
                'total' => $totals['total'],
            ]);
            $purchaseOrder->items()->delete();
            foreach ($lines as $line) {
                $line['purchase_order_id'] = $purchaseOrder->id;
                PurchaseOrderItem::create($line);
            }

            return $purchaseOrder->load('items');
        });

        return response()->json($order->toFicheArray());
    }

    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->delete();

        return response()->json(['message' => 'Bon supprimé']);
    }

    private function validated(Request $request, ?PurchaseOrder $order = null): array
    {
        $data = $request->validate([
            'order_date' => ['required', 'date'],
            'reference' => ['required', 'string', 'max:64', Rule::unique('purchase_orders', 'reference')->ignore($order?->id)],
            'supplier_code' => ['nullable', 'string', 'max:64'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'payment_type' => ['nullable', 'in:Esp,Chq,Eff,Vir,Vers'],
            'due_term' => ['nullable', 'string', 'max:20'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_ref' => ['nullable', 'string', 'max:64'],
            'items.*.barcode' => ['nullable', 'string', 'max:64'],
            'items.*.equiv' => ['nullable', 'string', 'max:64'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.brand' => ['nullable', 'string', 'max:120'],
            'items.*.family' => ['nullable', 'string', 'max:120'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.remise' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['supplier_code'] = trim((string) ($data['supplier_code'] ?? ''));
        $data['supplier_name'] = trim($data['supplier_name']);
        $data['reference'] = trim($data['reference']);
        $data['payment_type'] = trim((string) ($data['payment_type'] ?? '')) ?: null;
        $data['due_term'] = trim((string) ($data['due_term'] ?? ''));
        $this->rejectDuplicateProducts($data['items']);

        return $data;
    }

    private function rejectDuplicateProducts(array $items): void
    {
        $seen = [];
        foreach ($items as $item) {
            $key = implode("\n", [
                mb_strtolower(trim((string) ($item['article_ref'] ?? ''))),
                mb_strtolower(trim((string) ($item['description'] ?? ''))),
                mb_strtolower(trim((string) ($item['equiv'] ?? ''))),
                mb_strtolower(trim((string) ($item['brand'] ?? ''))),
                mb_strtolower(trim((string) ($item['family'] ?? ''))),
            ]);
            if (isset($seen[$key])) {
                throw ValidationException::withMessages([
                    'items' => 'Ce produit est déjà saisi sur ce bon (réf, désignation, equiv, marque, famille).',
                ]);
            }
            $seen[$key] = true;
        }
    }

    private function buildLines(array $data): array
    {
        $lines = [];
        $qty = 0.0;
        $amount = 0.0;
        foreach ($data['items'] as $item) {
            $price = round((float) $item['unit_price'], 2);
            $remise = min(100, max(0, (float) ($item['remise'] ?? 0)));
            $quantity = round((float) $item['quantity'], 3);
            $sub = round($quantity * $price * (1 - $remise / 100), 2);
            $qty += $quantity;
            $amount += $sub;
            $lines[] = [
                'article_ref' => trim((string) ($item['article_ref'] ?? '')) ?: null,
                'barcode' => trim((string) ($item['barcode'] ?? '')) ?: null,
                'equiv' => trim((string) ($item['equiv'] ?? '')) ?: null,
                'description' => trim($item['description']),
                'brand' => trim((string) ($item['brand'] ?? '')) ?: null,
                'family' => trim((string) ($item['family'] ?? '')) ?: null,
                'quantity' => $quantity,
                'unit_price' => $price,
                'remise' => $remise,
                'total' => $sub,
            ];
        }

        return [$lines, ['qty_total' => round($qty, 3), 'total' => round($amount, 2)]];
    }

    private function supplierId(string $name): ?int
    {
        return Supplier::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->value('id');
    }
}
