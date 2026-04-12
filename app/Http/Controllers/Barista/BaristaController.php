<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class BaristaController extends Controller
{
    public function index()
    {
        return view('barista.index');
    }

    public function orders()
    {
        $orders = Order::with('items.product')
            ->where('status', 'pending')
            ->orWhere('status', 'preparing')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('barista.orders', compact('orders'));
    }

    public function completeOrder(Order $order)
    {
        $order->update(['status' => 'completed']);
        
        return redirect()->back()->with('success', 'Order marked as completed!');
    }
}
