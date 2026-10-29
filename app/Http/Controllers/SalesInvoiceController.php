<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesInvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = SalesInvoice::query()
            ->with(['customer', 'items'])
            ->orderByDesc('id')
            ->get()
            ->map->toFicheArray()
            ->values();

        $next = (SalesInvoice::max('id') ?? 0) + 1;

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_ref' => 'FV-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'count' => $rows->count(),
                'total_ht' => round($rows->sum('total_ht'), 2),
                'total_ttc' => round($rows->sum('total_ttc'), 2),
                'clients' => Customer::query()->orderBy('name')->get(['id', 'name', 'ice']),
                'stock' => FiscalMovementController::movements()
                    ->filter(fn (array $row) => $row['achat'] > 0 && ($row['reference'] !== '' || $row['designation'] !== ''))
                    ->map(fn (array $row) => [
                        'key' => $row['key'],
                        'reference' => $row['reference'],
                        'designation' => $row['designation'],
                        'stock' => $row['stock'],
                        'purchase_price' => $this->purchasePrices()[$row['key']] ?? 0,
                    ])
                    ->values(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $invoice = DB::transaction(function () use ($data) {
            [$lines, $totals] = $this->buildLines($data);
            $customer = $this->resolveCustomer($data);
            $invoice = SalesInvoice::create([
                'customer_id' => $customer->id,
                'depot' => 'depot_a',
                'invoice_date' => $data['invoice_date'],
                'status' => 'en_attente',
                'total_ht' => $totals['total_ht'],
                'tva' => $totals['tva'],
                'total_ttc' => $totals['total_ttc'],
            ]);
            foreach ($lines as $line) {
                $line['sales_invoice_id'] = $invoice->id;
                SalesInvoiceItem::create($line);
            }

            return $invoice->load(['customer', 'items']);
        });

        return response()->json($invoice->toFicheArray(), 201);
    }

    public function update(Request $request, SalesInvoice $salesInvoice): JsonResponse
    {
        $data = $this->validated($request);

        $invoice = DB::transaction(function () use ($data, $salesInvoice) {
            [$lines, $totals] = $this->buildLines($data);
            $customer = $this->resolveCustomer($data);
            $salesInvoice->update([
                'customer_id' => $customer->id,
                'invoice_date' => $data['invoice_date'],
                'total_ht' => $totals['total_ht'],
                'tva' => $totals['tva'],
                'total_ttc' => $totals['total_ttc'],
            ]);
            $salesInvoice->items()->delete();
            foreach ($lines as $line) {
                $line['sales_invoice_id'] = $salesInvoice->id;
                SalesInvoiceItem::create($line);
            }

            return $salesInvoice->load(['customer', 'items']);
        });

        return response()->json($invoice->toFicheArray());
    }

    public function destroy(SalesInvoice $salesInvoice): JsonResponse
    {
        $salesInvoice->delete();

        return response()->json(['message' => 'Facture supprimée']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:customers,id'],
            'client_name' => ['nullable', 'string', 'max:255', 'required_without:client_id'],
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

        $stock = FiscalMovementController::movements()
            ->filter(fn (array $row) => $row['achat'] > 0)
            ->keyBy('key');

        foreach ($data['items'] as $index => $item) {
            $product = $stock->get(FiscalMovementController::lineKey($item['article_ref'] ?? '', $item['description'] ?? ''));
            if (! $product) {
                throw ValidationException::withMessages([
                    "items.$index.description" => 'Choisissez un produit du stock achats.',
                ]);
            }
            $data['items'][$index]['article_ref'] = $product['reference'] !== '' ? $product['reference'] : null;
            $data['items'][$index]['description'] = $product['designation'];
        }

        return $data;
    }

    private function resolveCustomer(array $data): Customer
    {
        if (! empty($data['client_id'])) {
            $customer = Customer::query()->findOrFail($data['client_id']);
            $customer->update(['ice' => $data['ice']]);

            return $customer;
        }

        $name = trim((string) ($data['client_name'] ?? ''));
        $customer = Customer::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($customer) {
            $customer->update(['ice' => $data['ice']]);

            return $customer;
        }

        return Customer::query()->create([
            'name' => $name,
            'ice' => $data['ice'],
            'status' => 'actif',
        ]);
    }

    private function buildLines(array $data): array
    {
        $lines = [];
        $base = 0;
        $prices = $this->purchasePrices();
        foreach ($data['items'] as $item) {
            $remise = min(100, max(0, (float) ($item['remise'] ?? 0)));
            $key = FiscalMovementController::lineKey($item['article_ref'] ?? '', $item['description'] ?? '');
            $purchase = round((float) ($prices[$key] ?? 0), 2);
            $sale = round($purchase * (1 - $remise / 100), 2);
            $line = round((float) $item['quantity'] * $sale, 2);
            $base += $line;
            $lines[] = [
                'article_ref' => $item['article_ref'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'purchase_price' => $purchase,
                'unit_price' => $sale,
                'remise' => $remise,
                'total' => $line,
            ];
        }

        $totalTtc = round($base, 2);
        $totalHt = round($totalTtc / 1.2, 2);
        $tva = round($totalTtc - $totalHt, 2);

        return [$lines, ['total_ht' => $totalHt, 'tva' => $tva, 'total_ttc' => $totalTtc]];
    }

    /**
     * @return array<string, float>
     */
    private function purchasePrices(): array
    {
        $prices = [];
        $lines = DB::table('supplier_invoice_items as i')
            ->join('supplier_invoices as f', 'f.id', '=', 'i.supplier_invoice_id')
            ->where('f.status', '!=', 'annulee')
            ->orderBy('f.invoice_date')
            ->orderBy('i.id')
            ->get(['i.article_ref', 'i.description', 'i.unit_price']);

        foreach ($lines as $line) {
            $prices[FiscalMovementController::lineKey($line->article_ref, $line->description)] = round((float) $line->unit_price, 2);
        }

        return $prices;
    }
}
