<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvoice;
use App\Models\SupplierInvoicePayment;
use App\Models\SupplierInvoicePaymentLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierInvoicePaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $lines = SupplierInvoicePaymentLine::query()
            ->with(['payment.invoice.supplier'])
            ->orderBy('supplier_invoice_payment_id')
            ->orderBy('id')
            ->get();

        $running = [];
        $rows = [];
        foreach ($lines as $line) {
            $payment = $line->payment;
            $invoice = $payment?->invoice;
            if (! $payment || ! $invoice) {
                continue;
            }
            $invoiceId = $invoice->id;
            $running[$invoiceId] = round(($running[$invoiceId] ?? 0) + (float) $line->amount, 2);
            $rows[] = [
                'id' => $payment->id,
                'line_id' => $line->id,
                'payment_date' => $payment->payment_date?->format('d/m/Y'),
                'payment_date_raw' => $payment->payment_date?->format('Y-m-d'),
                'reference' => $payment->reference,
                'invoice_id' => $invoice->id,
                'invoice_reference' => $invoice->reference,
                'fournisseur' => $invoice->supplier?->name ?? '—',
                'total_ttc' => round((float) $invoice->total_ttc, 2),
                'method' => $line->method,
                'line_reference' => $line->reference,
                'bank' => $line->bank,
                'nom_tire' => $line->nom_tire,
                'amount' => round((float) $line->amount, 2),
                'balance' => round((float) $invoice->total_ttc - $running[$invoiceId], 2),
            ];
        }

        $paidByInvoice = $running;
        $invoices = SupplierInvoice::query()
            ->with('supplier')
            ->orderByDesc('id')
            ->get()
            ->map(function (SupplierInvoice $invoice) use ($paidByInvoice) {
                $paid = round((float) ($paidByInvoice[$invoice->id] ?? 0), 2);
                $total = round((float) $invoice->total_ttc, 2);

                return [
                    'id' => $invoice->id,
                    'reference' => $invoice->reference,
                    'fournisseur' => $invoice->supplier?->name ?? '—',
                    'total_ttc' => $total,
                    'paid' => $paid,
                    'balance' => round($total - $paid, 2),
                ];
            })
            ->values();

        $next = (SupplierInvoicePayment::max('id') ?? 0) + 1;

        return response()->json([
            'data' => array_reverse($rows),
            'meta' => [
                'next_ref' => 'RA-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'invoices' => $invoices,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $payment = DB::transaction(function () use ($data) {
            $invoice = SupplierInvoice::query()->lockForUpdate()->findOrFail($data['supplier_invoice_id']);
            $this->assertWithinBalance($invoice, $data['lines']);
            $payment = SupplierInvoicePayment::create([
                'supplier_invoice_id' => $invoice->id,
                'payment_date' => $data['payment_date'],
            ]);
            $this->storeLines($payment, $data['lines']);
            $this->refreshStatus($invoice);

            return $payment;
        });

        return response()->json(['id' => $payment->id, 'reference' => $payment->reference], 201);
    }

    public function update(Request $request, SupplierInvoicePayment $supplierInvoicePayment): JsonResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $supplierInvoicePayment) {
            $invoice = SupplierInvoice::query()->lockForUpdate()->findOrFail($supplierInvoicePayment->supplier_invoice_id);
            if ((int) $data['supplier_invoice_id'] !== (int) $invoice->id) {
                throw ValidationException::withMessages([
                    'supplier_invoice_id' => 'La facture de ce règlement ne peut pas changer.',
                ]);
            }
            $this->assertWithinBalance($invoice, $data['lines'], $supplierInvoicePayment->id);
            $supplierInvoicePayment->update(['payment_date' => $data['payment_date']]);
            $supplierInvoicePayment->lines()->delete();
            $this->storeLines($supplierInvoicePayment, $data['lines']);
            $this->refreshStatus($invoice);
        });

        return response()->json(['id' => $supplierInvoicePayment->id, 'reference' => $supplierInvoicePayment->reference]);
    }

    public function destroy(SupplierInvoicePayment $supplierInvoicePayment): JsonResponse
    {
        $invoice = $supplierInvoicePayment->invoice;
        $supplierInvoicePayment->delete();
        if ($invoice) {
            $this->refreshStatus($invoice);
        }

        return response()->json(['message' => 'Règlement supprimé']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_invoice_id' => ['required', 'integer', 'exists:supplier_invoices,id'],
            'payment_date' => ['required', 'date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
            'lines.*.method' => ['required', 'in:Esp,Chq,Eff,Vir,Vers'],
            'lines.*.reference' => ['nullable', 'string', 'max:64'],
            'lines.*.bank' => ['nullable', 'string', 'max:64'],
            'lines.*.nom_tire' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function assertWithinBalance(SupplierInvoice $invoice, array $lines, ?int $exceptPaymentId = null): void
    {
        $paymentIds = $invoice->payments()
            ->when($exceptPaymentId, fn ($query) => $query->where('id', '!=', $exceptPaymentId))
            ->pluck('id');
        $already = (float) SupplierInvoicePaymentLine::query()
            ->whereIn('supplier_invoice_payment_id', $paymentIds)
            ->sum('amount');
        $incoming = round(collect($lines)->sum(fn ($line) => (float) $line['amount']), 2);
        $remaining = round((float) $invoice->total_ttc - $already, 2);
        if ($incoming - $remaining > 0.009) {
            throw ValidationException::withMessages([
                'lines' => 'Le montant réglé dépasse le solde de la facture.',
            ]);
        }
    }

    private function storeLines(SupplierInvoicePayment $payment, array $lines): void
    {
        foreach ($lines as $line) {
            $cash = $line['method'] === 'Esp';
            SupplierInvoicePaymentLine::create([
                'supplier_invoice_payment_id' => $payment->id,
                'amount' => $line['amount'],
                'method' => $line['method'],
                'reference' => $cash ? null : ($line['reference'] ?? null),
                'bank' => $cash ? null : ($line['bank'] ?? null),
                'nom_tire' => $cash ? null : ($line['nom_tire'] ?? null),
            ]);
        }
    }

    private function refreshStatus(SupplierInvoice $invoice): void
    {
        $paid = (float) SupplierInvoicePaymentLine::query()
            ->whereIn('supplier_invoice_payment_id', $invoice->payments()->pluck('id'))
            ->sum('amount');
        $invoice->update([
            'status' => $paid <= 0 ? 'en_attente' : ($paid + 0.009 >= (float) $invoice->total_ttc ? 'payee' : 'partielle'),
        ]);
    }
}
