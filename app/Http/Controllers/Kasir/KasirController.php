<?php
// app/Http/Controllers/Kasir/KasirController.php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\KasirHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $categories = Category::orderByRaw("CASE name
                WHEN 'Coffee' THEN 1
                WHEN 'Non Coffee' THEN 2
                WHEN 'Coffee Milk' THEN 3
                WHEN 'Snack' THEN 4
                WHEN 'Bottle' THEN 5
                ELSE 6 END")
            ->orderBy('name')
            ->get();

        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderByRaw("CASE categories.name
                WHEN 'Coffee' THEN 1
                WHEN 'Non Coffee' THEN 2
                WHEN 'Coffee Milk' THEN 3
                WHEN 'Snack' THEN 4
                WHEN 'Bottle' THEN 5
                ELSE 6 END")
            ->orderBy('categories.name')
            ->orderBy('products.name')
            ->select('products.*')
            ->get();
        
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
            'payment_method' => 'required|in:cash,qris',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'global_discount' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        
        try {
            // Hitung subtotal
            $subtotal = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi");
                }
                $subtotal += $product->price * $item['quantity'];
            }

            // Hitung global discount
            $globalDiscountPercent = $request->global_discount ?? 0;
            $globalDiscountAmount = $subtotal * ($globalDiscountPercent / 100);
            $afterDiscount = $subtotal - $globalDiscountAmount;

            // Hitung tax
            $taxPercentage = $request->tax_percentage ?? 0;
            $taxAmount = $afterDiscount * ($taxPercentage / 100);
            $total = $afterDiscount + $taxAmount;

            // Generate order number dengan format ORD-IDPGICFFEE{DATE}-{URUTAN}
            $transactionNumber = $this->generateOrderNumber();

            // Buat order
            $order = Order::create([
                'order_number' => $transactionNumber,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total' => $total,
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => 'done', // Use 'done' instead of 'completed'
                'notes' => $request->notes ?? null,
            ]);

            // Buat order items dan kurangi stok
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                // Kurangi stok
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Simpan ke KasirHistory
            $kasirHistory = KasirHistory::create([
                'transaction_number' => $transactionNumber,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method ?? 'cash',
                'total_amount' => $total,
                'cash_received' => $request->payment,
                'change_amount' => $request->payment - $total,
                'notes' => $request->notes ?? null,
                'items' => $request->items,
                'items_count' => count($request->items),
                'status' => 'completed', // KasirHistory uses 'completed'
            ]);

            // Simpan ke Transaction untuk sinkronisasi dengan laporan penjualan
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'cash_received' => $request->payment,
                'change_amount' => $request->payment - $total,
                'payment_method' => $request->payment_method ?? 'cash',
                'items' => $request->items,
                'status' => 'completed', // Transaction uses 'completed'
                'subtotal_amount' => $subtotal,
                'total_item_discount' => 0,
                'global_discount_percent' => $globalDiscountPercent,
                'global_discount_amount' => $globalDiscountAmount,
                'tax_amount' => $taxAmount,
                'notes' => $request->notes ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order,
                'kasir_history' => $kasirHistory,
                'transaction' => $transaction,
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
        $transaction = Transaction::with('user')->findOrFail($orderId);
        $this->hydrateTransactionItemNames($transaction);

        return view('orders.receipt', ['transaction' => $transaction]);
    }

    public function receiptByOrderNumber($orderNumber)
    {
        $transaction = Transaction::with('user')
            ->where('transaction_number', $orderNumber)
            ->firstOrFail();

        $this->hydrateTransactionItemNames($transaction);

        return view('orders.receipt', ['transaction' => $transaction]);
    }

    private function hydrateTransactionItemNames(Transaction $transaction)
    {
        if (!is_array($transaction->items) || empty($transaction->items)) {
            return;
        }

        $productIds = collect($transaction->items)
            ->pluck('id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($productIds)) {
            return;
        }

        $productNames = Product::whereIn('id', $productIds)
            ->pluck('name', 'id')
            ->all();

        $transaction->items = collect($transaction->items)->map(function ($item) use ($productNames) {
            $item['product_name'] = $productNames[$item['id']] ?? ($item['product_name'] ?? 'Unknown');
            return $item;
        })->toArray();
    }

    public function orders()
    {
        $orders = Order::with('items.product')->latest()->paginate(20);
        return view('kasir.orders', compact('orders'));
    }

    public function apiHistory()
    {
        // Very simple approach - return empty if no data, no complex logic
        try {
            $transactions = collect(); // Start with empty collection
            
            // Try to get transactions, but don't fail if table doesn't exist
            try {
                $transactions = Transaction::with('user')
                    ->whereDate('created_at', today())
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(50)
                    ->get(['id', 'transaction_number', 'payment_method', 'total_amount', 'created_at', 'user_id']);
            } catch (\Exception $e) {
                // Table might not exist, just return empty
                $transactions = collect();
            }

            return response()->json([
                'transactions' => $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id ?? 0,
                        'transaction_number' => $transaction->transaction_number ?? 'N/A',
                        'order_number' => $transaction->transaction_number ?? 'N/A',
                        'payment_method' => $transaction->payment_method ?? 'cash',
                        'total_amount' => $transaction->total_amount ?? 0,
                        'created_at' => $transaction->created_at ? $transaction->created_at->toISOString() : now()->toISOString(),
                        'user' => $transaction->user ? $transaction->user->name : 'System'
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            // Always return valid structure
            return response()->json([
                'transactions' => []
            ]);
        }
    }

    /**
     * Generate order number dengan format ORD-IDPGICFFEE{DATE}-{URUTAN}
     */
    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        
        // Cari transaksi terakhir hari ini untuk mendapatkan urutan
        $lastTransaction = Transaction::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastTransaction) {
            // Extract sequence dari transaction number terakhir
            $lastNumber = $lastTransaction->transaction_number;
            if (preg_match('/ORD-IDPGICFFEE' . $date . '-(\d+)/', $lastNumber, $matches)) {
                $sequence = (int)$matches[1] + 1;
            }
        }
        
        return 'ORD-IDPGICFFEE' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}