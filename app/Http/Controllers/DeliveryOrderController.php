<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DeliveryOrderController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = DeliveryOrder::query()
            ->with('items')
            ->orderByDesc('id')
            ->get()
            ->map->toFicheArray()
            ->values();

        $next = (DeliveryOrder::max('id') ?? 0) + 1;
        $totalVentes = round((float) $rows->sum('total'), 2);
        $totalPaye = round((float) $rows->sum('paid'), 2);

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_ref' => 'BL-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'total_ventes' => $totalVentes,
                'total_paye' => $totalPaye,
                'solde' => round($totalVentes - $totalPaye, 2),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $order = DB::transaction(function () use ($data) {
            [$lines, $totals] = $this->buildLines($data);
            $order = DeliveryOrder::create([
                'order_date' => $data['order_date'],
                'reference' => $data['reference'],
                'client_id' => $this->clientId($data['client_name'], $data['client_code']),
                'client_code' => $data['client_code'] ?: null,
                'client_name' => $data['client_name'],
                'payment_type' => $data['payment_type'] ?: null,
                'due_term' => $data['due_term'] ?: null,
                'qty_total' => $totals['qty_total'],
                'total' => $totals['total'],
            ]);
            foreach ($lines as $line) {
                $line['delivery_order_id'] = $order->id;
                DeliveryOrderItem::create($line);
            }

            return $order->load('items');
        });

        return response()->json($order->toFicheArray(), 201);
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder): JsonResponse
    {
        $data = $this->validated($request, $deliveryOrder);
        $order = DB::transaction(function () use ($data, $deliveryOrder) {
            [$lines, $totals] = $this->buildLines($data);
            $deliveryOrder->update([
                'order_date' => $data['order_date'],
                'reference' => $data['reference'],
                'client_id' => $this->clientId($data['client_name'], $data['client_code']),
                'client_code' => $data['client_code'] ?: null,
                'client_name' => $data['client_name'],
                'payment_type' => $data['payment_type'] ?: null,
                'due_term' => $data['due_term'] ?: null,
                'qty_total' => $totals['qty_total'],
                'total' => $totals['total'],
            ]);
            $deliveryOrder->items()->delete();
            foreach ($lines as $line) {
                $line['delivery_order_id'] = $deliveryOrder->id;
                DeliveryOrderItem::create($line);
            }

            return $deliveryOrder->load('items');
        });

        return response()->json($order->toFicheArray());
    }

    public function destroy(DeliveryOrder $deliveryOrder): JsonResponse
    {
        $deliveryOrder->delete();

        return response()->json(['message' => 'Bon supprimé']);
    }

    private function validated(Request $request, ?DeliveryOrder $order = null): array
    {
        $data = $request->validate([
            'order_date' => ['required', 'date'],
            'reference' => ['required', 'string', 'max:64', Rule::unique('delivery_orders', 'reference')->ignore($order?->id)],
            'client_code' => ['nullable', 'string', 'max:64'],
            'client_name' => ['required', 'string', 'max:255'],
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

        $data['client_code'] = trim((string) ($data['client_code'] ?? ''));
        $data['client_name'] = trim($data['client_name']);
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
        $catalog = StockMovementController::products()->keyBy(function (array $row) {
            return mb_strtolower(trim((string) $row['reference'])).'|'.mb_strtolower(trim((string) $row['designation']));
        });
        foreach ($data['items'] as $item) {
            $lookup = mb_strtolower(trim((string) ($item['article_ref'] ?? ''))).'|'.mb_strtolower(trim((string) ($item['description'] ?? '')));
            $known = $catalog->get($lookup);
            $price = $known
                ? round((float) $known['purchase_price'], 2)
                : round((float) $item['unit_price'], 2);
            $remise = min(100, max(0, (float) ($item['remise'] ?? 0)));
            $quantity = round((float) $item['quantity'], 3);
            $sub = round($quantity * $price * (1 + $remise / 100), 2);
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

    private function clientId(string $name, string $code = ''): ?int
    {
        $code = trim($code);
        if ($code !== '' && preg_match('/^CC-0*(\d+)$/i', $code, $match)) {
            $id = (int) $match[1];
            if ($id > 0 && Client::query()->whereKey($id)->exists()) {
                return $id;
            }
        }

        $name = trim($name);
        if ($name === '') {
            return null;
        }

        return Client::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->value('id');
    }
}
