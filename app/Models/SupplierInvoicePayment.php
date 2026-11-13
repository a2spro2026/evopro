<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoicePayment extends Model
{
    protected $fillable = [
        'supplier_invoice_id',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SupplierInvoicePaymentLine::class);
    }

    public function getReferenceAttribute(): string
    {
        return 'RA-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
