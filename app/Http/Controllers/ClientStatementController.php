<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\DeliveryOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientStatementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
        ]);

        $clientIdsWithOrders = DeliveryOrder::query()
            ->whereNotNull('client_id')
            ->distinct()
            ->pluck('client_id')
            ->filter()
            ->values()
            ->all();

        $clientIdsWithPayments = ClientPayment::query()
            ->whereNotNull('client_id')
            ->distinct()
            ->pluck('client_id')
            ->filter()
            ->values()
            ->all();

        $activeIds = array_values(array_unique(array_merge($clientIdsWithOrders, $clientIdsWithPayments)));

        $clients = Client::query()
            ->whereIn('id', $activeIds ?: [0])
            ->orderBy('name')
            ->get(['id', 'name', 'city'])
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'code' => $client->code,
                'name' => $client->name,
                'city' => $client->city,
                'has_activity' => true,
            ])
            ->values();

        $clientId = isset($data['client_id']) ? (int) $data['client_id'] : null;
        $client = $clientId ? Client::query()->findOrFail($clientId) : null;

        $ordersQuery = DeliveryOrder::query()->orderBy('order_date')->orderBy('id');
        $paymentsQuery = ClientPayment::query()
            ->with(['client', 'allocations.deliveryOrder'])
            ->orderBy('payment_date')
            ->orderBy('id');

        if ($client) {
            $ordersQuery->where(function ($query) use ($client) {
                $query->where('client_id', $client->id);
                $code = trim((string) $client->code);
                $name = trim((string) $client->name);
                if ($code !== '') {
                    $query->orWhereRaw('LOWER(client_code) = ?', [mb_strtolower($code)]);
                }
                if ($name !== '') {
                    $query->orWhereRaw('LOWER(client_name) = ?', [mb_strtolower($name)]);
                }
            });
            $paymentsQuery->where('client_id', $client->id);
        }

        $events = collect();

        $ordersQuery->get()->each(function (DeliveryOrder $order) use ($events) {
            $events->push([
                'sort_date' => $order->order_date?->format('Y-m-d') ?? '1970-01-01',
                'sort_id' => $order->id,
                'operation' => 'Vente',
                'date' => $order->order_date?->format('Y-m-d'),
                'nom' => $order->client_name ?: '—',
                'bon' => $order->reference,
                'qte' => round((float) $order->qty_total, 3),
                'debit' => round((float) $order->total, 2),
                'credit' => 0.0,
                'type_reg' => null,
                'num_reg' => null,
                'nom_tire' => null,
                'date_encaiss' => null,
                'action' => null,
            ]);
        });

        $paymentsQuery->get()->each(function (ClientPayment $payment) use ($events) {
            $action = trim((string) ($payment->statut ?? ''));
            $events->push([
                'sort_date' => $payment->payment_date?->format('Y-m-d') ?? '1970-01-01',
                'sort_id' => 1000000 + $payment->id,
                'operation' => 'Rég',
                'date' => $payment->payment_date?->format('Y-m-d'),
                'nom' => $payment->client?->name ?: '—',
                'bon' => null,
                'qte' => null,
                'debit' => 0.0,
                'credit' => round((float) $payment->montant, 2),
                'type_reg' => $payment->reglement,
                'num_reg' => $payment->numero ?: $payment->reference,
                'nom_tire' => $payment->nom_tire,
                'date_encaiss' => $payment->date_decaissement?->format('Y-m-d'),
                'action' => $action !== '' ? $action : null,
            ]);
        });

        $sorted = $events->sortBy([
            ['sort_date', 'asc'],
            ['sort_id', 'asc'],
        ])->values();

        $running = 0.0;
        $rows = $sorted->map(function (array $row) use (&$running) {
            $running = round($running + (float) $row['debit'] - (float) $row['credit'], 2);
            $action = trim((string) ($row['action'] ?? ''));

            return [
                'operation' => $row['operation'],
                'date' => $row['date'],
                'date_fr' => $row['date'] ? $this->frDate($row['date']) : '—',
                'nom' => $row['nom'] ?: '—',
                'bon' => $row['bon'],
                'qte' => $row['qte'],
                'debit' => round((float) $row['debit'], 2),
                'credit' => round((float) $row['credit'], 2),
                'solde' => $running,
                'type_reg' => $row['type_reg'],
                'num_reg' => $row['num_reg'],
                'nom_tire' => $row['nom_tire'],
                'date_encaiss' => $row['date_encaiss'],
                'date_encaiss_fr' => $row['date_encaiss'] ? $this->frDate($row['date_encaiss']) : '—',
                'paye' => $action === 'Payé',
                'devalide' => $action === 'Dévalidé',
                'impaye' => $action === 'Imp',
                'reporte' => $action === 'Report',
            ];
        })->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'clients' => $clients,
                'client' => $client ? [
                    'id' => $client->id,
                    'code' => $client->code,
                    'name' => $client->name,
                ] : null,
                'total_debit' => round($rows->sum('debit'), 2),
                'total_credit' => round($rows->sum('credit'), 2),
                'solde' => $running,
            ],
        ]);
    }

    private function frDate(string $value): string
    {
        $parts = explode('-', $value);

        return count($parts) === 3 ? $parts[2].'/'.$parts[1].'/'.$parts[0] : $value;
    }
}
