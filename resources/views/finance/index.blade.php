@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pemasukan Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($todayIncome, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.finance.daily') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pemasukan Bulan Ini</p>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($monthIncome, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.finance.monthly') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pemasukan Tahun Ini</p>
                    <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($yearIncome, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.finance.yearly') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Chart (This Month) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Pemasukan Harian - {{ now()->format('F Y') }}</h3>
            <div class="h-64">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- Monthly Chart (This Year) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Pemasukan Bulanan - {{ now()->format('Y') }}</h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Statistik Hari Ini</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Total Transaksi</span>
                    <span class="font-semibold">{{ \App\Models\Order::whereDate('created_at', now())->where('status', 'done')->count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Rata-rata per Transaksi</span>
                    <span class="font-semibold">
                        @php
                            $todayCount = \App\Models\Order::whereDate('created_at', now())->where('status', 'done')->count();
                            $avgToday = $todayCount > 0 ? $todayIncome / $todayCount : 0;
                        @endphp
                        Rp {{ number_format($avgToday, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-semibold">
                        @php
                            $cashToday = \App\Models\Order::whereDate('created_at', now())->where('status', 'done')->where('payment_method', 'cash')->count();
                            $qrisToday = \App\Models\Order::whereDate('created_at', now())->where('status', 'done')->where('payment_method', 'qris')->count();
                        @endphp
                        Cash: {{ $cashToday }} | QRIS: {{ $qrisToday }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Statistik Bulan Ini</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Total Transaksi</span>
                    <span class="font-semibold">{{ \App\Models\Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('status', 'done')->count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Rata-rata per Hari</span>
                    <span class="font-semibold">
                        @php
                            $daysInMonth = now()->daysInMonth;
                            $avgDaily = $daysInMonth > 0 ? $monthIncome / $daysInMonth : 0;
                        @endphp
                        Rp {{ number_format($avgDaily, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Proyeksi Akhir Bulan</span>
                    <span class="font-semibold">
                        @php
                            $dayOfMonth = now()->day;
                            $projection = $dayOfMonth > 0 ? ($monthIncome / $dayOfMonth) * $daysInMonth : 0;
                        @endphp
                        Rp {{ number_format($projection, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Akses Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.finance.daily') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                <div class="p-3 bg-blue-100 rounded-full mr-4">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <div>
                    <p class="font-semibold">Laporan Harian</p>
                    <p class="text-sm text-gray-500">Lihat detail per hari</p>
                </div>
            </a>
            <a href="{{ route('admin.finance.monthly') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                <div class="p-3 bg-green-100 rounded-full mr-4">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
                <div>
                    <p class="font-semibold">Laporan Bulanan</p>
                    <p class="text-sm text-gray-500">Rekap per bulan</p>
                </div>
            </a>
            <a href="{{ route('admin.finance.yearly') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                <div class="p-3 bg-purple-100 rounded-full mr-4">
                    <i class="fas fa-calendar text-purple-600"></i>
                </div>
                <div>
                    <p class="font-semibold">Laporan Tahunan</p>
                    <p class="text-sm text-gray-500">Rekap per tahun</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyData->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m');
            })) !!},
            datasets: [{
                label: 'Pemasukan',
                data: {!! json_encode($dailyData->pluck('total')) !!},
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

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthlyData = {!! json_encode($monthlyData) !!};
    
    // Fill missing months with 0
    const filledData = months.map((month, index) => {
        const found = monthlyData.find(item => item.month === index + 1);
        return found ? found.total : 0;
    });

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Pemasukan',
                data: filledData,
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