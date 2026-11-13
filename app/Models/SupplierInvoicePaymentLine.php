<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvoicePaymentLine extends Model
{
    protected $fillable = [
        'supplier_invoice_payment_id',
        'amount',
        'method',
        'reference',
        'bank',
        'nom_tire',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoicePayment::class, 'supplier_invoice_payment_id');
    }
}
