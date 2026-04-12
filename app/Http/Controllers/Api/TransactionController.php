<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'payment_method' => 'required|in:CASH,QRIS',
            'cash_received' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Merge duplicate product rows first to reduce DB writes.
            $mergedItems = [];
            foreach ($request->items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $price = (int) $item['price'];

                if (!isset($mergedItems[$productId])) {
                    $mergedItems[$productId] = [
                        'product_id' => $productId,
                        'quantity' => 0,
                        'price' => $price,
                    ];
                }

                $mergedItems[$productId]['quantity'] += $quantity;
                $mergedItems[$productId]['price'] = $price;
            }

            $productIds = array_keys($mergedItems);

            // Lock rows in one query so parallel cashiers don't oversell stock.
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa produk tidak ditemukan'
                ], 404);
            }

            $totalAmount = 0;
            $transactionItems = [];
            $updatedProducts = [];

            foreach ($mergedItems as $item) {
                $product = $products->get($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk {$product->name}"
                    ], 400);
                }

                $itemTotal = $item['quantity'] * $item['price'];
                $totalAmount += $itemTotal;

                $product->stock -= $item['quantity'];
                if ($product->stock <= 0) {
                    $product->is_available = false;
                }
                $product->save();

                $updatedProducts[] = $product;

                $transactionItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $itemTotal,
                ];
            }

            // Calculate change for cash payment
            $cashReceived = $request->payment_method === 'CASH' ? $request->cash_received : null;
            $changeAmount = $request->payment_method === 'CASH' && $cashReceived ? ($cashReceived - $totalAmount) : null;

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'transaction_number' => Transaction::generateTransactionNumber(),
                'total_amount' => $totalAmount,
                'cash_received' => $cashReceived,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'items' => $transactionItems,
                'status' => 'completed',
            ]);

            // Push stock updates using in-memory updated products (no extra DB queries).
            foreach ($updatedProducts as $updatedProduct) {
                $this->broadcastStockUpdate($updatedProduct);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'transaction_number' => $transaction->transaction_number,
                    'total_amount' => $transaction->total_amount,
                    'cash_received' => $transaction->cash_received,
                    'change_amount' => $transaction->change_amount,
                    'payment_method' => $transaction->payment_method,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTodayStats()
    {
        $today = now()->format('Y-m-d');
        
        $income = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $expense = 0; // Placeholder for future expense tracking
        
        $transactionCount = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'today_income' => $income,
                'today_expense' => $expense,
                'today_transactions' => $transactionCount,
            ]
        ]);
    }

    private function broadcastStockUpdate($product)
    {
        // Create push notification for real-time update
        $updateData = [
            'type' => 'stock_update',
            'product_id' => $product->id,
            'stock' => $product->stock,
            'is_available' => $product->is_available,
            'timestamp' => now()->toISOString(),
            'action' => 'transaction_update'
        ];

        // Write once with lock to avoid duplicate/noisy file modification triggers.
        file_put_contents(
            storage_path('app/stock_updates.json'),
            json_encode($updateData),
            LOCK_EX
        );
        
        // Skip per-request logging here to reduce I/O during checkout bursts.
    }
}
