<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        // Today's finance
        $today = now()->format('Y-m-d');
        $todayIncome = Order::whereDate('created_at', $today)
            ->where('status', 'done')
            ->sum('total');
        
        // This month
        $monthIncome = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'done')
            ->sum('total');
        
        // This year
        $yearIncome = Order::whereYear('created_at', now()->year)
            ->where('status', 'done')
            ->sum('total');

        // Chart data - daily for current month
        $dailyData = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'done')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly data for current year
        $monthlyData = Order::whereYear('created_at', now()->year)
            ->where('status', 'done')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('finance.index', compact(
            'todayIncome', 
            'monthIncome', 
            'yearIncome',
            'dailyData',
            'monthlyData'
        ));
    }

    public function daily(Request $request)
    {
        $date = $request->date ?: now()->format('Y-m-d');
        
        $orders = Order::whereDate('created_at', $date)
            ->where('status', 'done')
            ->with('items.product')
            ->get();

        $total = $orders->sum('total');
        $count = $orders->count();

        return view('finance.daily', compact('orders', 'total', 'count', 'date'));
    }

    public function monthly(Request $request)
    {
        $month = $request->month ?: now()->month;
        $year = $request->year ?: now()->year;

        $orders = Order::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'done')
            ->with('items.product')
            ->get();

        $total = $orders->sum('total');
        $count = $orders->count();

        return view('finance.monthly', compact('orders', 'total', 'count', 'month', 'year'));
    }

    public function yearly(Request $request)
    {
        $year = $request->year ?: now()->year;

        $orders = Order::whereYear('created_at', $year)
            ->where('status', 'done')
            ->with('items.product')
            ->get();

        $total = $orders->sum('total');
        $count = $orders->count();

        return view('finance.yearly', compact('orders', 'total', 'count', 'year'));
    }
}