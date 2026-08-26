<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
    ];

    public function stockLedgers(): HasMany
    {
        return $this->hasMany(StockLedger::class);
    }
}