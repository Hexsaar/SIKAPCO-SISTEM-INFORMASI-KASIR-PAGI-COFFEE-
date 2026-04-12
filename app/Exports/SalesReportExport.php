<?php

namespace App\Exports;

use App\Models\Order;
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
        $query = Order::with(['items.product', 'user'])
            ->where('status', 'done');

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
            'No. Order',
            'Tanggal',
            'Kasir',
            'Total Item',
            'Subtotal',
            'Pajak',
            'Total',
            'Metode Pembayaran'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->user->name,
            $order->items->sum('quantity'),
            $order->subtotal,
            $order->tax,
            $order->total,
            strtoupper($order->payment_method)
        ];
    }
}