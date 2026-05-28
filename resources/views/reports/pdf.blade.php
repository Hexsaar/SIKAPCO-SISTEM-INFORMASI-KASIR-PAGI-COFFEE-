<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - Pagicoffee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4F2E22;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #4F2E22;
        }
        
        .header h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: normal;
            color: #666;
        }
        
        .header-info {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .summary-table td {
            padding: 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }
        
        .summary-table td:first-child {
            font-weight: bold;
            width: 150px;
            background: #f0f0f0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background: #4F2E22;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #4F2E22;
        }
        
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .order-number {
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }
        
        .items {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .total {
            font-weight: bold;
            color: #4F2E22;
        }
        
        .payment {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAGICOFFEE</h1>
        <h2>LAPORAN PENJUALAN</h2>
        <div class="header-info">
            <strong>Periode:</strong> {{ request('start_date') ?: 'Semua' }} - {{ request('end_date') ?: 'Semua' }}<br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table class="summary-table">
        <tr>
            <td>Total Pendapatan</td>
            <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Order</td>
            <td>{{ $totalOrders }} Transaksi</td>
        </tr>
    </table>

    <table class="data-table">
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
                <td class="order-number">{{ $order->transaction_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->user->name }}</td>
                <td class="items">
                    @php
                        $itemsText = [];
                        foreach($order->items as $item) {
                            $product = \App\Models\Product::find($item['id']);
                            $productName = $product ? $product->name : 'Product Deleted';
                            $itemsText[] = $productName . ' (' . $item['quantity'] . 'x)';
                        }
                        echo implode("\n", $itemsText);
                    @endphp
                </td>
                <td class="total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td class="payment">{{ $order->payment_method }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <strong>Dicetak oleh:</strong> {{ Auth::user()->name }} | {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>