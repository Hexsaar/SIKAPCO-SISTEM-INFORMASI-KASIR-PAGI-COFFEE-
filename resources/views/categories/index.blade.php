@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold text-[#4F2E22]">Daftar Kategori</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola kategori menu dan best seller</p>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3e251b] transition-colors shadow-sm">
                <span class="material-icons inline mr-2 text-sm">add</span>Tambah Kategori
            </a>
        </div>
    </div>

    <div class="p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-green-800 hover:text-green-900">
                    <span class="material-icons text-sm">close</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-red-800 hover:text-red-900">
                    <span class="material-icons text-sm">close</span>
                </button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Best Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Menu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $category->description ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->is_best_seller)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="material-icons text-xs mr-1">star</span>
                                        Best Seller
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Regular
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $category->products->count() }} menu
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#4F2E22] hover:text-[#3e251b]">
                                        <span class="material-icons text-sm">edit</span>
                                    </a>
                                    @if($category->products->count() == 0)
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <span class="material-icons text-sm">delete</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 cursor-not-allowed" title="Tidak dapat menghapus kategori yang memiliki menu">
                                            <span class="material-icons text-sm">delete</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection