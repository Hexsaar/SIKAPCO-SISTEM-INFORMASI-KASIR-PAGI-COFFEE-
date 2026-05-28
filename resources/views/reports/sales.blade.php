@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Penjualan</h1>
                <p class="text-gray-600">Pantau dan analisis performa penjualan Pagicoffee</p>
            </div>
            
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
                <div class="text-xs text-gray-500">Live Update</div>
                <div class="text-sm font-semibold text-green-600">{{ now()->format('H:i:s') }}</div>
            </div>
        </div>
        
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center mb-6">
            <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] rounded-lg p-2 mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Filter Laporan</h3>
        </div>
        
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <div class="relative">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent transition-all duration-200">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <div class="relative">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent transition-all duration-200">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select name="month" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent transition-all duration-200">
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
                    <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent transition-all duration-200">
                        <option value="">Pilih Tahun</option>
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ (request('year') ?: now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] text-white px-6 py-2.5 rounded-lg hover:from-[#5a3a2e] hover:to-[#4a3020] transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Terapkan Filter
                </button>
                
                <a href="{{ route('admin.reports.sales') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </a>
                
                <div class="flex-1"></div>
                
                <a href="?date={{ today()->format('Y-m-d') }}" class="bg-purple-100 text-purple-700 px-4 py-2.5 rounded-lg hover:bg-purple-200 transition-all duration-200 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Hari Ini
                </a>
                
                <a href="?month={{ now()->month }}&year={{ now()->year }}" class="bg-indigo-100 text-indigo-700 px-4 py-2.5 rounded-lg hover:bg-indigo-200 transition-all duration-200 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Bulan Ini
                </a>
                
                <a href="?year={{ now()->year }}" class="bg-teal-100 text-teal-700 px-4 py-2.5 rounded-lg hover:bg-teal-200 transition-all duration-200 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Tahun Ini
                </a>
                
                <a href="{{ route('admin.reports.sales.pdf') }}?{{ http_build_query(request()->all()) }}" class="bg-red-500 text-white px-4 py-2.5 rounded-lg hover:bg-red-600 transition-all duration-200 flex items-center text-sm shadow-md" target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    PDF
                </a>
                
                <a href="{{ route('admin.reports.sales.excel') }}?{{ http_build_query(request()->all()) }}" class="bg-green-500 text-white px-4 py-2.5 rounded-lg hover:bg-green-600 transition-all duration-200 flex items-center text-sm shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v2a2 2 0 002 2h6a2 2 0 002-2v-2M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M9 17l6-6m0 0l6 6m-6-6v12"></path>
                    </svg>
                    Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-green-600 text-sm font-medium">+12.5%</div>
            </div>
            <div class="text-gray-600 text-sm mb-1">Total Pendapatan</div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="text-blue-600 text-sm font-medium">+8.2%</div>
            </div>
            <div class="text-gray-600 text-sm mb-1">Total Order</div>
            <div class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="text-purple-600 text-sm font-medium">+5.7%</div>
            </div>
            <div class="text-gray-600 text-sm mb-1">Rata-rata Order</div>
            <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($averageOrder, 0, ',', '.') }}</div>
        </div>
        
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-500 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-orange-600 text-sm font-medium">+15.3%</div>
            </div>
            <div class="text-gray-600 text-sm mb-1">Menu Terjual</div>
            <div class="text-2xl font-bold text-gray-900">{{ $bestSellers->sum('total_sold') }}</div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Detail Transaksi</h3>
                        <div class="text-white/80 text-sm">
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
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ORDER ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">PEMBAYARAN</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">STATUS</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">TOTAL</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">WAKTU</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-[#4F2E22]/10 rounded-lg p-2 mr-3">
                                    <svg class="w-4 h-4 text-[#4F2E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $order->transaction_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($order->payment_method == 'cash') bg-green-100 text-green-800 border border-green-200
                                @elseif($order->payment_method == 'qris') bg-blue-100 text-blue-800 border border-blue-200
                                @else bg-gray-100 text-gray-800 border border-gray-200
                                @endif">
                                @if($order->payment_method == 'cash')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($order->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-gray-100 text-gray-800 border border-gray-200
                                @endif">
                                @if($order->status == 'completed')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                {{ strtoupper($order->status ?? 'completed') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">{{ $order->created_at->format('H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.orders.print-receipt', $order) }}" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#4F2E22] to-[#3f251b] text-white text-sm font-bold rounded-lg hover:from-[#5a3a2e] hover:to-[#4a3020] transition-all duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada transaksi</h3>
                                <p class="text-sm text-gray-500 max-w-md">Transaksi akan muncul di sini setelah pelanggan melakukan pembayaran.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            {{ $orders->links('pagination.custom') }}
        </div>
        @endif
    </div>
</div>
@endsection
