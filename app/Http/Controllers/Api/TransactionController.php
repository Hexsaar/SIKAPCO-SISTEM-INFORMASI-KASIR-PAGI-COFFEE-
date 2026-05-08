<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // Convert frontend data format to match KasirController format
        $items = [];
        $totalPayment = 0;
        
        foreach ($request->items as $item) {
            $items[] = [
                'id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
            $totalPayment += $item['price'] * $item['quantity'];
        }

        // Create new request with correct format
        $newRequest = new Request([
            'items' => $items,
            'payment' => $request->cash_received ?? $totalPayment,
            'payment_method' => strtolower($request->payment_method),
            'customer_name' => 'Guest',
            'notes' => $request->notes
        ]);

        // Use KasirController checkout logic for synchronization
        $kasirController = new \App\Http\Controllers\Kasir\KasirController();
        return $kasirController->checkout($newRequest);
    }

    public function todayStats()
    {
        $kasirController = new \App\Http\Controllers\Kasir\KasirController();
        return $kasirController->apiHistory();
    }
}
