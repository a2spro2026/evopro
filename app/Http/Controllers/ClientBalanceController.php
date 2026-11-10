<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\DeliveryOrder;
use Illuminate\Http\JsonResponse;

class ClientBalanceController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = Client::query()->orderBy('id')->get(['id', 'name']);
        $byCode = [];
        $byName = [];
        foreach ($clients as $client) {
            $byCode[mb_strtolower($client->code)] = $client->id;
            $byName[mb_strtolower(trim((string) $client->name))] = $client->id;
        }

        $ventes = [];
        foreach (DeliveryOrder::query()->get(['client_id', 'client_code', 'client_name', 'total']) as $order) {
            $clientId = $this->resolveClientId(
                $order->client_id,
                (string) ($order->client_code ?? ''),
                (string) ($order->client_name ?? ''),
                $byCode,
                $byName
            );
            if (! $clientId) {
                continue;
            }
            $ventes[$clientId] = round(($ventes[$clientId] ?? 0) + (float) $order->total, 2);
        }

        $payes = ClientPayment::query()
            ->selectRaw('client_id, COALESCE(SUM(montant), 0) as montant_paye')
            ->whereNotNull('client_id')
            ->groupBy('client_id')
            ->pluck('montant_paye', 'client_id');

        $rows = $clients
            ->filter(fn (Client $client) => array_key_exists($client->id, $ventes))
            ->map(function (Client $client) use ($ventes, $payes) {
                $totalVentes = round((float) ($ventes[$client->id] ?? 0), 2);
                $montantPaye = round((float) ($payes[$client->id] ?? 0), 2);
                $solde = round(max($totalVentes - $montantPaye, 0), 2);
                $reliquat = round(max($montantPaye - $totalVentes, 0), 2);

                return [
                    'id' => $client->id,
                    'code' => $client->code,
                    'name' => $client->name,
                    'total_ventes' => $totalVentes,
                    'montant_paye' => $montantPaye,
                    'solde' => $solde,
                    'reliquat' => $reliquat,
                ];
            })
            ->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total_ventes' => round($rows->sum('total_ventes'), 2),
                'montant_paye' => round($rows->sum('montant_paye'), 2),
                'solde' => round($rows->sum('solde'), 2),
                'reliquat' => round($rows->sum('reliquat'), 2),
                'count' => $rows->count(),
            ],
        ]);
    }

    private function resolveClientId(mixed $clientId, string $code, string $name, array $byCode, array $byName): ?int
    {
        $id = (int) $clientId;
        if ($id > 0) {
            return $id;
        }

        $codeKey = mb_strtolower(trim($code));
        if ($codeKey !== '' && isset($byCode[$codeKey])) {
            return (int) $byCode[$codeKey];
        }

        $nameKey = mb_strtolower(trim($name));
        if ($nameKey !== '' && isset($byName[$nameKey])) {
            return (int) $byName[$nameKey];
        }

        return null;
    }
}
