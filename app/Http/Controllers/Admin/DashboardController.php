<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Expense;
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
                
            $monthlyExpense = Expense::whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
                
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
        $productIds = [];
        
        foreach ($transactions as $transaction) {
            if (is_array($transaction->items)) {
                foreach ($transaction->items as $item) {
                    $productId = $item['id'] ?? null;
                    $productName = $item['product_name'] ?? null;
                    $quantity = $item['quantity'] ?? 0;
                    
                    if ($productId) {
                        $key = 'id:' . $productId;
                        if (!isset($productSales[$key])) {
                            $productSales[$key] = [
                                'total_sold' => 0,
                                'product_id' => $productId,
                                'product_name' => $productName,
                                'price' => 0,
                            ];
                        }
                        $productSales[$key]['total_sold'] += $quantity;
                        $productIds[] = $productId;
                    } else {
                        $key = $productName ?: 'Unknown';
                        if (!isset($productSales[$key])) {
                            $productSales[$key] = [
                                'total_sold' => 0,
                                'product_id' => null,
                                'product_name' => $productName ?: 'Unknown',
                                'price' => 0,
                            ];
                        }
                        $productSales[$key]['total_sold'] += $quantity;
                    }
                }
            }
        }
        
        // Ambil harga produk dari database untuk setiap produk yang terjual
        if (!empty($productIds)) {
            $products = Product::whereIn('id', array_unique($productIds))->get()->keyBy('id');
            foreach ($productSales as &$data) {
                if ($data['product_id'] && isset($products[$data['product_id']])) {
                    $product = $products[$data['product_id']];
                    $data['price'] = $product->price;
                    $data['product_name'] = $product->name;
                }
            }
        }
        
        foreach ($productSales as $key => &$data) {
            if (empty($data['product_name'])) {
                $data['product_name'] = 'Unknown';
            }
        }
        
        // Sort by total_sold (descending)
        uasort($productSales, function($a, $b) {
            return $b['total_sold'] - $a['total_sold'];
        });
        
        $topSellers = array_slice($productSales, 0, 5, true);
        
        // Convert to collection for consistency
        $result = collect();
        foreach ($topSellers as $data) {
            $result->push((object) [
                'name' => $data['product_name'] ?? 'Unknown',
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
                    $productId = $item['id'] ?? null;
                    $productName = $item['product_name'] ?? null;
                    $quantity = $item['quantity'] ?? 0;

                    $product = null;
                    if ($productId) {
                        $product = Product::with('category')->find($productId);
                    }

                    if (!$product && $productName) {
                        $product = Product::with('category')->where('name', $productName)->first();
                    }

                    if ($product && $product->category) {
                        $categoryName = $product->category->name;
                    } else {
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