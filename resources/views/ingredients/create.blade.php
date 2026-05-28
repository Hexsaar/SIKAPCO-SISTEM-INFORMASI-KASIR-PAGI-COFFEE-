@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Tambah Bahan Baku</h1>
            <a href="{{ route('admin.ingredients.index') }}" class="text-gray-600 hover:text-gray-900">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.ingredients.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Nama Bahan Baku
                    </label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                        placeholder="Contoh: Kopi, Gula, Susu">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">
                            Stok Awal
                        </label>
                        <input type="number" name="stock" id="stock" required step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="unit" class="block text-gray-700 text-sm font-bold mb-2">
                            Satuan
                        </label>
                        <select name="unit" id="unit" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                            <option value="kg">kg</option>
                            <option value="gram">gram</option>
                            <option value="liter">liter</option>
                            <option value="ml">ml</option>
                            <option value="pcs">pcs</option>
                            <option value="pack">pack</option>
                            <option value="botol">botol</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="min_stock" class="block text-gray-700 text-sm font-bold mb-2">
                            Stok Minimum (Peringatan)
                        </label>
                        <input type="number" name="min_stock" id="min_stock" required step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="price_per_unit" class="block text-gray-700 text-sm font-bold mb-2">
                            Harga per Satuan (Rp)
                        </label>
                        <input type="number" name="price_per_unit" id="price_per_unit" required step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                            placeholder="0">
                    </div>
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full bg-[#4F2E22] text-white py-3 rounded-lg hover:bg-[#3f251b] transition font-bold">
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
