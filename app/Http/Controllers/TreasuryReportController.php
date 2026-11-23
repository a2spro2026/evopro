<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\ClientPayment;
use App\Models\DeliveryOrder;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\SalesInvoicePayment;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoicePayment;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TreasuryReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $month = $request->query('month');
        $month = ($month !== null && $month !== '') ? (int) $month : null;

        $rows = collect();

        if (Schema::hasTable('purchase_orders')) {
            PurchaseOrder::query()
                ->orderByDesc('order_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (PurchaseOrder $order) use ($rows) {
                    $date = $order->order_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'BA '.$order->reference,
                        type: 'ba',
                        debit: (float) $order->total,
                        ref: $order->reference,
                    ));
                });
        }

        if (Schema::hasTable('delivery_orders')) {
            DeliveryOrder::query()
                ->orderByDesc('order_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (DeliveryOrder $order) use ($rows) {
                    $date = $order->order_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'BL '.$order->reference,
                        type: 'bl',
                        credit: (float) $order->total,
                        ref: $order->reference,
                    ));
                });
        }

        if (Schema::hasTable('supplier_payments')) {
            SupplierPayment::query()
                ->where('reglement', 'Esp')
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (SupplierPayment $payment) use ($rows) {
                    $date = $payment->payment_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'Esp Achats '.$payment->reference,
                        type: 'esp_ba',
                        caisse: (float) $payment->montant,
                        ref: $payment->reference,
                    ));
                });
        }

        if (Schema::hasTable('client_payments')) {
            ClientPayment::query()
                ->where('reglement', 'Esp')
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (ClientPayment $payment) use ($rows) {
                    $date = $payment->payment_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'Esp Clients '.$payment->reference,
                        type: 'esp_bl',
                        caisse: (float) $payment->montant,
                        ref: $payment->reference,
                    ));
                });
        }

        if (Schema::hasTable('supplier_invoices')) {
            SupplierInvoice::query()
                ->orderByDesc('invoice_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (SupplierInvoice $invoice) use ($rows) {
                    $date = $invoice->invoice_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'FA '.$invoice->reference,
                        type: 'fa',
                        debit_facture: (float) $invoice->total_ttc,
                        ref: $invoice->reference,
                    ));
                });
        }

        if (Schema::hasTable('sales_invoices')) {
            SalesInvoice::query()
                ->orderByDesc('invoice_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (SalesInvoice $invoice) use ($rows) {
                    $date = $invoice->invoice_date;
                    if (! $date) {
                        return;
                    }
                    $rows->push($this->row(
                        date: $date,
                        operation: 'FV '.$invoice->reference,
                        type: 'fv',
                        credit_facture: (float) $invoice->total_ttc,
                        ref: $invoice->reference,
                    ));
                });
        }

        if (Schema::hasTable('supplier_invoice_payments') && Schema::hasTable('supplier_invoice_payment_lines')) {
            SupplierInvoicePayment::query()
                ->with(['lines', 'invoice'])
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (SupplierInvoicePayment $payment) use ($rows) {
                    $date = $payment->payment_date;
                    if (! $date) {
                        return;
                    }
                    $esp = (float) $payment->lines
                        ->where('method', 'Esp')
                        ->sum('amount');
                    if ($esp <= 0) {
                        return;
                    }
                    $invoiceRef = $payment->invoice?->reference ?: $payment->reference;
                    $rows->push($this->row(
                        date: $date,
                        operation: 'Esp Fact Achat '.$invoiceRef,
                        type: 'esp_fa',
                        esp_facture: $esp,
                        ref: $payment->reference,
                    ));
                });
        }

        if (Schema::hasTable('sales_invoice_payments') && Schema::hasTable('sales_invoice_payment_lines')) {
            SalesInvoicePayment::query()
                ->with(['lines', 'invoice'])
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (SalesInvoicePayment $payment) use ($rows) {
                    $date = $payment->payment_date;
                    if (! $date) {
                        return;
                    }
                    $esp = (float) $payment->lines
                        ->where('method', 'Esp')
                        ->sum('amount');
                    if ($esp <= 0) {
                        return;
                    }
                    $invoiceRef = $payment->invoice?->reference ?: $payment->reference;
                    $rows->push($this->row(
                        date: $date,
                        operation: 'Esp Fact Vente '.$invoiceRef,
                        type: 'esp_fv',
                        esp_facture: $esp,
                        ref: $payment->reference,
                    ));
                });
        }

        if (Schema::hasTable('charges')) {
            Charge::query()
                ->with('category')
                ->orderByDesc('charge_date')
                ->orderByDesc('id')
                ->get()
                ->each(function (Charge $charge) use ($rows) {
                    $date = $charge->charge_date;
                    if (! $date) {
                        return;
                    }
                    $label = $charge->category?->name
                        ?: ($charge->designation ?: $charge->reference);
                    $rows->push($this->row(
                        date: $date,
                        operation: 'CH '.$charge->reference.($label ? ' — '.$label : ''),
                        type: 'charge',
                        charge: (float) $charge->montant,
                        ref: $charge->reference,
                    ));
                });
        }

        $filtered = $rows
            ->filter(function (array $row) use ($month) {
                if ($month && (int) $row['month'] !== $month) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(fn (array $row) => $row['date'].'-'.$row['id'])
            ->values()
            ->map(function (array $row, int $index) {
                $row['id'] = $index + 1;

                return $row;
            });

        $debit = round((float) $filtered->sum('debit'), 2);
        $credit = round((float) $filtered->sum('credit'), 2);
        $caisse = round((float) $filtered->sum('caisse'), 2);
        $charge = round((float) $filtered->sum('charge'), 2);
        $debitFacture = round((float) $filtered->sum('debit_facture'), 2);
        $creditFacture = round((float) $filtered->sum('credit_facture'), 2);
        $espFacture = round((float) $filtered->sum('esp_facture'), 2);
        $solde = round($debit - ($credit + $caisse), 2);

        return response()->json([
            'data' => $filtered,
            'meta' => [
                'debit' => $debit,
                'credit' => $credit,
                'caisse' => $caisse,
                'charge' => $charge,
                'solde' => $solde,
                'debit_facture' => $debitFacture,
                'credit_facture' => $creditFacture,
                'esp_facture' => $espFacture,
            ],
        ]);
    }

    private function row(
        Carbon $date,
        string $operation,
        string $type,
        float $debit = 0,
        float $credit = 0,
        float $caisse = 0,
        float $charge = 0,
        float $debit_facture = 0,
        float $credit_facture = 0,
        float $esp_facture = 0,
        string $ref = '',
    ): array {
        return [
            'id' => $type.'-'.$date->format('Ymd').'-'.$ref,
            'date' => $date->format('Y-m-d'),
            'date_fr' => $date->format('d/m/Y'),
            'month' => (int) $date->format('n'),
            'operation' => $operation,
            'type' => $type,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'caisse' => round($caisse, 2),
            'charge' => round($charge, 2),
            'debit_facture' => round($debit_facture, 2),
            'credit_facture' => round($credit_facture, 2),
            'esp_facture' => round($esp_facture, 2),
            'ref' => $ref,
        ];
    }
}
