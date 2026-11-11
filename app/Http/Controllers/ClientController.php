<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $this->importCustomersFromSalesInvoices();

        $rows = Client::query()->orderByDesc('id')->get()->map->toFicheArray()->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_id' => $this->nextCode(),
                'date' => now()->format('d/m/Y'),
                'total_soldes' => number_format((float) Client::query()->sum('initial_balance'), 2, '.', ''),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $client = Client::create($this->validated($request));

        return response()->json($client->toFicheArray(), 201);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $client->update($this->validated($request));

        return response()->json($client->fresh()->toFicheArray());
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'reglement' => ['nullable', 'in:Esp,Chq,Eff,Vir,Vers'],
            'echeance' => ['nullable', 'in:45 Jrs,60 Jrs,90 Jrs,120 Jrs'],
            'initial_balance' => ['nullable', 'numeric'],
        ]);

        return [
            'name' => trim($data['name']),
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'reglement' => $data['reglement'] ?? null,
            'payment_terms' => $data['echeance'] ?? null,
            'initial_balance' => $data['initial_balance'] ?? 0,
            'status' => 'actif',
        ];
    }

    private function nextCode(): string
    {
        $next = (Client::max('id') ?? 0) + 1;

        return 'CC-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function importCustomersFromSalesInvoices(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        foreach (Customer::query()->orderBy('id')->get() as $customer) {
            $name = trim((string) $customer->name);
            if ($name === '') {
                continue;
            }

            $exists = Client::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists();
            if ($exists) {
                continue;
            }

            Client::query()->create([
                'name' => $name,
                'ice' => $customer->ice,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'city' => $customer->city,
                'reglement' => $customer->reglement,
                'payment_terms' => $customer->payment_terms,
                'status' => $customer->status ?: 'actif',
                'initial_balance' => 0,
            ]);
        }
    }
}
