@extends('layouts.admin')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('admin.expenses.index') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"></path>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.expenses.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Laporan Keuangan</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-700">Edit Pengeluaran</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="mt-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Edit Pengeluaran</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui data pengeluaran bisnis Anda</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.expenses.update', $expense) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Amount Field -->
                        <div class="sm:col-span-2">
                            <label for="amount" class="block text-sm font-medium text-gray-700">
                                Jumlah Pengeluaran <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       step="0.01" 
                                       min="0" 
                                       required
                                       value="{{ $expense->amount }}"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600" id="amount-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type Field -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Tipe Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select id="type" 
                                    name="type" 
                                    required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Pilih tipe pembayaran</option>
                                <option value="cash" {{ $expense->type == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                <option value="qris" {{ $expense->type == 'qris' ? 'selected' : '' }}>📱 QRIS</option>
                                <option value="other" {{ $expense->type == 'other' ? 'selected' : '' }}>💳 Lainnya</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600" id="type-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Field -->
                        <div>
                            <label for="expense_date" class="block text-sm font-medium text-gray-700">
                                Tanggal Pengeluaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="expense_date" 
                                   name="expense_date" 
                                   required
                                   value="{{ $expense->expense_date->format('Y-m-d') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('expense_date')
                                <p class="mt-2 text-sm text-red-600" id="expense_date-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="description" 
                                   name="description" 
                                   required
                                   maxlength="255"
                                   value="{{ $expense->description }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Contoh: Beli kertas, Listrik, ATK, dll">
                            @error('description')
                                <p class="mt-2 text-sm text-red-600" id="description-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes Field -->
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Catatan
                            </label>
                            <div class="mt-1">
                                <textarea id="notes" 
                                          name="notes" 
                                          rows="4" 
                                          maxlength="500"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                                          placeholder="Tambahkan catatan atau detail tambahan jika diperlukan...">{{ $expense->notes ?? '' }}</textarea>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span id="charCount">{{ strlen($expense->notes ?? '') }}</span> / 500 karakter
                                </div>
                            </div>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600" id="notes-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 sm:grid sm:grid-cols-2 sm:gap-3">
                    <a href="{{ route('admin.expenses.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-1">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    
    // Format amount input
    amountInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove non-numeric characters except decimal point
        value = value.replace(/[^\d.]/g, '');
        
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (parts.length === 2 && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        
        e.target.value = value;
    });
});
</script>
@endsection
