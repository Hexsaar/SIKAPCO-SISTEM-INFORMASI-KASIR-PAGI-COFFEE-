<?php
// app/Http/Controllers/Kasir/KasirController.php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->where('stock', '>', 0)->get();
        
        return view('kasir.index', compact('categories', 'products'));
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::with('category')
            ->where('category_id', $categoryId)
            ->where('stock', '>', 0)
            ->get();
            
        return response()->json($products);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search');
        $products = Product::with('category')
            ->where('name', 'like', "%{$search}%")
            ->where('stock', '>', 0)
            ->get();
            
        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        
        try {
            // Hitung total
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi");
                }
                $total += $product->price * $item['quantity'];
            }

            // Buat order
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT),
                'customer_name' => $request->customer_name ?? 'Guest',
                'total_amount' => $total,
                'payment_amount' => $request->payment,
                'change_amount' => $request->payment - $total,
                'status' => 'completed',
                'user_id' => auth()->id(),
            ]);

            // Buat order items dan kurangi stok
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Kurangi stok
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order,
                'receipt_url' => route('kasir.receipt', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function receipt($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        return view('kasir.receipt', compact('order'));
    }

    public function orders()
    {
        $orders = Order::with('items.product')->latest()->paginate(20);
        return view('kasir.orders', compact('orders'));
    }

    public function apiHistory()
    {
        // Get today's transactions for kasir
        $transactions = Transaction::with('user')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit to last 50 transactions for performance
            ->get(['id', 'transaction_number', 'payment_method', 'total_amount', 'created_at', 'user_id']);

        return response()->json([
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'payment_method' => $transaction->payment_method,
                    'total_amount' => $transaction->total_amount,
                    'created_at' => $transaction->created_at->toISOString(),
                    'user' => $transaction->user ? $transaction->user->name : 'System'
                ];
            })
        ]);
    }
}