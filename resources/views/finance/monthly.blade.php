@extends('layouts.admin')

@section('title', 'Laporan Keuangan Bulanan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Laporan Keuangan Bulanan</h2>
                <p class="text-gray-600">{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex space-x-2">
                    <select name="month" class="border rounded-lg px-3 py-2">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="border rounded-lg px-3 py-2">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Tampilkan
                    </button>
                </form>
                <a href="{{ route('finance.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Transaksi</p>
            <p class="text-3xl font-bold text-blue-600">{{ $count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Pemasukan</p>
            <p class="text-3xl font-bold text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Rata-rata per Hari</p>
            <p class="text-3xl font-bold text-purple-600">
                Rp {{ number_format($count > 0 ? $total / now()->daysInMonth : 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Rata-rata per Transaksi</p>
            <p class="text-3xl font-bold text-orange-600">
                Rp {{ number_format($count > 0 ? $total / $count : 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Daily Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Pemasukan Harian</h3>
        <div class="h-80">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Rekap Harian</h3>
        </div>
        <div class="p-6">
            @php
                $dailyTotals = $orders->groupBy(function($order) {
                    return $order->created_at->format('Y-m-d');
                })->map(function($dayOrders) {
                    return [
                        'count' => $dayOrders->count(),
                        'total' => $dayOrders->sum('total')
                    ];
                });
            @endphp
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Jumlah Transaksi</th>
                        <th class="p-3">Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyTotals as $date => $data)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
                        <td class="p-3">{{ $data['count'] }}</td>
                        <td class="p-3">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('dailyChart').getContext('2d');
    
    // Prepare daily data
    const daysInMonth = {{ now()->daysInMonth }};
    const dailyData = {!! json_encode($dailyTotals) !!};
    
    const labels = [];
    const data = [];
    
    for(let i = 1; i <= daysInMonth; i++) {
        labels.push(i);
        const date = '{{ $year }}-' + String({{ $month }}).padStart(2, '0') + '-' + String(i).padStart(2, '0');
        data.push(dailyData[date] ? dailyData[date].total : 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pemasukan',
                data: data,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection