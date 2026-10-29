<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    protected $fillable = [
        'customer_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesInvoicePayment::class);
    }

    public function getReferenceAttribute(): string
    {
        return 'FV-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function toFicheArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'invoice_date' => $this->invoice_date?->format('d/m/Y'),
            'invoice_date_raw' => $this->invoice_date?->format('Y-m-d'),
            'client_id' => $this->customer_id,
            'client' => $this->customer?->name ?? '—',
            'client_address' => $this->customer?->address,
            'client_city' => $this->customer?->city,
            'ice' => $this->customer?->ice,
            'total_ht' => round((float) $this->total_ht, 2),
            'tva' => round((float) $this->tva, 2),
            'total_ttc' => round((float) $this->total_ttc, 2),
            'items' => $this->items->map(fn (SalesInvoiceItem $item) => [
                'id' => $item->id,
                'article_ref' => $item->article_ref,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'purchase_price' => (float) $item->purchase_price,
                'unit_price' => (float) $item->unit_price,
                'remise' => (float) $item->remise,
                'total' => (float) $item->total,
            ])->values(),
        ];
    }
}
