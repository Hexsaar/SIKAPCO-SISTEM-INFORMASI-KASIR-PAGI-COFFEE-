<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - Pagicoffee</title>
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
        .summary-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td,
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .summary-table td:first-child {
            width: 200px;
            background: #f0f0f0;
            font-weight: bold;
        }
        .data-table th {
            background: #4F2E22;
            color: white;
            font-size: 11px;
            text-align: left;
        }
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
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
        <h2>LAPORAN KEUANGAN</h2>
        <div class="header-info">
            <strong>Periode:</strong> {{ $startDate }} - {{ $endDate }}<br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table class="summary-table">
        <tr>
            <td>Total Pemasukan Cash</td>
            <td>Rp {{ number_format($cashTransactions, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Pemasukan QRIS</td>
            <td>Rp {{ number_format($qrisTransactions, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Pemasukan</td>
            <td>Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Pengeluaran</td>
            <td>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Saldo Bersih</td>
            <td>Rp {{ number_format($netBalance, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Catatan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($expense->type) }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->notes ?: '-' }}</td>
                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Dicetak oleh:</strong> {{ Auth::user()->name }} | {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
