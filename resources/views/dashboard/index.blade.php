@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600">{{ now()->locale('id')->translatedFormat('l, F d Y') }}</p>
        </div>
        <div class="text-right">
            <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-600">Owner</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-r from-green-400 to-green-500 rounded-xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Total Pemasukan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($todayIncome, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-arrow-up text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-red-400 to-red-500 rounded-xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Pengeluaran</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($todayExpense, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-400 to-blue-500 rounded-xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Stok</p>
                    <p class="text-2xl font-bold">{{ $totalProducts }} Menu</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Statistics Line Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Grafik Penjualan</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="flex justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
                    <span class="text-sm text-gray-600">Penjualan Bulanan</span>
                </div>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Kategori Penjualan</h3>
            <div class="relative h-64">
                <canvas id="donutChart"></canvas>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-800">{{ $categorySales['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Coffee</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Non Coffee</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Coffee Milk</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Snack</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Bottle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Seller Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Best Seller</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($bestSellersToday->take(3) as $item)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                            <p class="text-sm text-gray-600">Rp {{ number_format($item->price ?? 17000, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ $item->total_sold }} Terjual</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">Americano</p>
                            <p class="text-sm text-gray-600">Rp 17.000</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">12 Terjual</p>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">Cappuccino</p>
                            <p class="text-sm text-gray-600">Rp 18.000</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">10 Terjual</p>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">Latte</p>
                            <p class="text-sm text-gray-600">Rp 20.000</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">8 Terjual</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Penjualan Bulanan',
                    data: {!! json_encode($incomeData) !!},
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }
            ]
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
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Donut Chart
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categorySales['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($categorySales['data'] ?? []) !!},
                backgroundColor: [
                    'rgb(168, 85, 247)',
                    'rgb(34, 197, 94)',
                    'rgb(234, 179, 8)',
                    'rgb(249, 115, 22)',
                    'rgb(59, 130, 246)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    function confirmDeactivate(id) {
        if(confirm('Apakah Anda yakin ingin menonaktifkan karyawan ini?')) {
            // Submit form deactivate
            document.getElementById('deactivate-form-' + id).submit();
        }
    }
</script>
@endsection