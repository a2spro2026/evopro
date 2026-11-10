<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\JsonResponse;

class SupplierBalanceController extends Controller
{
    public function index(): JsonResponse
    {
        $achats = PurchaseOrder::query()
            ->selectRaw('supplier_id, COALESCE(SUM(total), 0) as total_achats')
            ->whereNotNull('supplier_id')
            ->groupBy('supplier_id')
            ->pluck('total_achats', 'supplier_id');

        $payes = SupplierPayment::query()
            ->selectRaw('supplier_id, COALESCE(SUM(montant), 0) as montant_paye')
            ->whereNotNull('supplier_id')
            ->groupBy('supplier_id')
            ->pluck('montant_paye', 'supplier_id');

        $supplierIds = $achats->keys()->filter()->values();

        $rows = Supplier::query()
            ->whereIn('id', $supplierIds)
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(function (Supplier $supplier) use ($achats, $payes) {
                $totalAchats = round((float) ($achats[$supplier->id] ?? 0), 2);
                $montantPaye = round((float) ($payes[$supplier->id] ?? 0), 2);
                $solde = round(max($totalAchats - $montantPaye, 0), 2);
                $reliquat = round(max($montantPaye - $totalAchats, 0), 2);

                return [
                    'id' => $supplier->id,
                    'code' => $supplier->code,
                    'name' => $supplier->name,
                    'total_achats' => $totalAchats,
                    'montant_paye' => $montantPaye,
                    'solde' => $solde,
                    'reliquat' => $reliquat,
                ];
            })
            ->values();

        $meta = [
            'total_achats' => round($rows->sum('total_achats'), 2),
            'montant_paye' => round($rows->sum('montant_paye'), 2),
            'solde' => round($rows->sum('solde'), 2),
            'reliquat' => round($rows->sum('reliquat'), 2),
            'count' => $rows->count(),
        ];

        return response()->json([
            'data' => $rows,
            'meta' => $meta,
        ]);
    }
}
