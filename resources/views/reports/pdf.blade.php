<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9fafb;
        }
        .summary-item {
            margin: 5px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Penjualan</p>
        <p>Periode: {{ request('start_date') ?: 'Semua' }} - {{ request('end_date') ?: 'Semua' }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item"><strong>Total Pendapatan:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="summary-item"><strong>Total Order:</strong> {{ $totalOrders }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->user->name }}</td>
                <td>
                    @foreach($order->items as $item)
                        {{ $item->product_name }} ({{ $item->quantity }}x){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>
                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} | {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>