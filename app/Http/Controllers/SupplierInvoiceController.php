<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = SupplierInvoice::query()
            ->with(['supplier', 'items'])
            ->orderByDesc('id')
            ->get()
            ->map->toFicheArray()
            ->values();

        $next = (SupplierInvoice::max('id') ?? 0) + 1;

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_ref' => 'FA-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'count' => $rows->count(),
                'total_ht' => round($rows->sum('total_ht'), 2),
                'total_ttc' => round($rows->sum('total_ttc'), 2),
                'suppliers' => Supplier::query()->orderBy('name')->get(['id', 'name', 'ice']),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $invoice = DB::transaction(function () use ($data) {
            [$lines, $totals] = $this->buildLines($data);
            $supplier = $this->resolveSupplier($data);
            $invoice = SupplierInvoice::create([
                'supplier_id' => $supplier->id,
                'depot' => 'depot_a',
                'invoice_date' => $data['invoice_date'],
                'status' => 'en_attente',
                'total_ht' => $totals['total_ht'],
                'tva' => $totals['tva'],
                'total_ttc' => $totals['total_ttc'],
            ]);
            foreach ($lines as $line) {
                $line['supplier_invoice_id'] = $invoice->id;
                SupplierInvoiceItem::create($line);
            }

            return $invoice->load(['supplier', 'items']);
        });

        return response()->json($invoice->toFicheArray(), 201);
    }

    public function update(Request $request, SupplierInvoice $supplierInvoice): JsonResponse
    {
        $data = $this->validated($request);

        $invoice = DB::transaction(function () use ($data, $supplierInvoice) {
            [$lines, $totals] = $this->buildLines($data);
            $supplier = $this->resolveSupplier($data);
            $supplierInvoice->update([
                'supplier_id' => $supplier->id,
                'invoice_date' => $data['invoice_date'],
                'total_ht' => $totals['total_ht'],
                'tva' => $totals['tva'],
                'total_ttc' => $totals['total_ttc'],
            ]);
            $supplierInvoice->items()->delete();
            foreach ($lines as $line) {
                $line['supplier_invoice_id'] = $supplierInvoice->id;
                SupplierInvoiceItem::create($line);
            }

            return $supplierInvoice->load(['supplier', 'items']);
        });

        return response()->json($invoice->toFicheArray());
    }

    public function destroy(SupplierInvoice $supplierInvoice): JsonResponse
    {
        $supplierInvoice->delete();

        return response()->json(['message' => 'Facture supprimée']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:255', 'required_without:supplier_id'],
            'ice' => ['required', 'digits:15'],
            'invoice_date' => ['required', 'date'],
            'total_ttc' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_ref' => ['nullable', 'string', 'max:64'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.remise' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }

    private function resolveSupplier(array $data): Supplier
    {
        if (! empty($data['supplier_id'])) {
            $supplier = Supplier::query()->findOrFail($data['supplier_id']);
            $supplier->update(['ice' => $data['ice']]);

            return $supplier;
        }

        $name = trim((string) ($data['supplier_name'] ?? ''));
        $supplier = Supplier::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($supplier) {
            $supplier->update(['ice' => $data['ice']]);

            return $supplier;
        }

        return Supplier::query()->create([
            'name' => $name,
            'ice' => $data['ice'],
            'status' => 'actif',
        ]);
    }

    private function buildLines(array $data): array
    {
        $lines = [];
        $base = 0;
        foreach ($data['items'] as $item) {
            $remise = min(100, max(0, (float) ($item['remise'] ?? 0)));
            $line = round((float) $item['quantity'] * (float) $item['unit_price'] * (1 - $remise / 100), 2);
            $base += $line;
            $lines[] = [
                'article_ref' => $item['article_ref'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'remise' => $remise,
                'total' => $line,
            ];
        }

        if (isset($data['total_ttc']) && (float) $data['total_ttc'] > 0) {
            $totalTtc = round((float) $data['total_ttc'], 2);
            $totalHt = round($totalTtc / 1.2, 2);
            $tva = round($totalTtc - $totalHt, 2);
        } else {
            $totalHt = round($base, 2);
            $tva = round($totalHt * 0.20, 2);
            $totalTtc = round($totalHt + $tva, 2);
        }

        return [$lines, ['total_ht' => $totalHt, 'tva' => $tva, 'total_ttc' => $totalTtc]];
    }
}
