<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\ChargeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChargeController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Charge::query()
            ->with('category')
            ->orderByDesc('charge_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Charge $charge) => $this->format($charge))
            ->values();

        $categories = ChargeCategory::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ChargeCategory $cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
            ])
            ->values();

        $next = (Charge::max('id') ?? 0) + 1;

        return response()->json([
            'data' => $rows,
            'meta' => [
                'next_ref' => $this->referenceFor($next),
                'date' => now()->format('Y-m-d'),
                'total' => round((float) $rows->sum(fn (array $row) => (float) $row['montant']), 2),
                'categories' => $categories,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $charge = DB::transaction(function () use ($data) {
            if (empty($data['reference'])) {
                $next = (Charge::max('id') ?? 0) + 1;
                $data['reference'] = $this->referenceFor($next);
            }

            return Charge::query()->create($data)->load('category');
        });

        return response()->json($this->format($charge), 201);
    }

    public function update(Request $request, Charge $charge): JsonResponse
    {
        $data = $this->validated($request, $charge);

        $charge->fill($data)->save();
        $charge->load('category');

        return response()->json($this->format($charge));
    }

    public function destroy(Charge $charge): JsonResponse
    {
        $charge->delete();

        return response()->json(['ok' => true]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
        ]);

        $name = trim($data['name']);
        $existing = ChargeCategory::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            return response()->json([
                'id' => $existing->id,
                'name' => $existing->name,
            ]);
        }

        $category = ChargeCategory::query()->create(['name' => $name]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ], 201);
    }

    private function validated(Request $request, ?Charge $charge = null): array
    {
        $data = $request->validate([
            'reference' => [
                'nullable',
                'string',
                'max:64',
                Rule::unique('charges', 'reference')->ignore($charge?->id),
            ],
            'charge_date' => ['required', 'date'],
            'charge_category_id' => ['nullable', 'integer', 'exists:charge_categories,id'],
            'designation' => ['nullable', 'string', 'max:255'],
            'beneficiaire' => ['nullable', 'string', 'max:255'],
            'reglement' => ['nullable', 'string', 'max:10'],
            'numero' => ['nullable', 'string', 'max:50'],
            'banque' => ['nullable', 'string', 'max:100'],
            'nom_tire' => ['nullable', 'string', 'max:150'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date_encaissement' => ['nullable', 'date'],
        ]);

        $data['montant'] = round((float) $data['montant'], 2);
        $data['charge_category_id'] = $data['charge_category_id'] ?? null;
        $data['designation'] = trim((string) ($data['designation'] ?? '')) ?: null;
        $data['beneficiaire'] = trim((string) ($data['beneficiaire'] ?? '')) ?: null;
        $data['reglement'] = trim((string) ($data['reglement'] ?? '')) ?: null;
        $data['numero'] = trim((string) ($data['numero'] ?? '')) ?: null;
        $data['banque'] = trim((string) ($data['banque'] ?? '')) ?: null;
        $data['nom_tire'] = trim((string) ($data['nom_tire'] ?? '')) ?: null;
        $data['date_encaissement'] = $data['date_encaissement'] ?? null;

        return $data;
    }

    private function format(Charge $charge): array
    {
        return [
            'id' => $charge->id,
            'reference' => $charge->reference,
            'charge_date' => optional($charge->charge_date)->format('Y-m-d'),
            'charge_date_fr' => optional($charge->charge_date)->format('d/m/Y'),
            'month' => optional($charge->charge_date)->format('n'),
            'charge_category_id' => $charge->charge_category_id,
            'categorie' => $charge->category?->name ?? '',
            'designation' => $charge->designation ?? '',
            'beneficiaire' => $charge->beneficiaire ?? '',
            'reglement' => $charge->reglement ?? '',
            'numero' => $charge->numero ?? '',
            'banque' => $charge->banque ?? '',
            'nom_tire' => $charge->nom_tire ?? '',
            'montant' => round((float) $charge->montant, 2),
            'date_encaissement' => optional($charge->date_encaissement)->format('Y-m-d'),
            'date_encaissement_fr' => optional($charge->date_encaissement)->format('d/m/Y'),
        ];
    }

    private function referenceFor(int $id): string
    {
        return 'CH'.str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
}
