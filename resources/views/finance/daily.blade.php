@extends('layouts.admin')

@section('title', 'Laporan Keuangan Harian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Laporan Keuangan Harian</h2>
                <p class="text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('finance.daily', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </a>
                <a href="{{ route('finance.daily', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Berikutnya <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('finance.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
            <p class="text-sm text-gray-600">Rata-rata per Transaksi</p>
            <p class="text-3xl font-bold text-purple-600">
                Rp {{ number_format($count > 0 ? $total / $count : 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>
            @php
                $cashTotal = $orders->where('payment_method', 'cash')->sum('total');
                $qrisTotal = $orders->where('payment_method', 'qris')->sum('total');
                $cashCount = $orders->where('payment_method', 'cash')->count();
                $qrisCount = $orders->where('payment_method', 'qris')->count();
            @endphp
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Cash</span>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Rp {{ number_format($cashTotal, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">{{ $cashCount }} transaksi</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>QRIS</span>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">{{ $qrisCount }} transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Jam Sibuk</h3>
            @php
                $hourlyData = $orders->groupBy(function($order) {
                    return $order->created_at->format('H');
                })->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'total' => $group->sum('total')
                    ];
                })->sortKeys();
            @endphp
            <div class="space-y-2">
                @foreach($hourlyData as $hour => $data)
                    <div class="flex items-center">
                        <span class="w-16 text-sm">{{ $hour }}:00 - {{ $hour }}:59</span>
                        <div class="flex-1 mx-2">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 rounded-full h-2" style="width: {{ ($data['count'] / max($hourlyData->max('count'), 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-medium">{{ $data['count'] }} transaksi</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Daftar Transaksi</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[540px]">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="p-3">Waktu</th>
                        <th class="p-3">No. Order</th>
                        <th class="p-3">Items</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $order->created_at->format('H:i') }}</td>
                        <td class="p-3">{{ $order->order_number }}</td>
                        <td class="p-3">
                            @foreach($order->items as $item)
                                <div>{{ $item->product_name }} ({{ $item->quantity }}x)</div>
                            @endforeach
                        </td>
                        <td class="p-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_method == 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $order->user->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">Tidak ada transaksi pada hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection