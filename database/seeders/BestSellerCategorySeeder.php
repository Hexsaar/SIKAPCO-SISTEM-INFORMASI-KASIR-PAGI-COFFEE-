<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BestSellerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Best Seller category already exists
        $existingCategory = \App\Models\Category::where('name', 'Best Seller')->first();
        
        if (!$existingCategory) {
            \App\Models\Category::create([
                'name' => 'Best Seller',
                'slug' => 'best-seller',
                'description' => 'Kategori untuk menu best seller',
            ]);
            
            $this->command->info('Best Seller category created successfully!');
        } else {
            $this->command->info('Best Seller category already exists!');
        }
    }
}
