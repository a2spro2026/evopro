<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'ice',
        'payment_terms',
        'reglement',
        'status',
        'notes',
        'initial_balance',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
        ];
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getCodeAttribute(): string
    {
        return 'CF-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function unpaidPurchaseBalance(): float
    {
        if (! Schema::hasTable('purchase_orders')) {
            return 0.0;
        }

        $orders = $this->relationLoaded('purchaseOrders')
            ? $this->purchaseOrders
            : $this->purchaseOrders()->get(['id', 'total', 'montant_paye']);

        return round($orders->sum(function (PurchaseOrder $order) {
            return max((float) $order->total - (float) ($order->montant_paye ?? 0), 0);
        }), 2);
    }

    public function displayedSolde(): float
    {
        return round((float) $this->initial_balance + $this->unpaidPurchaseBalance(), 2);
    }

    public function toFicheArray(): array
    {
        $initial = round((float) $this->initial_balance, 2);
        $baSolde = $this->unpaidPurchaseBalance();
        $solde = round($initial + $baSolde, 2);

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'contact' => $this->phone ?: $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'reglement' => $this->reglement,
            'echeance' => $this->payment_terms,
            'initial_balance' => number_format($initial, 2, '.', ''),
            'ba_solde' => number_format($baSolde, 2, '.', ''),
            'solde' => number_format($solde, 2, '.', ''),
            'created_at' => $this->created_at?->format('d/m/Y'),
        ];
    }
}
