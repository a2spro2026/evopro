<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Supplier::query()->orderByDesc('id')->get()->map->toFicheArray()->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_id' => $this->nextCode(),
                'date' => now()->format('d/m/Y'),
                'total_soldes' => number_format((float) Supplier::query()->sum('initial_balance'), 2, '.', ''),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $supplier = Supplier::create($this->validated($request));

        return response()->json($supplier->toFicheArray(), 201);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($this->validated($request));

        return response()->json($supplier->fresh()->toFicheArray());
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

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
        $next = (Supplier::max('id') ?? 0) + 1;

        return 'CF-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
