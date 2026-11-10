<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FiscalMovementController extends Controller
{
    public static function movements()
    {
        $rows = [];

        $purchases = DB::table('supplier_invoice_items as i')
            ->join('supplier_invoices as f', 'f.id', '=', 'i.supplier_invoice_id')
            ->where('f.status', '!=', 'annulee')
            ->get(['i.article_ref', 'i.description', 'i.quantity', 'f.invoice_date']);

        foreach ($purchases as $line) {
            $key = self::lineKey($line->article_ref, $line->description);
            $rows[$key] ??= self::blankLine($line);
            $rows[$key]['achat'] += (float) $line->quantity;
            self::touchMonth($rows[$key], $line->invoice_date ?? null);
        }

        if (Schema::hasTable('sales_invoices') && Schema::hasTable('sales_invoice_items')) {
            $sales = DB::table('sales_invoice_items as i')
                ->join('sales_invoices as f', 'f.id', '=', 'i.sales_invoice_id')
                ->where('f.status', '!=', 'annulee')
                ->get(['i.article_ref', 'i.description', 'i.quantity', 'f.invoice_date']);

            foreach ($sales as $line) {
                $key = self::lineKey($line->article_ref, $line->description);
                $rows[$key] ??= self::blankLine($line);
                $rows[$key]['vente'] += (float) $line->quantity;
                self::touchMonth($rows[$key], $line->invoice_date ?? null);
            }
        }

        $metaByKey = self::metaByKey();

        return collect($rows)
            ->map(function (array $row, string $key) use ($metaByKey) {
                $meta = $metaByKey[$key] ?? ['equiv' => '', 'brand' => '', 'family' => ''];
                $row['key'] = $key;
                $row['achat'] = round($row['achat'], 3);
                $row['vente'] = round($row['vente'], 3);
                $row['stock'] = round($row['achat'] - $row['vente'], 3);
                $row['equiv'] = $meta['equiv'];
                $row['brand'] = $meta['brand'];
                $row['family'] = $meta['family'];
                $row['months'] = collect(array_keys($row['months']))
                    ->map(fn ($month) => (int) $month)
                    ->sort()
                    ->values();

                return $row;
            })
            ->sortBy([
                ['reference', 'asc'],
                ['designation', 'asc'],
            ])
            ->values();
    }

    public static function lineKey(mixed $reference, mixed $description): string
    {
        $ref = mb_strtolower(trim((string) $reference));
        if ($ref !== '') {
            return 'ref:'.$ref;
        }

        return 'desc:'.mb_strtolower(trim((string) $description));
    }

    public function index(): JsonResponse
    {
        $data = self::movements();

        return response()->json([
            'data' => $data,
            'meta' => [
                'count' => $data->count(),
                'achat' => round((float) $data->sum('achat'), 3),
                'vente' => round((float) $data->sum('vente'), 3),
                'stock' => round((float) $data->sum('stock'), 3),
            ],
        ]);
    }

    private static function blankLine(object $line): array
    {
        return [
            'reference' => trim((string) $line->article_ref),
            'designation' => trim((string) $line->description),
            'equiv' => '',
            'brand' => '',
            'family' => '',
            'months' => [],
            'achat' => 0.0,
            'vente' => 0.0,
            'stock' => 0.0,
        ];
    }

    private static function touchMonth(array &$row, mixed $date): void
    {
        $stamp = strtotime((string) $date);
        if ($stamp === false) {
            return;
        }
        $month = (int) date('n', $stamp);
        if ($month >= 1 && $month <= 12) {
            $row['months'][$month] = true;
        }
    }

    private static function metaByKey(): array
    {
        if (! Schema::hasTable('purchase_order_items')) {
            return [];
        }

        $meta = [];
        $columns = ['article_ref', 'description', 'equiv'];
        if (Schema::hasColumn('purchase_order_items', 'brand')) {
            $columns[] = 'brand';
        }
        if (Schema::hasColumn('purchase_order_items', 'family')) {
            $columns[] = 'family';
        }
        $lines = DB::table('purchase_order_items')->get($columns);
        foreach ($lines as $line) {
            $key = self::lineKey($line->article_ref, $line->description);
            $meta[$key] ??= ['equiv' => '', 'brand' => '', 'family' => ''];
            $equiv = trim((string) ($line->equiv ?? ''));
            $brand = trim((string) ($line->brand ?? ''));
            $family = trim((string) ($line->family ?? ''));
            if ($equiv !== '') {
                $meta[$key]['equiv'] = $equiv;
            }
            if ($brand !== '') {
                $meta[$key]['brand'] = $brand;
            }
            if ($family !== '') {
                $meta[$key]['family'] = $family;
            }
        }

        return $meta;
    }
}
