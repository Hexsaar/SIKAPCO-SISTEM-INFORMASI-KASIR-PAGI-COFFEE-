<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Barista\BaristaController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Redirect after login based on role
Route::middleware(['auth'])->get('/redirect', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('dashboard');
    } elseif ($user->role === 'kasir') {
        return redirect()->route('kasir.index');
    } elseif ($user->role === 'barista') {
        return redirect()->route('barista.index');
    } elseif ($user->role === 'pending') {
        // Redirect pending users to kasir instead of pending page
        return redirect()->route('kasir.index');
    }
    return redirect('/login');
})->name('redirect');

// General authenticated routes
Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/toggle', [EmployeeController::class, 'toggle'])->name('employees.toggle');
    Route::get('/api/employees', [EmployeeController::class, 'apiList'])->name('api.employees');
    Route::post('/api/employees', [EmployeeController::class, 'apiStore'])->name('api.employees.store');
    Route::get('/api/employees/{employee}', [EmployeeController::class, 'apiShow'])->name('api.employees.show');
    Route::post('/api/employees/{employee}/toggle', [EmployeeController::class, 'apiToggle'])->name('api.employees.toggle');
    
    // Pending Users Management
    Route::get('/pending-users', [EmployeeController::class, 'pendingUsers'])->name('employees.pending');
    Route::post('/approve-user/{user}', [EmployeeController::class, 'approveUser'])->name('employees.approve');
    Route::delete('/reject-user/{user}', [EmployeeController::class, 'rejectUser'])->name('employees.reject');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/api/notifications/count', [NotificationController::class, 'getCount'])->name('api.notifications.count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('api.notifications.recent');
    
    // Orders
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{transaction}/print', [OrderController::class, 'printReceipt'])->name('orders.print-receipt');
    Route::get('/api/transactions/history', [OrderController::class, 'apiHistory'])->name('api.transactions.history');
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.sales.pdf');
    Route::get('/reports/sales/export-excel', [ReportController::class, 'exportExcel'])->name('reports.sales.excel');

    // Expense exports
    Route::get('/expenses/export-pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export-pdf');
    Route::get('/expenses/export-excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export-excel');
    
    // Finance
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/daily', [FinanceController::class, 'daily'])->name('finance.daily');
    Route::get('/finance/monthly', [FinanceController::class, 'monthly'])->name('finance.monthly');
    Route::get('/finance/yearly', [FinanceController::class, 'yearly'])->name('finance.yearly');
    
    // Expenses
    Route::resource('expenses', ExpenseController::class);
    
    // Ingredients
    Route::prefix('ingredients')->name('ingredients.')->group(function () {
        Route::get('/', [IngredientController::class, 'index'])->name('index');
        Route::get('/create', [IngredientController::class, 'create'])->name('create');
        Route::post('/', [IngredientController::class, 'store'])->name('store');
        Route::get('/{ingredient}/edit', [IngredientController::class, 'edit'])->name('edit');
        Route::put('/{ingredient}', [IngredientController::class, 'update'])->name('update');
        Route::delete('/{ingredient}', [IngredientController::class, 'destroy'])->name('destroy');
        Route::post('/{ingredient}/stock-adjust', [IngredientController::class, 'stockAdjust'])->name('stock-adjust');
        Route::get('/recipes', [IngredientController::class, 'recipeIndex'])->name('recipes');
        Route::post('/recipes', [IngredientController::class, 'recipeStore'])->name('recipe-store');
        Route::delete('/recipes/{recipe}', [IngredientController::class, 'recipeDestroy'])->name('recipe-destroy');
    });
});

// Kasir Routes
Route::middleware(['auth', 'role:kasir,pending'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('index');
    Route::get('/products/category/{categoryId}', [KasirController::class, 'getProductsByCategory'])->name('products.by-category');
    Route::get('/products/search', [KasirController::class, 'searchProducts'])->name('products.search');
    Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [KasirController::class, 'orders'])->name('orders');
    Route::get('/receipt/{order}', [KasirController::class, 'receipt'])->name('receipt');
    Route::get('/receipt/number/{order_number}', [KasirController::class, 'receiptByOrderNumber'])->name('receipt.by-number');
    Route::get('/api/transactions/history', [KasirController::class, 'apiHistory'])->name('api.transactions.history');
});

// User Profile Routes (All authenticated users)
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('user.profile.show');
    Route::post('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
    
    // Debug route
    Route::get('/user/profile/test', function() {
        return response()->json(['message' => 'Test route works!', 'user' => auth()->user()->name]);
    });
});

// Barista Routes
Route::middleware(['auth', 'role:barista'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('/', [BaristaController::class, 'index'])->name('index');
    Route::get('/orders', [BaristaController::class, 'orders'])->name('orders');
    Route::post('/orders/{order}/complete', [BaristaController::class, 'completeOrder'])->name('orders.complete');
});

// Pending Routes - DISABLED (redirect to kasir instead)
// Route::middleware(['auth', 'role:pending'])->prefix('pending')->name('pending.')->group(function () {
//     Route::get('/', [PendingController::class, 'index'])->name('index');
// });

// API Routes for Kasir
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::post('/transactions', [App\Http\Controllers\Api\TransactionController::class, 'store']);
    Route::get('/transactions/today-stats', [App\Http\Controllers\Api\TransactionController::class, 'todayStats']);
    
    // Product API for real-time stock
    Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::get('/products/stock-version', [App\Http\Controllers\Api\ProductController::class, 'stockVersion']);
    Route::post('/products/{id}/toggle-availability', [App\Http\Controllers\Api\ProductController::class, 'toggleAvailability']);
    Route::put('/products/{id}/stock', [App\Http\Controllers\Api\ProductController::class, 'updateStock']);
    Route::get('/products/stock-stats', [App\Http\Controllers\Api\ProductController::class, 'getStockStats']);
    
    // Stock stream for real-time updates
    Route::get('/stock-stream', [App\Http\Controllers\Api\StockStreamController::class, 'stream']);
    Route::post('/stock-stream/trigger', [App\Http\Controllers\Api\StockStreamController::class, 'triggerUpdate']);
});

// Fallback dashboard route (for backward compatibility)
Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';