<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    protected $fillable = [
        'reference',
        'designation',
        'stock_initial',
        'purchase_price',
    ];

    protected function casts(): array
    {
        return [
            'stock_initial' => 'decimal:3',
            'purchase_price' => 'decimal:2',
        ];
    }
}
