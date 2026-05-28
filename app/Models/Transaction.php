<?php

namespace App\Models;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_number',
        'total_amount',
        'cash_received',
        'change_amount',
        'payment_method',
        'items',
        'status',
        'subtotal_amount',
        'total_item_discount',
        'global_discount_percent',
        'global_discount_amount',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'total_item_discount' => 'decimal:2',
        'global_discount_percent' => 'decimal:2',
        'global_discount_amount' => 'decimal:2',
        'items' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateTransactionNumber(): string
    {
        $date = now()->format('Ymd');
        $lastTransaction = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -3)) + 1 : 1;
        
        return 'ORD-IDPGICFFEE' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public static function getTodayIncome(): float
    {
        return self::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    public static function getTodayExpense(): float
    {
        return Expense::whereDate('expense_date', today())
            ->sum('amount');
    }

    public static function getTodayTransactionsCount(): int
    {
        return self::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
    }
}
