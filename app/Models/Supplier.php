<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function getCodeAttribute(): string
    {
        return 'CF-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function toFicheArray(): array
    {
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
            'initial_balance' => number_format((float) $this->initial_balance, 2, '.', ''),
            'created_at' => $this->created_at?->format('d/m/Y'),
        ];
    }
}
