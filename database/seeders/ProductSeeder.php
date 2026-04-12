<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Americano',
                'category_id' => 1,
                'price' => 25000,
                'stock' => 100,
                'description' => 'Kopi hitam klasik dengan rasa yang kuat'
            ],
            [
                'name' => 'Cappuccino',
                'category_id' => 1,
                'price' => 30000,
                'stock' => 80,
                'description' => 'Kopi dengan busa susu yang lembut'
            ],
            [
                'name' => 'Latte',
                'category_id' => 1,
                'price' => 32000,
                'stock' => 85,
                'description' => 'Kopi dengan susu yang creamy'
            ],
            [
                'name' => 'Green Tea Latte',
                'category_id' => 2,
                'price' => 28000,
                'stock' => 60,
                'description' => 'Minuman teh hijau dengan susu'
            ],
            [
                'name' => 'Chocolate',
                'category_id' => 2,
                'price' => 27000,
                'stock' => 75,
                'description' => 'Minuman coklat panas/dingin'
            ],
            [
                'name' => 'Nasi Goreng',
                'category_id' => 3,
                'price' => 35000,
                'stock' => 40,
                'description' => 'Nasi goreng dengan telur dan ayam'
            ],
            [
                'name' => 'French Fries',
                'category_id' => 4,
                'price' => 20000,
                'stock' => 120,
                'description' => 'Kentang goreng renyah'
            ],
            [
                'name' => 'Cheese Cake',
                'category_id' => 5,
                'price' => 30000,
                'stock' => 30,
                'description' => 'Kue keju lembut'
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'category_id' => $product['category_id'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'description' => $product['description'],
                'is_available' => true,
            ]);
        }
    }
}