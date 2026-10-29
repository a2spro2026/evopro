<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoicePayment;
use App\Models\SalesInvoicePaymentLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesInvoicePaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $lines = SalesInvoicePaymentLine::query()
            ->with(['payment.invoice.customer'])
            ->orderBy('sales_invoice_payment_id')
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
                'client' => $invoice->customer?->name ?? '—',
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
        $invoices = SalesInvoice::query()
            ->with('customer')
            ->orderByDesc('id')
            ->get()
            ->map(function (SalesInvoice $invoice) use ($paidByInvoice) {
                $paid = round((float) ($paidByInvoice[$invoice->id] ?? 0), 2);
                $total = round((float) $invoice->total_ttc, 2);

                return [
                    'id' => $invoice->id,
                    'reference' => $invoice->reference,
                    'client' => $invoice->customer?->name ?? '—',
                    'total_ttc' => $total,
                    'paid' => $paid,
                    'balance' => round($total - $paid, 2),
                ];
            })
            ->values();

        $next = (SalesInvoicePayment::max('id') ?? 0) + 1;

        return response()->json([
            'data' => array_reverse($rows),
            'meta' => [
                'next_ref' => 'RG-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'date' => now()->format('Y-m-d'),
                'invoices' => $invoices,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sales_invoice_id' => ['required', 'integer', 'exists:sales_invoices,id'],
            'payment_date' => ['required', 'date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
            'lines.*.method' => ['required', 'in:Esp,Chq,Eff,Vir,Vers'],
            'lines.*.reference' => ['nullable', 'string', 'max:64'],
            'lines.*.bank' => ['nullable', 'string', 'max:64'],
            'lines.*.nom_tire' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = DB::transaction(function () use ($data) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($data['sales_invoice_id']);
            $this->assertWithinBalance($invoice, $data['lines']);
            $payment = SalesInvoicePayment::create([
                'sales_invoice_id' => $invoice->id,
                'payment_date' => $data['payment_date'],
            ]);
            $this->storeLines($payment, $data['lines']);
            $this->refreshStatus($invoice);

            return $payment;
        });

        return response()->json(['id' => $payment->id, 'reference' => $payment->reference], 201);
    }

    public function update(Request $request, SalesInvoicePayment $salesInvoicePayment): JsonResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $salesInvoicePayment) {
            $invoice = SalesInvoice::query()->lockForUpdate()->findOrFail($salesInvoicePayment->sales_invoice_id);
            if ((int) $data['sales_invoice_id'] !== (int) $invoice->id) {
                throw ValidationException::withMessages([
                    'sales_invoice_id' => 'La facture de ce règlement ne peut pas changer.',
                ]);
            }
            $this->assertWithinBalance($invoice, $data['lines'], $salesInvoicePayment->id);
            $salesInvoicePayment->update(['payment_date' => $data['payment_date']]);
            $salesInvoicePayment->lines()->delete();
            $this->storeLines($salesInvoicePayment, $data['lines']);
            $this->refreshStatus($invoice);
        });

        return response()->json(['id' => $salesInvoicePayment->id, 'reference' => $salesInvoicePayment->reference]);
    }

    public function destroy(SalesInvoicePayment $salesInvoicePayment): JsonResponse
    {
        $invoice = $salesInvoicePayment->invoice;
        $salesInvoicePayment->delete();
        if ($invoice) {
            $this->refreshStatus($invoice);
        }

        return response()->json(['message' => 'Règlement supprimé']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'sales_invoice_id' => ['required', 'integer', 'exists:sales_invoices,id'],
            'payment_date' => ['required', 'date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
            'lines.*.method' => ['required', 'in:Esp,Chq,Eff,Vir,Vers'],
            'lines.*.reference' => ['nullable', 'string', 'max:64'],
            'lines.*.bank' => ['nullable', 'string', 'max:64'],
            'lines.*.nom_tire' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function assertWithinBalance(SalesInvoice $invoice, array $lines, ?int $exceptPaymentId = null): void
    {
        $paymentIds = $invoice->payments()
            ->when($exceptPaymentId, fn ($query) => $query->where('id', '!=', $exceptPaymentId))
            ->pluck('id');
        $already = (float) SalesInvoicePaymentLine::query()
            ->whereIn('sales_invoice_payment_id', $paymentIds)
            ->sum('amount');
        $incoming = round(collect($lines)->sum(fn ($line) => (float) $line['amount']), 2);
        $remaining = round((float) $invoice->total_ttc - $already, 2);
        if ($incoming - $remaining > 0.009) {
            throw ValidationException::withMessages([
                'lines' => 'Le montant réglé dépasse le solde de la facture.',
            ]);
        }
    }

    private function storeLines(SalesInvoicePayment $payment, array $lines): void
    {
        foreach ($lines as $line) {
            $cash = $line['method'] === 'Esp';
            SalesInvoicePaymentLine::create([
                'sales_invoice_payment_id' => $payment->id,
                'amount' => $line['amount'],
                'method' => $line['method'],
                'reference' => $cash ? null : ($line['reference'] ?? null),
                'bank' => $cash ? null : ($line['bank'] ?? null),
                'nom_tire' => $cash ? null : ($line['nom_tire'] ?? null),
            ]);
        }
    }

    private function refreshStatus(SalesInvoice $invoice): void
    {
        $paid = (float) SalesInvoicePaymentLine::query()
            ->whereIn('sales_invoice_payment_id', $invoice->payments()->pluck('id'))
            ->sum('amount');
        $invoice->update([
            'status' => $paid <= 0 ? 'en_attente' : ($paid + 0.009 >= (float) $invoice->total_ttc ? 'payee' : 'partielle'),
        ]);
    }
}
