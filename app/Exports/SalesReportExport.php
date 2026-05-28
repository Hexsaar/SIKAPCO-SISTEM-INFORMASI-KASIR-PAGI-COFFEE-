<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Transaction::with('user')
            ->where('status', 'completed');

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $this->request->start_date . ' 00:00:00',
                $this->request->end_date . ' 23:59:59'
            ]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'Kasir',
            'Items',
            'Total',
            'Metode Pembayaran'
        ];
    }

    public function map($transaction): array
    {
        $items = [];
        if (is_array($transaction->items)) {
            foreach ($transaction->items as $item) {
                $product = \App\Models\Product::find($item['id']);
                $productName = $product ? $product->name : 'Product Deleted';
                $items[] = $productName . ' (' . ($item['quantity'] ?? 0) . 'x)';
            }
        }
        
        return [
            $transaction->transaction_number,
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->user->name,
            implode(', ', $items),
            $transaction->total_amount,
            strtoupper($transaction->payment_method)
        ];
    }
}