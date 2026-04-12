<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik hari ini - menggunakan Transaction model
        $todayIncome = Transaction::getTodayIncome();
        $todayExpense = Transaction::getTodayExpense();
        
        $totalProducts = Product::count();
        
        // Chart data (6 bulan terakhir) - menggunakan Transaction
        $months = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M');
            
            $monthlyIncome = Transaction::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('status', 'completed')
                ->sum('total_amount');
                
            $monthlyExpense = 0; // TODO: Implement expense tracking
                
            $incomeData[] = $monthlyIncome;
            $expenseData[] = $monthlyExpense;
        }
        
        // Data untuk donut chart kategori penjualan
        $categorySales = $this->getCategorySales();
        
        // Best selling products (hari ini) - menggunakan Transaction
        $bestSellersToday = $this->getBestSellers('today');
        $bestSellersMonth = $this->getBestSellers('month');
        $bestSellersYear = $this->getBestSellers('year');
        
        // Total income today, month, year - menggunakan Transaction
        $totalIncomeToday = Transaction::getTodayIncome();
        
        $totalIncomeMonth = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $totalIncomeYear = Transaction::whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');
        
        // Employees
        $employees = Employee::with('user')
            ->where('is_active', true)
            ->limit(4)
            ->get();
        
        // Additional stats from transactions
        $todayTransactionsCount = Transaction::getTodayTransactionsCount();
        
        return view('dashboard.index', compact(
            'todayIncome', 
            'todayExpense', 
            'totalProducts',
            'months',
            'incomeData',
            'expenseData',
            'categorySales',
            'bestSellersToday',
            'bestSellersMonth',
            'bestSellersYear',
            'totalIncomeToday',
            'totalIncomeMonth',
            'totalIncomeYear',
            'employees',
            'todayTransactionsCount'
        ));
    }
    
    private function getBestSellers($period)
    {
        $query = Transaction::where('status', 'completed');
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
        
        $transactions = $query->get();
        
        $productSales = [];
        
        foreach ($transactions as $transaction) {
            if (is_array($transaction->items)) {
                foreach ($transaction->items as $item) {
                    $productName = $item['product_name'] ?? 'Unknown';
                    $quantity = $item['quantity'] ?? 0;
                    
                    if (!isset($productSales[$productName])) {
                        $productSales[$productName] = [
                            'total_sold' => 0,
                            'product_id' => null
                        ];
                    }
                    $productSales[$productName]['total_sold'] += $quantity;
                }
            }
        }
        
        // Ambil harga produk dari database untuk setiap produk yang terjual
        foreach ($productSales as $productName => &$data) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $data['price'] = $product->price;
                $data['product_id'] = $product->id;
            } else {
                $data['price'] = 0; // Default jika produk tidak ditemukan
            }
        }
        
        // Sort by total_sold (descending)
        uasort($productSales, function($a, $b) {
            return $b['total_sold'] - $a['total_sold'];
        });
        
        $topSellers = array_slice($productSales, 0, 5, true);
        
        // Convert to collection for consistency
        $result = collect();
        foreach ($topSellers as $name => $data) {
            $result->push((object) [
                'name' => $name,
                'total_sold' => $data['total_sold'],
                'price' => $data['price'],
                'product_id' => $data['product_id']
            ]);
        }
        
        return $result;
    }
    
    private function getCategorySales()
    {
        // Ambil semua transaksi completed bulan ini
        $transactions = Transaction::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();
        
        $categoryTotals = [];
        
        foreach ($transactions as $transaction) {
            if (is_array($transaction->items)) {
                foreach ($transaction->items as $item) {
                    $productName = $item['product_name'] ?? 'Unknown';
                    $quantity = $item['quantity'] ?? 0;
                    
                    // Cari product berdasarkan nama untuk dapatkan kategori
                    $product = Product::where('name', $productName)->first();
                    
                    if ($product && $product->category) {
                        $categoryName = $product->category->name;
                    } else {
                        // Default kategori jika tidak ditemukan
                        $categoryName = $this->getDefaultCategory($productName);
                    }
                    
                    if (!isset($categoryTotals[$categoryName])) {
                        $categoryTotals[$categoryName] = 0;
                    }
                    $categoryTotals[$categoryName] += $quantity;
                }
            }
        }
        
        // Format data untuk donut chart
        $categories = ['Coffee', 'Non Coffee', 'Coffee Milk', 'Snack', 'Bottle'];
        $data = [];
        
        foreach ($categories as $category) {
            $data[] = $categoryTotals[$category] ?? 0;
        }
        
        return [
            'labels' => $categories,
            'data' => $data,
            'total' => array_sum($data)
        ];
    }
    
    private function getDefaultCategory($productName)
    {
        // Logic default kategori berdasarkan nama produk
        $productName = strtolower($productName);
        
        if (strpos($productName, 'coffee') !== false || 
            strpos($productName, 'espresso') !== false || 
            strpos($productName, 'americano') !== false ||
            strpos($productName, 'cappuccino') !== false) {
            return 'Coffee';
        } elseif (strpos($productName, 'milk') !== false || 
                  strpos($productName, 'latte') !== false) {
            return 'Coffee Milk';
        } elseif (strpos($productName, 'tea') !== false || 
                  strpos($productName, 'juice') !== false) {
            return 'Non Coffee';
        } elseif (strpos($productName, 'snack') !== false || 
                  strpos($productName, 'cake') !== false ||
                  strpos($productName, 'pastry') !== false) {
            return 'Snack';
        } elseif (strpos($productName, 'bottle') !== false || 
                  strpos($productName, 'mineral') !== false) {
            return 'Bottle';
        }
        
        return 'Coffee'; // Default
    }
}