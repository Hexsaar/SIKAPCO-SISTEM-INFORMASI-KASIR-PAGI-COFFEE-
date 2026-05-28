<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_best_seller'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}