@extends('layouts.admin')

@section('title', 'Laporan Keuangan Tahunan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Laporan Keuangan Tahunan</h2>
                <p class="text-gray-600">{{ $year }}</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex space-x-2">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Transaksi</p>
            <p class="text-3xl font-bold text-blue-600">{{ $count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Pemasukan</p>
            <p class="text-3xl font-bold text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Rata-rata per Bulan</p>
            <p class="text-3xl font-bold text-purple-600">
                Rp {{ number_format($count > 0 ? $total / 12 : 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Pemasukan Bulanan</h3>
        <div class="h-80">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Rekap Bulanan</h3>
        </div>
        <div class="p-6">
            @php
                $monthlyTotals = $orders->groupBy(function($order) {
                    return $order->created_at->format('m');
                })->map(function($monthOrders) {
                    return [
                        'count' => $monthOrders->count(),
                        'total' => $monthOrders->sum('total')
                    ];
                });
                
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            @endphp
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">Bulan</th>
                        <th class="p-3">Jumlah Transaksi</th>
                        <th class="p-3">Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($months as $index => $monthName)
                        @php
                            $monthNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                            $data = $monthlyTotals[$monthNum] ?? ['count' => 0, 'total' => 0];
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $monthName }}</td>
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
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthlyData = {!! json_encode($monthlyTotals) !!};
    
    // Fill missing months with 0
    const filledData = months.map((month, index) => {
        const monthNum = String(index + 1).padStart(2, '0');
        return monthlyData[monthNum] ? monthlyData[monthNum].total : 0;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Pemasukan',
                data: filledData,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
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