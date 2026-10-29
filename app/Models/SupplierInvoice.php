<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'reference',
        'supplier_id',
        'depot',
        'invoice_date',
        'due_date',
        'status',
        'notes',
        'total_ht',
        'tva',
        'total_ttc',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'total_ht' => 'decimal:2',
            'tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function getReferenceAttribute(): string
    {
        $stored = trim((string) ($this->attributes['reference'] ?? ''));
        if ($stored !== '') {
            return $stored;
        }

        return 'FA-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function toFicheArray(): array
    {
        $depots = ['depot_a' => 'Depot A', 'depot_b' => 'Depot B'];
        $statuts = [
            'brouillon' => 'Brouillon',
            'en_attente' => 'En attente',
            'partielle' => 'Partielle',
            'payee' => 'Payée',
            'en_retard' => 'En retard',
            'annulee' => 'Annulée',
        ];

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'invoice_date' => $this->invoice_date?->format('d/m/Y'),
            'invoice_date_raw' => $this->invoice_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('d/m/Y'),
            'due_date_raw' => $this->due_date?->format('Y-m-d'),
            'supplier_id' => $this->supplier_id,
            'fournisseur' => $this->supplier?->name ?? '—',
            'ice' => $this->supplier?->ice,
            'depot' => $this->depot,
            'depot_label' => $depots[$this->depot] ?? $this->depot,
            'total_ht' => round((float) $this->total_ht, 2),
            'tva' => round((float) $this->tva, 2),
            'total_ttc' => round((float) $this->total_ttc, 2),
            'status' => $this->status,
            'status_label' => $statuts[$this->status] ?? $this->status,
            'notes' => $this->notes,
            'items' => $this->items->map(fn (SupplierInvoiceItem $item) => [
                'id' => $item->id,
                'article_ref' => $item->article_ref,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'remise' => (float) $item->remise,
                'total' => (float) $item->total,
            ])->values(),
        ];
    }
}
