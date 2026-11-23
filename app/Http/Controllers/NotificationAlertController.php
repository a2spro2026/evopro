<?php

namespace App\Http\Controllers;

use App\Models\ClientPayment;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class NotificationAlertController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today();
        $limit = $today->copy()->addDays(2);
        $alerts = collect();

        $bankMethods = ['Chq', 'Eff', 'Vir', 'Vers'];

        if (Schema::hasTable('supplier_payments')) {
            SupplierPayment::query()
                ->with('supplier')
                ->whereNotNull('date_decaissement')
                ->whereDate('date_decaissement', '<=', $limit->toDateString())
                ->whereIn('reglement', $bankMethods)
                ->orderBy('date_decaissement')
                ->orderBy('id')
                ->get()
                ->each(function (SupplierPayment $payment) use ($alerts, $today) {
                    $date = $payment->date_decaissement;
                    if (! $date) {
                        return;
                    }
                    $alerts->push([
                        'id' => 'ra-'.$payment->id,
                        'kind' => 'reglement_fournisseur',
                        'severity' => $date->lt($today) ? 'urgent' : 'warning',
                        'title' => 'Rég. fournisseur à la banque',
                        'message' => trim(($payment->reference ?: '—').' · '.($payment->supplier?->name ?: 'Fournisseur')),
                        'detail' => 'Décaissement '.$date->format('d/m/Y').' · '.($payment->reglement ?: '—'),
                        'date' => $date->format('Y-m-d'),
                        'date_fr' => $date->format('d/m/Y'),
                        'panel' => 'f-regl',
                    ]);
                });
        }

        if (Schema::hasTable('client_payments')) {
            ClientPayment::query()
                ->with('client')
                ->whereNotNull('date_decaissement')
                ->whereDate('date_decaissement', '<=', $limit->toDateString())
                ->whereIn('reglement', $bankMethods)
                ->orderBy('date_decaissement')
                ->orderBy('id')
                ->get()
                ->each(function (ClientPayment $payment) use ($alerts, $today) {
                    $date = $payment->date_decaissement;
                    if (! $date) {
                        return;
                    }
                    $alerts->push([
                        'id' => 'rc-'.$payment->id,
                        'kind' => 'reglement_client',
                        'severity' => $date->lt($today) ? 'urgent' : 'warning',
                        'title' => 'Rég. client à la banque',
                        'message' => trim(($payment->reference ?: '—').' · '.($payment->client?->name ?: 'Client')),
                        'detail' => 'Décaissement '.$date->format('d/m/Y').' · '.($payment->reglement ?: '—'),
                        'date' => $date->format('Y-m-d'),
                        'date_fr' => $date->format('d/m/Y'),
                        'panel' => 'c-regl',
                    ]);
                });
        }

        $ruptures = StockMovementController::products()
            ->filter(fn (array $row) => (float) ($row['stock'] ?? 0) <= 0)
            ->take(40)
            ->values();

        foreach ($ruptures as $row) {
            $ref = trim((string) ($row['reference'] ?? ''));
            $designation = trim((string) ($row['designation'] ?? ''));
            $alerts->push([
                'id' => 'rupture-'.md5(($row['key'] ?? '').'|'.$ref.'|'.$designation),
                'kind' => 'rupture',
                'severity' => 'warning',
                'title' => 'Article en rupture',
                'message' => $ref !== '' ? $ref.($designation !== '' ? ' · '.$designation : '') : ($designation !== '' ? $designation : 'Article'),
                'detail' => 'Stock '.number_format((float) ($row['stock'] ?? 0), 0, ',', ' '),
                'date' => null,
                'date_fr' => null,
                'panel' => 's-prod',
            ]);
        }

        $sorted = $alerts
            ->sortBy(function (array $alert) {
                $prio = $alert['severity'] === 'urgent' ? '0' : '1';
                $kind = match ($alert['kind']) {
                    'reglement_fournisseur', 'reglement_client' => '0',
                    default => '1',
                };
                $date = $alert['date'] ?? '9999-99-99';

                return $prio.'-'.$kind.'-'.$date.'-'.$alert['title'];
            })
            ->values();

        return response()->json([
            'count' => $sorted->count(),
            'data' => $sorted,
        ]);
    }
}
