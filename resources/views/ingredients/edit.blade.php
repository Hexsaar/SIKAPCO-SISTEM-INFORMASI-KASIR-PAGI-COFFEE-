@extends('layouts.app')

@section('title', 'Edit Bahan Baku')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Bahan Baku</h1>
            <a href="{{ route('admin.ingredients.index') }}" class="text-gray-600 hover:text-gray-900">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.ingredients.update', $ingredient) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Nama Bahan Baku
                    </label>
                    <input type="text" name="name" id="name" required value="{{ $ingredient->name }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">
                            Stok Saat Ini
                        </label>
                        <input type="number" name="stock" id="stock" required step="0.01" min="0" value="{{ $ingredient->stock }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                    </div>
                    <div>
                        <label for="unit" class="block text-gray-700 text-sm font-bold mb-2">
                            Satuan
                        </label>
                        <select name="unit" id="unit" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                            <option value="kg" {{ $ingredient->unit === 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="gram" {{ $ingredient->unit === 'gram' ? 'selected' : '' }}>gram</option>
                            <option value="liter" {{ $ingredient->unit === 'liter' ? 'selected' : '' }}>liter</option>
                            <option value="ml" {{ $ingredient->unit === 'ml' ? 'selected' : '' }}>ml</option>
                            <option value="pcs" {{ $ingredient->unit === 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="pack" {{ $ingredient->unit === 'pack' ? 'selected' : '' }}>pack</option>
                            <option value="botol" {{ $ingredient->unit === 'botol' ? 'selected' : '' }}>botol</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="min_stock" class="block text-gray-700 text-sm font-bold mb-2">
                            Stok Minimum (Peringatan)
                        </label>
                        <input type="number" name="min_stock" id="min_stock" required step="0.01" min="0" value="{{ $ingredient->min_stock }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                    </div>
                    <div>
                        <label for="price_per_unit" class="block text-gray-700 text-sm font-bold mb-2">
                            Harga per Satuan (Rp)
                        </label>
                        <input type="number" name="price_per_unit" id="price_per_unit" required step="0.01" min="0" value="{{ $ingredient->price_per_unit }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $ingredient->is_active ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-gray-700 text-sm font-bold">Aktif</span>
                    </label>
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full bg-[#4F2E22] text-white py-3 rounded-lg hover:bg-[#3f251b] transition font-bold">
                        Update Bahan Baku
                    </button>
                </div>
            </form>
        </div>

        <!-- Stock Adjustment Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Adjust Stok Cepat</h2>
            <form action="{{ route('admin.ingredients.stock-adjust', $ingredient) }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">
                            Jumlah
                        </label>
                        <input type="number" name="quantity" id="quantity" required step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                    </div>
                    <div>
                        <label for="type" class="block text-gray-700 text-sm font-bold mb-2">
                            Tipe
                        </label>
                        <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                            <option value="add">Tambah Stok</option>
                            <option value="subtract">Kurangi Stok</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                    Adjust Stok
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
