<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Order::with(['items.product', 'user'])
            ->where('status', 'done');

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year ?? now()->year);
        }

        // Filter by year
        if ($request->filled('year') && !$request->filled('month')) {
            $query->whereYear('created_at', $request->year);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate totals
        $totalRevenue = $query->sum('total');
        $totalOrders = $query->count();
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Best selling products
        $bestSellers = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'done')
            ->when($request->filled('start_date') && $request->filled('end_date'), function($q) use ($request) {
                $q->whereBetween('orders.created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->select(
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Daily sales chart data (last 30 days)
        $dailySales = DB::table('orders')
            ->where('status', 'done')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.sales', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'averageOrder',
            'bestSellers',
            'dailySales'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Order::with(['items.product', 'user'])
            ->where('status', 'done');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $query->sum('total');
        $totalOrders = $query->count();

        $pdf = PDF::loadView('reports.pdf', compact('orders', 'totalRevenue', 'totalOrders'));
        
        return $pdf->download('laporan-penjualan-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new SalesReportExport($request), 'laporan-penjualan-'.now()->format('Y-m-d').'.xlsx');
    }
}