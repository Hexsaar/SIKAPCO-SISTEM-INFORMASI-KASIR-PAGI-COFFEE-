@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="bg-white rounded-lg shadow max-w-2xl">
    <div class="p-6 border-b">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-[#4F2E22]">Tambah Kategori Baru</h2>
                <p class="text-sm text-gray-600 mt-1">Buat kategori menu baru</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                <span class="material-icons">close</span>
            </a>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                      placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        
        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <a href="{{ route('admin.categories.index') }}"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                Batal
            </a>
            <button type="submit" class="flex-1 px-4 py-2 bg-[#4F2E22] text-white rounded-lg hover:bg-[#3e251b]">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>
@endsection