@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Laporan</h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select name="month" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Pilih Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select name="year" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Pilih Tahun</option>
                    @foreach(range(now()->year, now()->year - 5) as $y)
                        <option value="{{ $y }}" {{ (request('year') ?: now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2 md:col-span-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Reset
                </a>
                <a href="{{ route('admin.reports.sales.pdf') }}?{{ http_build_query(request()->all()) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('admin.reports.sales.excel') }}?{{ http_build_query(request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Pendapatan</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Order</p>
            <p class="text-2xl font-bold text-blue-600">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Rata-rata Order</p>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($averageOrder, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Menu Terjual</p>
            <p class="text-2xl font-bold text-orange-600">{{ $bestSellers->sum('total_sold') }}</p>
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Menu Terlaris (Best Seller)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">Nama Menu</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Total Terjual</th>
                        <th class="p-3">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bestSellers as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $item->name }}</td>
                        <td class="p-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="p-3">{{ $item->total_sold }}x</td>
                        <td class="p-3">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Grafik Penjualan 30 Hari Terakhir</h3>
        <div class="h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Detail Transaksi</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">No. Order</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Kasir</th>
                        <th class="p-3">Items</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $order->order_number }}</td>
                        <td class="p-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3">{{ $order->user->name }}</td>
                        <td class="p-3">
                            @foreach($order->items as $item)
                                <div>{{ $item->product_name }} ({{ $item->quantity }}x)</div>
                            @endforeach
                        </td>
                        <td class="p-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->payment_method == 'cash') bg-green-100 text-green-800
                                @elseif($order->payment_method == 'qris') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m');
            })) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($dailySales->pluck('total_revenue')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
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