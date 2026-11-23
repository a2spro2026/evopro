<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }
}
