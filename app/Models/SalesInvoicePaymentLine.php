<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoicePaymentLine extends Model
{
    protected $fillable = [
        'sales_invoice_payment_id',
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
        return $this->belongsTo(SalesInvoicePayment::class, 'sales_invoice_payment_id');
    }
}
