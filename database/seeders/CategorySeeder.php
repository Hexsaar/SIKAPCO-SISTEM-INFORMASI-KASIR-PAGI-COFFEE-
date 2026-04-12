<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Coffee',
            'Non Coffee',
            'Coffee Milk',
            'Snack',
            'Bottle'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category
            ], [
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => "Kategori $category"
            ]);
        }
    }
}