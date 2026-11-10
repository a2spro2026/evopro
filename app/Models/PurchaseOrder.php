<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_date',
        'reference',
        'supplier_id',
        'supplier_code',
        'supplier_name',
        'payment_type',
        'due_term',
        'qty_total',
        'total',
        'montant_paye',
        'payment_action',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'qty_total' => 'decimal:3',
            'total' => 'decimal:2',
            'montant_paye' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function toFicheArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->order_date?->format('Y-m-d'),
            'ref' => $this->reference,
            'supplier' => $this->supplier_name,
            'frns' => $this->supplier_code,
            'qty' => (float) $this->qty_total,
            'total' => (float) $this->total,
            'paid' => round((float) ($this->montant_paye ?? 0), 2),
            'solde' => round((float) $this->total - (float) ($this->montant_paye ?? 0), 2),
            'paiement' => $this->payment_type,
            'echeance' => $this->due_term,
            'month' => $this->order_date ? (int) $this->order_date->format('n') : null,
            'items' => $this->items->map(fn (PurchaseOrderItem $item) => [
                'ref' => $item->article_ref,
                'barcode' => $item->barcode,
                'equiv' => $item->equiv,
                'description' => $item->description,
                'brand' => $item->brand,
                'family' => $item->family,
                'qty' => (float) $item->quantity,
                'price' => (float) $item->unit_price,
                'remise' => (float) $item->remise,
                'sub' => (float) $item->total,
            ])->values(),
        ];
    }
}
