@extends('layouts.admin')

@section('title', 'History Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Transaksi / Produk" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.orders.history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
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
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-[#4F2E22] hover:bg-opacity-5 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $transaction->transaction_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($transaction->payment_method == 'cash') bg-green-100 text-green-800 border border-green-200
                                    @elseif($transaction->payment_method == 'qris') bg-blue-100 text-blue-800 border border-blue-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($transaction->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($transaction->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $transaction->created_at->format('H:i') }}</div>
                                <div class="text-xs text-gray-400">{{ $transaction->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('admin.orders.print-receipt', $transaction) }}" target="_blank" 
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
            
            @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links('pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection