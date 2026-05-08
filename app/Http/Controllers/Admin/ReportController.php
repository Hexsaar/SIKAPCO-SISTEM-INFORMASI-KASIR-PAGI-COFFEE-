<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Transaction::where('status', 'completed');

        // Default filter: show today's data if no filter applied
        if (!$request->filled('start_date') && !$request->filled('end_date') && 
            !$request->filled('month') && !$request->filled('year')) {
            $query->whereDate('created_at', today());
        }

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

        $orders = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Calculate totals
        $totalRevenue = $query->sum('total_amount');
        $totalOrders = $query->count();
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Best selling products from transactions
        $transactions = $query->get();
        $productSales = [];
        
        foreach ($transactions as $transaction) {
            if (is_array($transaction->items)) {
                foreach ($transaction->items as $item) {
                    $productName = $item['product_name'] ?? 'Unknown';
                    $quantity = $item['quantity'] ?? 0;
                    $price = $item['price'] ?? 0;
                    
                    if (!isset($productSales[$productName])) {
                        $productSales[$productName] = [
                            'name' => $productName,
                            'price' => $price,
                            'total_sold' => 0,
                            'total_revenue' => 0
                        ];
                    }
                    $productSales[$productName]['total_sold'] += $quantity;
                    $productSales[$productName]['total_revenue'] += ($price * $quantity);
                }
            }
        }
        
        // Convert to collection
        $bestSellers = collect(array_values($productSales))
            ->sortByDesc('total_sold')
            ->take(10);

        // Daily sales chart data (last 30 days)
        $dailySales = DB::table('transactions')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
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
        $query = Transaction::where('status', 'completed');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $query->sum('total_amount');
        $totalOrders = $query->count();

        $pdf = PDF::loadView('reports.pdf', compact('orders', 'totalRevenue', 'totalOrders'));
        
        return $pdf->download('laporan-penjualan-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new SalesReportExport($request), 'laporan-penjualan-'.now()->format('Y-m-d').'.xlsx');
    }
}