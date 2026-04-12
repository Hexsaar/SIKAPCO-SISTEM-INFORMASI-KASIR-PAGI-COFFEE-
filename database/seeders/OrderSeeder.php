<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $products = Product::all();

        // Buat 20 order sample
        for ($i = 1; $i <= 20; $i++) {
            $status = ['pending', 'hold', 'done', 'cancelled'][rand(0, 3)];
            $payment = ['cash', 'qris'][rand(0, 1)];
            
            $subtotal = 0;
            $items = [];
            
            // Random items (1-3 items per order)
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $itemSubtotal = $product->price * $quantity;
                $subtotal += $itemSubtotal;
                
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal
                ];
            }
            
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $payment,
                'status' => $status,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))
            ]);
            
            foreach ($items as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }
        }
    }
}