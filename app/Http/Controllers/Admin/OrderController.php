<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
        // Build receipt HTML exactly like kasir
        $items = $transaction->items ?? [];
        $orderId = 'ORD-IDPGICFFEE' . ($transaction->created_at ? $transaction->created_at->format('Ymd') : date('Ymd'));
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { font-family: "Courier New", monospace; font-size: 13px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl" style="font-family: "Courier New", monospace; font-size: 13px;">
        <!-- Title -->
        <div class="text-center mb-3 font-bold text-lg">
          Cetak struk
        </div>
        
        <!-- Shop Info -->
        <div class="text-center mb-3 leading-tight">
          <div class="font-bold">Pagi Coffee</div>
          <div class="text-xs">Taman Pagelaran</div>
          <div class="text-xs">085353712877</div>
        </div>
        
        <!-- Separator 1 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Order ID -->
        <div class="text-xs mb-2 text-center">
          Order ID: ' . $orderId . '
        </div>
        
        <!-- Separator 2 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Kasir Info -->
        <div class="text-xs mb-2">
          Kasir: ' . ($transaction->user ? $transaction->user->name : 'Admin') . '
        </div>
        
        <!-- Separator 3 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Items Table -->
        <table class="w-full text-xs mb-2">
          <thead>
            <tr>
              <th class="text-left py-1">Item</th>
              <th class="text-center py-1">Qty</th>
              <th class="text-right py-1">Harga</th>
              <th class="text-right py-1">Subtotal</th>
            </tr>
          </thead>
          <tbody>';
        
        // Add items
        if (is_array($items)) {
            foreach ($items as $item) {
                $itemName = $item['product_name'] ?? 'Unknown';
                $quantity = $item['quantity'] ?? 0;
                $price = $item['price'] ?? 0;
                $subtotal = $price * $quantity;
                
                $html .= '
                <tr>
                  <td class="py-1">' . $itemName . '</td>
                  <td class="text-center py-1">' . $quantity . '</td>
                  <td class="text-right py-1">Rp ' . number_format($price, 0, ',', '.') . '</td>
                  <td class="text-right py-1">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
                </tr>';
            }
        }
        
        $html .= '
          </tbody>
        </table>
        
        <!-- Separator 4 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Summary Section -->
        <div class="text-xs mb-2 space-y-1">
          <div class="flex justify-between">
            <span>Discount (%)</span>
            <span class="text-right">Rp 0</span>
          </div>
          <div class="flex justify-between">
            <span>Sub total</span>
            <span class="text-right">Rp ' . number_format($transaction->total_amount ?? 0, 0, ',', '.') . '</span>
          </div>
          <div class="flex justify-between">
            <span>Tax 5%</span>
            <span class="text-right">Rp 0</span>
          </div>
        </div>
        
        <!-- Separator 5 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Total Section -->
        <div class="text-lg font-bold mb-3 text-center">
          Total: Rp ' . number_format($transaction->total_amount ?? 0, 0, ',', '.') . '
        </div>
        
        <!-- Separator 6 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Payment Section -->
        <div class="text-xs mb-3 space-y-1">';
        
        if ($transaction->payment_method === 'cash' && $transaction->cash_received) {
            $html .= '
            <div class="flex justify-between">
              <span>Tunai</span>
              <span class="text-right">' . number_format($transaction->cash_received, 0, ',', '.') . '</span>
            </div>
            <div class="flex justify-between font-bold">
              <span>Kembalian</span>
              <span class="text-right">' . number_format($transaction->change_amount, 0, ',', '.') . '</span>
            </div>';
        } else {
            $html .= '
            <div class="flex justify-between">
              <span>Metode Pembayaran</span>
              <span class="text-right font-bold">' . strtoupper($transaction->payment_method ?? 'CASH') . '</span>
            </div>';
        }
        
        $html .= '
        </div>
        
        <!-- Separator 7 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Footer -->
        <div class="text-center text-xs text-gray-600 mb-4">
          <p>Terima kasih telah berbelanja</p>
          <p>Silakan datang kembali</p>
        </div>
        
        <!-- Button -->
        <button class="w-full bg-[#4F2E22] hover:bg-[#3f251b] text-white font-bold py-2 rounded-lg transition active:scale-95 no-print" onclick="window.print()">
          Cetak
        </button>
    </div>
</body>
</html>';
        
        return response($html);
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