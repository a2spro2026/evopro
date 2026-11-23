<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charge extends Model
{
    protected $fillable = [
        'reference',
        'charge_date',
        'charge_category_id',
        'designation',
        'beneficiaire',
        'reglement',
        'numero',
        'banque',
        'nom_tire',
        'montant',
        'date_encaissement',
    ];

    protected function casts(): array
    {
        return [
            'charge_date' => 'date',
            'date_encaissement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChargeCategory::class, 'charge_category_id');
    }
}
