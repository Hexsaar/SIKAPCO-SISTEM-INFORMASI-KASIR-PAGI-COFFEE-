@extends('layouts.admin')

@section('title', 'Kelola Menu')

@section('content')
<div class="bg-white rounded-lg shadow" x-data="productApp()">
    <div class="p-6 border-b">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-xl font-semibold text-[#4F2E22]">Daftar Menu</h2>
            <button @click="showCreateModal = true" class="bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3e251b] transition-colors shadow-sm">
                <span class="material-icons inline mr-2 text-sm">add</span>Tambah Menu
            </button>
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
        
        <!-- Search & Filter Section -->
        <div class="mb-6 bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">search</span>
                        <input type="text" 
                               x-model="searchTerm" 
                               @input="filterProducts"
                               placeholder="Cari menu berdasarkan nama..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select x-model="selectedCategory" @change="filterProducts" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Active Filters -->
            <div x-show="searchTerm || selectedCategory" class="mt-3 flex items-center gap-2">
                <span class="text-sm text-gray-600">Filter aktif:</span>
                <span x-show="searchTerm" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#4F2E22] text-white">
                    Search: <span x-text="searchTerm"></span>
                    <button @click="searchTerm = ''; filterProducts()" class="ml-1 hover:text-gray-200">
                        <span class="material-icons text-xs">close</span>
                    </button>
                </span>
                <span x-show="selectedCategory" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#4F2E22] text-white">
                    Kategori: <span x-text="categories.find(c => c.id == selectedCategory)?.name || ''"></span>
                    <button @click="selectedCategory = ''; filterProducts()" class="ml-1 hover:text-gray-200">
                        <span class="material-icons text-xs">close</span>
                    </button>
                </span>
            </div>
        </div>
        
        <!-- CREATE MODAL -->
        <div class="fixed inset-0 bg-black/50 modal-overlay z-40 flex items-center justify-center" x-show="showCreateModal" @click.self="showCreateModal = false">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4 modal-overlay max-h-[90vh] overflow-y-auto" @click.stop="">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Tambah Menu Baru</h3>
                        <button @click="showCreateModal = false" class="text-gray-500 hover:text-gray-700">
                            <span class="material-icons">close</span>
                        </button>
                    </div>

                    <!-- Preview Card -->
                    <div class="mb-6 p-4 bg-slate-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-3">Preview:</p>
                        <div class="menu-card flex flex-col items-center rounded-lg border border-slate-200 bg-white p-3 text-center max-w-[200px] mx-auto">
                            <img id="createImagePreview" :src="form.imagePreview" alt="Preview" class="h-24 w-20 rounded-lg border border-slate-200 object-cover">
                            <div class="mt-2 text-sm font-semibold text-gray-800" x-text="form.name || 'Menu Name'"></div>
                            <div class="text-xs text-amber-700 font-bold" x-text="'Rp ' + (form.price ? parseInt(form.price).toLocaleString('id-ID') : '0')"></div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="createProduct()" class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                            <input type="text" x-model="form.name" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" placeholder="Contoh: Americano" required>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select x-model="form.category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price and Stock -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                <input type="number" x-model="form.price" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" placeholder="17000" min="0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                <input type="number" x-model="form.stock" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" placeholder="10" min="0" required>
                            </div>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Menu</label>
                            <input type="file" @change="handleImageChange" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Ukuran rekomendasi: Square (JPG, PNG - max 2MB)</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                            <textarea x-model="form.description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" placeholder="Deskripsi menu..."></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-4">
                            <button type="button" @click="showCreateModal = false" class="flex-1 px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#4F2E22] text-white rounded-lg hover:bg-[#3e251b]" :disabled="isLoading">
                                <span x-show="!isLoading">Tambah Menu</span>
                                <span x-show="isLoading">Loading...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Table -->
        <div class="overflow-x-auto -mx-6 rounded-lg border border-gray-200 shadow-sm">
        <table class="w-full min-w-[640px]">
            <thead class="bg-gradient-to-r from-[#4F2E22] to-[#3e251b] text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Menu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Harga</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Stok</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi Stok</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-product-id="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-lg object-cover mr-3 border border-gray-200" 
                                 src="{{ $product->image_url ?? 'https://via.placeholder.com/40' }}" 
                                 alt="{{ $product->name }}">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($product->description ?? '', 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $product->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm font-semibold text-[#4F2E22]">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <span class="text-sm font-medium {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $product->stock }}
                            </span>
                            @if($product->stock <= 5 && $product->stock > 0)
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low
                                </span>
                            @elseif($product->stock <= 0)
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-2 h-2 mr-1 rounded-full {{ $product->is_available ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $product->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                            <button onclick="toggleAvailability({{ $product->id }}, event)" 
                                    class="text-[#4F2E22] hover:text-[#3e251b] transition-colors duration-150" 
                                    title="Toggle Availability">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   value="{{ $product->stock }}" 
                                   min="0" 
                                   class="w-16 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent" 
                                   id="stock-{{ $product->id }}">
                            <button onclick="updateStock({{ $product->id }})" 
                                    class="text-green-600 hover:text-green-700 transition-colors duration-150" 
                                    title="Update Stock">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="openEditModal({{ $product->id }})" 
                                    class="text-[#4F2E22] hover:text-[#3e251b] transition-colors duration-150" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-700 transition-colors duration-150" 
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus menu ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <!-- Pagination with Info -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Data Info -->
            <div class="text-sm text-gray-600 order-2 sm:order-1">
                Menampilkan 
                <span class="font-semibold text-gray-900">
                    {{ ($products->currentPage() - 1) * $products->perPage() + 1 }}
                </span>
                sampai 
                <span class="font-semibold text-gray-900">
                    {{ min($products->currentPage() * $products->perPage(), $products->total()) }}
                </span>
                dari 
                <span class="font-semibold text-gray-900">
                    {{ $products->total() }}
                </span>
                menu
            </div>

            <!-- Pagination Links -->
            <div class="order-1 sm:order-2">
                {{ $products->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>

@include('products.edit')

<script>
function fetchWithTimeout(url, options = {}, timeout = 6000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    return fetch(url, {
        ...options,
        signal: controller.signal
    }).finally(() => clearTimeout(timer));
}

function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-[9999] space-y-2';
        document.body.appendChild(container);
    }

    const toneMap = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        info: 'bg-slate-800'
    };

    const toast = document.createElement('div');
    toast.className = `${toneMap[type] || toneMap.info} text-white px-4 py-2 rounded-lg shadow-lg text-sm opacity-0 translate-y-[-6px] transition-all duration-200`;
    toast.textContent = message;

    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-[-6px]');
    });

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-[-6px]');
        setTimeout(() => toast.remove(), 220);
    }, 2200);
}

function productApp() {
    return {
        showCreateModal: false,
        isLoading: false,
        searchTerm: '',
        selectedCategory: '',
        categories: @json($categories->map(function($cat) { return ['id' => $cat->id, 'name' => $cat->name]; })),
        form: {
            name: '',
            category_id: '',
            price: '',
            stock: '',
            description: '',
            imagePreview: 'https://via.placeholder.com/150',
            imageFile: null
        },
        handleImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.imagePreview = e.target.result;
                    this.form.imageFile = file;
                };
                reader.readAsDataURL(file);
            }
        },
        createProduct() {
            this.isLoading = true;
            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('category_id', this.form.category_id);
            formData.append('price', this.form.price);
            formData.append('stock', this.form.stock);
            formData.append('description', this.form.description);
            if (this.form.imageFile) {
                formData.append('image', this.form.imageFile);
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('{{ route("admin.products.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showCreateModal = false;
                    this.form = {
                        name: '',
                        category_id: '',
                        price: '',
                        stock: '',
                        description: '',
                        imagePreview: 'https://via.placeholder.com/150',
                        imageFile: null
                    };
                    showToast('Menu berhasil ditambahkan!', 'success');
                    location.reload();
                } else {
                    showToast(data.message || 'Gagal menambahkan menu', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Gagal menambahkan menu', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },
        filterProducts() {
            const rows = document.querySelectorAll('tbody tr[data-product-id]');
            const searchTerm = this.searchTerm.toLowerCase();
            const selectedCategory = this.selectedCategory;

            rows.forEach(row => {
                const productName = row.querySelector('td:first-child .text-sm')?.textContent.toLowerCase() || '';
                const categoryId = row.dataset.categoryId || '';
                
                const matchesSearch = !searchTerm || productName.includes(searchTerm);
                const matchesCategory = !selectedCategory || categoryId === selectedCategory;
                
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }
    };
}

// Toggle product availability
async function toggleAvailability(productId, event) {
    const button = event.target.closest('button');
    if (!button) return;

    if (button.dataset.pending === '1') return;

    const row = button.closest('tr');
    const statusCell = row?.querySelector('td:nth-child(5)');
    const statusSpan = statusCell?.querySelector('span');
    const stockInput = row?.querySelector('input[type="number"]');

    const prevClass = statusSpan?.className || '';
    const prevText = statusSpan?.textContent || '';
    const prevStock = stockInput?.value;
    const wasAvailable = statusSpan?.textContent?.trim() === 'Tersedia';
    const nextAvailable = !wasAvailable;

    if (statusSpan) {
        statusSpan.className = nextAvailable
            ? 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800'
            : 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800';
        statusSpan.textContent = nextAvailable ? 'Tersedia' : 'Tidak Tersedia';
    }

    try {
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        button.dataset.pending = '1';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetchWithTimeout(`/api/products/${productId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }, 6000);

        const result = await response.json();
        
        if (result.success) {
            const isAvailable = result.data.is_available;
            
            if (statusSpan) {
                statusSpan.className = isAvailable
                    ? 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800'
                    : 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800';
                statusSpan.textContent = isAvailable ? 'Tersedia' : 'Tidak Tersedia';
            }
            
            if (stockInput) {
                stockInput.value = result.data.stock;
            }

            showToast(isAvailable ? 'Menu diaktifkan' : 'Menu dinonaktifkan', 'success');
        } else {
            if (statusSpan) {
                statusSpan.className = prevClass;
                statusSpan.textContent = prevText;
            }
            if (stockInput && prevStock !== undefined) {
                stockInput.value = prevStock;
            }
            showToast(result.message || 'Gagal mengupdate status', 'error');
        }
        
        button.innerHTML = originalIcon;
        button.disabled = false;
        delete button.dataset.pending;
        
    } catch (error) {
        console.error('Error:', error);
        if (statusSpan) {
            statusSpan.className = prevClass;
            statusSpan.textContent = prevText;
        }
        if (stockInput && prevStock !== undefined) {
            stockInput.value = prevStock;
        }
        button.innerHTML = originalIcon;
        button.disabled = false;
        delete button.dataset.pending;
        showToast('Gagal mengupdate status', 'error');
    }
}

// Update stock
async function updateStock(productId) {
    const input = document.getElementById(`stock-${productId}`);
    const newStock = parseInt(input.value);
    
    if (isNaN(newStock) || newStock < 0) {
        showToast('Stok harus berupa angka positif', 'error');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetchWithTimeout(`/api/products/${productId}/stock`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ stock: newStock })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('Stok berhasil diperbarui', 'success');
            location.reload();
        } else {
            showToast(result.message || 'Gagal memperbarui stok', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Gagal memperbarui stok', 'error');
    }
}

// Stock polling for real-time updates
(function() {
    const NORMAL = 2000;
    const SLOW = 5000;
    const IDLE_THRESHOLD = 5;
    
    let timerId = null;
    let idleCycles = 0;
    let lastVersion = 0;

    async function poll() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetchWithTimeout('/api/products/stock-version', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            
            if (result.success && result.data.version > lastVersion) {
                lastVersion = result.data.version;
                idleCycles = 0;
                location.reload();
            } else {
                idleCycles++;
            }
        } catch (e) { }

        const delay = idleCycles >= IDLE_THRESHOLD ? SLOW : NORMAL;
        timerId = setTimeout(poll, delay);
    }

    poll();
})();
</script>
@endsection
