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
                <a href="?date={{ today()->format('Y-m-d') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                    <i class="fas fa-calendar-day mr-2"></i>Hari Ini
                </a>
                <a href="?month={{ now()->month }}&year={{ now()->year }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-calendar-alt mr-2"></i>Bulan Ini
                </a>
                <a href="?year={{ now()->year }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700">
                    <i class="fas fa-calendar mr-2"></i>Tahun Ini
                </a>
                <a href="{{ route('admin.reports.sales.pdf') }}?{{ http_build_query(request()->all()) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </a>
                <a href="{{ route('admin.reports.sales.excel') }}?{{ http_build_query(request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>Excel
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

    
    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-bold">{{ $orders->count() }}</span> dari 
                    <span class="font-bold">{{ $orders->total() }}</span> transaksi
                    @if(request()->filled('date'))
                        (Hari Ini: {{ request('date') }})
                    @elseif(request()->filled('month'))
                        (Bulan {{ DateTime::createFromFormat('!m', request('month'))->format('F') }} {{ request('year') }})
                    @elseif(request()->filled('year'))
                        (Tahun {{ request('year') }})
                    @else
                        (Hari Ini)
                    @endif
                </div>
            </div>
        </div>
        <div class="p-6 overflow-x-auto">
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">ORDER ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">PEMBAYARAN</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">STATUS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">TOTAL</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">WAKTU</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">CETAK BUKTI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                        <tr class="hover:bg-[#4F2E22] hover:bg-opacity-5 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $order->transaction_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($order->payment_method == 'cash') bg-green-100 text-green-800 border border-green-200
                                    @elseif($order->payment_method == 'qris') bg-blue-100 text-blue-800 border border-blue-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($order->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($order->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($order->status ?? 'completed') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $order->created_at->format('H:i') }}</div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('admin.orders.print-receipt', $order) }}" target="_blank" 
                                   class="inline-flex items-center px-4 py-2 bg-[#4F2E22] text-white text-sm font-bold rounded-lg hover:bg-[#3f251b] transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Cetak
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h3>
                                <p class="text-sm text-gray-500">Transaksi akan muncul di sini setelah pelanggan melakukan pembayaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links('pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
