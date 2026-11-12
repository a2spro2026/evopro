<?php

namespace App\Http\Controllers;

use App\Models\StockProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockMovementController extends Controller
{
    public static function normalizeToken(mixed $value): string
    {
        $text = mb_strtolower(trim((string) $value));
        if (class_exists(\Normalizer::class)) {
            $decomposed = \Normalizer::normalize($text, \Normalizer::FORM_D);
            if (is_string($decomposed) && $decomposed !== '') {
                $text = $decomposed;
            }
            $text = preg_replace('/\p{Mn}+/u', '', $text) ?? $text;
        }
        $text = preg_replace('/[^a-z0-9]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? $text;

        return $text;
    }

    public static function lineKey(mixed $reference, mixed $description): string
    {
        $ref = self::normalizeToken($reference);
        $ref = preg_replace('/[\s\-_]+/u', '', $ref) ?? $ref;
        if ($ref !== '') {
            return 'ref:'.$ref;
        }

        return 'desc:'.self::normalizeToken($description);
    }

    public static function resolveRowKey(array &$rows, array &$byDesc, mixed $reference, mixed $description): string
    {
        $refNorm = preg_replace('/[\s\-_]+/u', '', self::normalizeToken($reference)) ?? '';
        $refKey = $refNorm !== '' ? 'ref:'.$refNorm : null;
        $descNorm = self::normalizeToken($description);
        $descKey = $descNorm !== '' ? 'desc:'.$descNorm : null;

        if ($refKey && isset($rows[$refKey])) {
            if ($descKey) {
                $byDesc[$descKey] = $refKey;
            }

            return $refKey;
        }

        if ($descKey && isset($byDesc[$descKey], $rows[$byDesc[$descKey]])) {
            $existing = $byDesc[$descKey];
            if ($refKey && $existing !== $refKey && ! isset($rows[$refKey])) {
                $rows[$refKey] = $rows[$existing];
                $rows[$refKey]['key'] = $refKey;
                unset($rows[$existing]);
                $byDesc[$descKey] = $refKey;

                return $refKey;
            }

            return $existing;
        }

        $key = $refKey ?? $descKey ?? 'desc:';
        if ($descKey) {
            $byDesc[$descKey] = $key;
        }

        return $key;
    }

    public static function products()
    {
        if (! Schema::hasTable('purchase_order_items') && ! Schema::hasTable('supplier_invoice_items')) {
            return collect();
        }

        $rows = [];
        $byDesc = [];
        foreach (self::purchaseLines() as $line) {
            $key = self::resolveRowKey($rows, $byDesc, $line->article_ref, $line->description);
            if (! isset($rows[$key])) {
                $rows[$key] = self::blankRow($line);
                $rows[$key]['key'] = $key;
            } else {
                $ref = trim((string) ($line->article_ref ?? ''));
                if ($ref !== '' && trim((string) $rows[$key]['reference']) === '') {
                    $rows[$key]['reference'] = $ref;
                }
                if (trim((string) $rows[$key]['designation']) === '') {
                    $rows[$key]['designation'] = trim((string) $line->description);
                }
            }
            $rows[$key]['achat'] += (float) $line->quantity;
            self::addMonth($rows[$key], $line->sort_date ?? null, 'achat', (float) $line->quantity);
            $rows[$key]['purchase_price'] = round((float) $line->unit_price, 2);
            foreach (['barcode', 'equiv', 'brand', 'family'] as $field) {
                $value = trim((string) ($line->{$field} ?? ''));
                if ($value !== '') {
                    $rows[$key][$field] = $value;
                }
            }
        }

        if (Schema::hasTable('sales_invoice_items')) {
            $sales = DB::table('sales_invoice_items as i')
                ->join('sales_invoices as f', 'f.id', '=', 'i.sales_invoice_id')
                ->where('f.status', '!=', 'annulee')
                ->get(['i.article_ref', 'i.description', 'i.quantity', 'f.invoice_date']);

            foreach ($sales as $line) {
                $key = self::resolveRowKey($rows, $byDesc, $line->article_ref, $line->description);
                if (! isset($rows[$key])) {
                    continue;
                }
                $rows[$key]['vente'] += (float) $line->quantity;
                self::addMonth($rows[$key], $line->invoice_date ?? null, 'vente', (float) $line->quantity);
            }
        }

        if (Schema::hasTable('delivery_order_items')) {
            $deliveries = DB::table('delivery_order_items as i')
                ->join('delivery_orders as o', 'o.id', '=', 'i.delivery_order_id')
                ->orderBy('o.order_date')
                ->orderBy('i.id')
                ->get(['i.article_ref', 'i.description', 'i.quantity', 'o.order_date']);

            foreach ($deliveries as $line) {
                $key = self::resolveRowKey($rows, $byDesc, $line->article_ref, $line->description);
                if (! isset($rows[$key])) {
                    continue;
                }
                $rows[$key]['vente'] += (float) $line->quantity;
                self::addMonth($rows[$key], $line->order_date ?? null, 'vente', (float) $line->quantity);
            }
        }

        return collect($rows)
            ->map(function (array $row) {
                $row['achat'] = round($row['achat'], 3);
                $row['vente'] = round($row['vente'], 3);
                $row['stock_initial'] = 0;
                $row['stock'] = round($row['achat'] - $row['vente'], 3);
                $row['months'] = self::roundMonths($row['months']);
                $row['years'] = collect($row['years'])
                    ->map(fn (array $months) => self::roundMonths($months))
                    ->all();

                return $row;
            })
            ->sortBy([
                ['reference', 'asc'],
                ['designation', 'asc'],
            ])
            ->values()
            ->map(function (array $row, int $index) {
                $row['id'] = $index + 1;
                $row['code'] = 'PRD-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

                return $row;
            })
            ->values();
    }

    public function sheet(): JsonResponse
    {
        $data = self::products()->map(fn (array $row) => [
            'code' => $row['code'],
            'reference' => $row['reference'],
            'barcode' => $row['barcode'],
            'designation' => $row['designation'],
            'equiv' => $row['equiv'] ?? '',
            'family' => $row['family'] ?? '',
            'brand' => $row['brand'] ?? '',
            'months' => collect($row['months'] ?? [])
                ->filter(fn (array $month) => (float) ($month['achat'] ?? 0) > 0)
                ->keys()
                ->map(fn ($month) => (int) $month)
                ->values(),
            'quantity' => $row['achat'],
            'stock' => $row['stock'],
            'purchase_price' => $row['purchase_price'],
            'unit' => 'U',
            'unit_price' => $row['purchase_price'],
            'status' => 'Actif',
            'state' => $row['stock'] > 0 ? 'Disponible' : 'Rupture',
        ])->values();

        return response()->json(['data' => $data]);
    }

    private static function purchaseLines()
    {
        $lines = collect();

        if (Schema::hasTable('purchase_order_items')) {
            $orders = DB::table('purchase_order_items as i')
                ->join('purchase_orders as o', 'o.id', '=', 'i.purchase_order_id')
                ->get(['i.id', 'i.article_ref', 'i.barcode', 'i.equiv', 'i.description', 'i.brand', 'i.family', 'i.quantity', 'i.unit_price', 'o.order_date']);
            foreach ($orders as $line) {
                $line->sort_date = $line->order_date;
                $lines->push($line);
            }
        }

        if (Schema::hasTable('supplier_invoice_items')) {
            $invoices = DB::table('supplier_invoice_items as i')
                ->join('supplier_invoices as f', 'f.id', '=', 'i.supplier_invoice_id')
                ->where('f.status', '!=', 'annulee')
                ->get(['i.id', 'i.article_ref', 'i.description', 'i.quantity', 'i.unit_price', 'f.invoice_date']);
            foreach ($invoices as $line) {
                $line->sort_date = $line->invoice_date;
                $line->barcode = '';
                $line->equiv = '';
                $line->brand = '';
                $line->family = '';
                $lines->push($line);
            }
        }

        return $lines->sortBy([
            ['sort_date', 'asc'],
            ['id', 'asc'],
        ])->values();
    }

    private static function blankMonths(): array
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['achat' => 0.0, 'vente' => 0.0];
        }

        return $months;
    }

    private static function blankRow(object $line): array
    {
        return [
            'key' => self::lineKey($line->article_ref, $line->description),
            'reference' => trim((string) $line->article_ref),
            'barcode' => trim((string) ($line->barcode ?? '')),
            'equiv' => trim((string) ($line->equiv ?? '')),
            'designation' => trim((string) $line->description),
            'brand' => trim((string) ($line->brand ?? '')),
            'family' => trim((string) ($line->family ?? '')),
            'achat' => 0.0,
            'vente' => 0.0,
            'purchase_price' => 0.0,
            'months' => self::blankMonths(),
            'years' => [],
        ];
    }

    private static function addMonth(array &$row, mixed $date, string $field, float $quantity): void
    {
        $stamp = strtotime((string) $date);
        if ($stamp === false) {
            return;
        }
        $month = (int) date('n', $stamp);
        $year = (string) date('Y', $stamp);
        if ($month < 1 || $month > 12) {
            return;
        }
        $row['months'][$month][$field] += $quantity;
        $row['years'][$year] ??= self::blankMonths();
        $row['years'][$year][$month][$field] += $quantity;
    }

    private static function roundMonths(array $months): array
    {
        foreach ($months as $month => $values) {
            $months[$month] = [
                'achat' => round((float) ($values['achat'] ?? 0), 3),
                'vente' => round((float) ($values['vente'] ?? 0), 3),
            ];
        }

        return $months;
    }

    public function index(): JsonResponse
    {
        $data = self::products();

        $years = $data
            ->flatMap(fn (array $row) => array_keys($row['years'] ?? []))
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->sort()
            ->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'count' => $data->count(),
                'stock' => round((float) $data->sum('stock'), 3),
                'years' => $years,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $product = StockProduct::create($this->validated($request));

        return response()->json(self::products()->firstWhere('id', $product->id), 201);
    }

    public function update(Request $request, StockProduct $stockProduct): JsonResponse
    {
        $stockProduct->update($this->validated($request));

        return response()->json(self::products()->firstWhere('id', $stockProduct->id));
    }

    public function destroy(StockProduct $stockProduct): JsonResponse
    {
        $stockProduct->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'reference' => ['nullable', 'string', 'max:64'],
            'designation' => ['required', 'string', 'max:255'],
            'stock_initial' => ['nullable', 'numeric'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        return [
            'reference' => trim((string) ($data['reference'] ?? '')) ?: null,
            'designation' => trim($data['designation']),
            'stock_initial' => round((float) ($data['stock_initial'] ?? 0), 3),
            'purchase_price' => round((float) ($data['purchase_price'] ?? 0), 2),
        ];
    }
}
