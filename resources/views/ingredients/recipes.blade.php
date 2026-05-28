@extends('layouts.app')

@section('title', 'Kelola Resep Menu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Resep Menu</h1>
        <a href="{{ route('admin.ingredients.index') }}" class="text-gray-600 hover:text-gray-900">
            Kembali ke Bahan Baku
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Add Recipe Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Tambah Resep Baru</h2>
        <form action="{{ route('admin.ingredients.recipe-store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">
                        Menu
                    </label>
                    <select name="product_id" id="product_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                        <option value="">Pilih Menu</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="ingredient_id" class="block text-gray-700 text-sm font-bold mb-2">
                        Bahan Baku
                    </label>
                    <select name="ingredient_id" id="ingredient_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">
                        Jumlah yang Dibutuhkan
                    </label>
                    <input type="number" name="quantity" id="quantity" required step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                        placeholder="Contoh: 0.1">
                </div>
            </div>
            <button type="submit" class="bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3f251b] transition">
                + Tambah Resep
            </button>
        </form>
    </div>

    <!-- Recipes Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bahan Baku</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recipes as $recipe)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $recipe->product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $recipe->ingredient->name }}</div>
                            <div class="text-xs text-gray-500">Stok: {{ number_format($recipe->ingredient->stock, 2) }} {{ $recipe->ingredient->unit }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($recipe->quantity, 2) }} {{ $recipe->ingredient->unit }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.ingredients.recipe-destroy', $recipe) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus resep ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Belum ada resep
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
