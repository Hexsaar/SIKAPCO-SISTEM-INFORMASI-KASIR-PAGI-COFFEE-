{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-slate-200">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Preview Card (real-time preview seperti card) -->
                <div class="mb-8 p-4 bg-slate-50 rounded-xl">
                    <p class="text-sm text-gray-500 mb-3">Preview:</p>
                    <div class="menu-card flex flex-col items-center rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm max-w-[200px] mx-auto">
                        <img id="imagePreview" src="https://via.placeholder.com/150" 
                             alt="Preview" 
                             class="h-[90px] md:h-[110px] w-[75px] md:w-[90px] rounded-lg border border-slate-200 object-cover" />
                        <div id="namePreview" class="mt-1.5 text-xs md:text-[13px] font-semibold">Product Name</div>
                        <div id="pricePreview" class="mb-1 text-[11px] md:text-[12px] font-bold text-amber-700">Rp 0</div>
                        <div id="stockPreview" class="rounded-full border border-dashed border-slate-200 px-2 py-0.5 text-[10px] text-slate-500">Stok tersedia</div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input type="text" name="name" id="nameInput" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent"
                               placeholder="e.g., V60" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Stock (side by side) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (Rp)</label>
                            <input type="number" name="price" id="priceInput" value="{{ old('price') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                   placeholder="17000" min="0" required>
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" id="stockInput" value="{{ old('stock', 0) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                   placeholder="8" min="0" required>
                            @error('stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                        <input type="file" name="image" id="imageInput" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Recommended: Square image (JPG, PNG, GIF max 2MB)</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                  placeholder="Product description...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    
                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3e251b] transition-colors">
                            Create Product
                        </button>
                        <a href="{{ route('products.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Real-time preview update
    const nameInput = document.getElementById('nameInput');
    const priceInput = document.getElementById('priceInput');
    const stockInput = document.getElementById('stockInput');
    const imageInput = document.getElementById('imageInput');
    
    const namePreview = document.getElementById('namePreview');
    const pricePreview = document.getElementById('pricePreview');
    const stockPreview = document.getElementById('stockPreview');
    const imagePreview = document.getElementById('imagePreview');

    nameInput.addEventListener('input', function() {
        namePreview.textContent = this.value || 'Product Name';
    });

    priceInput.addEventListener('input', function() {
        const price = this.value ? parseInt(this.value).toLocaleString('id-ID') : '0';
        pricePreview.textContent = `Rp ${price}`;
    });

    stockInput.addEventListener('input', function() {
        const stock = parseInt(this.value) || 0;
        if (stock > 10) {
            stockPreview.textContent = 'Stok tersedia';
        } else if (stock > 0) {
            stockPreview.textContent = `Stok tersisa ${stock}`;
        } else {
            stockPreview.textContent = 'Habis';
        }
    });

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection