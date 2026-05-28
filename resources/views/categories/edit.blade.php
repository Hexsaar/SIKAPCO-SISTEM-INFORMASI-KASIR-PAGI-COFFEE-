@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="bg-white rounded-lg shadow max-w-2xl">
    <div class="p-6 border-b">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-[#4F2E22]">Edit Kategori</h2>
                <p class="text-sm text-gray-600 mt-1">Ubah informasi kategori</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                <span class="material-icons">close</span>
            </a>
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent"
                   placeholder="Contoh: Kopi" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent"
                      placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        
        <!-- Product Count Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <span class="material-icons text-blue-600 mr-2">info</span>
                <div>
                    <p class="text-sm font-medium text-blue-800">Informasi Menu</p>
                    <p class="text-sm text-blue-600">Kategori ini memiliki {{ $category->products->count() }} menu</p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <a href="{{ route('admin.categories.index') }}"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                Batal
            </a>
            <button type="submit" class="flex-1 px-4 py-2 bg-[#4F2E22] text-white rounded-lg hover:bg-[#3e251b]">
                Update Kategori
            </button>
        </div>
    </form>
</div>
@endsection