<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 
        'stock', 'category_id', 'image', 'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function isLowStock()
    {
        return $this->stock > 0 && $this->stock <= 5;
    }

    public function isAvailable()
    {
        return $this->is_available && $this->stock > 0;
    }

    public function updateStock($quantity)
    {
        $this->stock -= $quantity;
        
        // Auto disable if stock reaches 0
        if ($this->stock <= 0) {
            $this->is_available = false;
        }
        
        $this->save();
    }

    public function toggleAvailability()
    {
        $this->is_available = !$this->is_available;
        $this->save();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Check if image already includes 'uploads/' path
            if (str_starts_with($this->image, 'uploads/')) {
                return asset($this->image);
            }
            return asset('uploads/' . $this->image);
        }
        return 'https://via.placeholder.com/150';
    }

    // Scope untuk available products
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    // Scope untuk out of stock products
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    // Scope untuk low stock products
    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)->where('stock', '<=', 5);
    }
}