<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'unit',
        'min_stock',
        'price_per_unit',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function isLowStock()
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function updateStock($quantity)
    {
        $this->stock += $quantity;
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)
                     ->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
