<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get transactions data
        $cashTransactions = Order::where('payment_method', 'cash')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_amount');

        $qrisTransactions = Order::where('payment_method', 'qris')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_amount');

        $totalIncome = $cashTransactions + $qrisTransactions;

        // Get expenses data
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // Calculate balance
        $netBalance = $totalIncome - $totalExpenses;

        return view('admin.expenses.index', compact(
            'cashTransactions',
            'qrisTransactions', 
            'totalIncome',
            'expenses',
            'totalExpenses',
            'netBalance',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'type' => 'required|in:cash,qris,other',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        Expense::create([
            'amount' => $request->amount,
            'description' => $request->description,
            'type' => $request->type,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'type' => 'required|in:cash,qris,other',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $expense->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'type' => $request->type,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
}
