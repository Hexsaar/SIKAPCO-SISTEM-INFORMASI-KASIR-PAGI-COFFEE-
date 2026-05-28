<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function history(Request $request)
    {
        // Auto-cleanup transaksi lama (lebih dari 1 hari)
        $this->cleanupOldTransactions();
        
        $query = Transaction::with('user');

        // Filter by status - hanya completed yang ditampilkan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'completed'); // Default hanya completed
        }

        // Search by transaction number or product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhereJsonContains('items', [['product_name' => $search]]);
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Hanya tampilkan transaksi hari ini
        $query->whereDate('created_at', '>=', now()->subDay());

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get best selling today dari transactions
        $bestSellersToday = $this->getBestSellersToday();

        return view('orders.history', compact('transactions', 'bestSellersToday'));
    }

    private function cleanupOldTransactions()
    {
        // Hapus transaksi yang lebih dari 2 hari dan status completed
        Transaction::where('status', 'completed')
            ->where('created_at', '<', now()->subDays(2))
            ->delete();
        
        // Update transaksi pending yang lebih dari 1 hari menjadi expired
        Transaction::where('status', 'pending')
            ->where('created_at', '<', now()->subDay())
            ->update(['status' => 'expired']);
    }

    private function getBestSellersToday()
    {
        $transactions = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->get();
        
        $productSales = [];
        
        foreach ($transactions as $transaction) {
            if (is_array($transaction->items)) {
                foreach ($transaction->items as $item) {
                    $productName = $item['product_name'] ?? 'Unknown';
                    $quantity = $item['quantity'] ?? 0;
                    
                    if (!isset($productSales[$productName])) {
                        $productSales[$productName] = 0;
                    }
                    $productSales[$productName] += $quantity;
                }
            }
        }
        
        // Sort and limit to 5
        arsort($productSales);
        $topSellers = array_slice($productSales, 0, 5, true);
        
        // Convert to collection
        $result = collect();
        foreach ($topSellers as $name => $totalSold) {
            $result->push((object) [
                'product_name' => $name,
                'total_sold' => $totalSold
            ]);
        }
        
        return $result;
    }

    public function printReceipt(Transaction $transaction)
    {
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