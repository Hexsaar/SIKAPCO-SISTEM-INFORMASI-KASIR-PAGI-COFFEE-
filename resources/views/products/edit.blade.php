{{-- Edit Product Modal --}}
<div id="editProductModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Edit Product</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Preview Card -->
                <div class="mb-6 p-4 bg-slate-50 rounded-xl">
                    <p class="text-sm text-gray-500 mb-3">Preview:</p>
                    <div class="menu-card flex flex-col items-center rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm max-w-[200px] mx-auto">
                        <img id="editImagePreview" src="https://via.placeholder.com/150" 
                             alt="Preview" 
                             class="h-[90px] md:h-[110px] w-[75px] md:w-[90px] rounded-lg border border-slate-200 object-cover" />
                        <div id="editNamePreview" class="mt-1.5 text-xs md:text-[13px] font-semibold">Product Name</div>
                        <div id="editPricePreview" class="mb-1 text-[11px] md:text-[12px] font-bold text-amber-700">Rp 0</div>
                        <div id="editStockPreview" class="rounded-full border border-dashed border-slate-200 px-2 py-0.5 text-[10px] text-slate-500">Stok tersedia</div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input type="text" name="name" id="editNameInput" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22] focus:border-transparent"
                               placeholder="e.g., V60" required>
                        <div id="editNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="editCategorySelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]" required>
                            <option value="">Select Category</option>
                        </select>
                        <div id="editCategoryError" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>

                    <!-- Price and Stock -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (Rp)</label>
                            <input type="number" name="price" id="editPriceInput" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                   placeholder="17000" min="0" required>
                            <div id="editPriceError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" id="editStockInput" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                   placeholder="8" min="0" required>
                            <div id="editStockError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                        <input type="file" name="image" id="editImageInput" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]">
                        <div id="editImageError" class="text-red-500 text-xs mt-1 hidden"></div>
                        <p class="text-xs text-gray-500 mt-1">Recommended: Square image (JPG, PNG, GIF max 2MB)</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea name="description" id="editDescriptionInput" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]"
                                  placeholder="Product description..."></textarea>
                        <div id="editDescriptionError" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="closeEditModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="editSubmitBtn"
                                class="flex-1 bg-[#4F2E22] text-white px-4 py-2 rounded-lg hover:bg-[#3e251b] transition-colors">
                            Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let currentEditProductId = null;
let categories = [];

// Initialize categories from the page
document.addEventListener('DOMContentLoaded', function() {
    // Extract categories from the create modal select
    const createSelect = document.querySelector('select[x-model="form.category_id"]');
    if (createSelect) {
        const options = createSelect.querySelectorAll('option');
        categories = Array.from(options).map(option => ({
            id: option.value,
            name: option.textContent
        })).filter(cat => cat.id !== '');
    }
});

// Open edit modal
function openEditModal(productId) {
    currentEditProductId = productId;
    
    // Fetch product data
    fetch(`/api/products/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.data;
                
                // Populate form
                document.getElementById('editNameInput').value = product.name;
                document.getElementById('editPriceInput').value = product.price;
                document.getElementById('editStockInput').value = product.stock;
                document.getElementById('editDescriptionInput').value = product.description || '';
                
                // Populate categories
                const categorySelect = document.getElementById('editCategorySelect');
                categorySelect.innerHTML = '<option value="">Select Category</option>';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    if (category.id == product.category_id) {
                        option.selected = true;
                    }
                    categorySelect.appendChild(option);
                });
                
                // Update preview
                updateEditPreview();
                document.getElementById('editImagePreview').src = product.image_url || 'https://via.placeholder.com/150';
                
                // Clear any previous errors
                clearEditErrors();
                
                // Show modal
                document.getElementById('editProductModal').classList.remove('hidden');
                document.getElementById('editProductModal').classList.add('flex');
            } else {
                showToast('Failed to load product data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load product data', 'error');
        });
}

// Close edit modal
function closeEditModal() {
    document.getElementById('editProductModal').classList.add('hidden');
    document.getElementById('editProductModal').classList.remove('flex');
    currentEditProductId = null;
    clearEditErrors();
}

// Clear errors
function clearEditErrors() {
    const errorElements = document.querySelectorAll('[id$="Error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });
}

// Update preview
function updateEditPreview() {
    const name = document.getElementById('editNameInput').value || 'Product Name';
    const price = document.getElementById('editPriceInput').value ? parseInt(document.getElementById('editPriceInput').value).toLocaleString('id-ID') : '0';
    const stock = parseInt(document.getElementById('editStockInput').value) || 0;
    
    document.getElementById('editNamePreview').textContent = name;
    document.getElementById('editPricePreview').textContent = `Rp ${price}`;
    
    const stockPreview = document.getElementById('editStockPreview');
    if (stock > 10) {
        stockPreview.textContent = 'Stok tersedia';
    } else if (stock > 0) {
        stockPreview.textContent = `Stok tersisa ${stock}`;
    } else {
        stockPreview.textContent = 'Habis';
    }
}

// Handle form submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentEditProductId) return;
    
    const submitBtn = document.getElementById('editSubmitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Loading...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch(`/admin/products/${currentEditProductId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product updated successfully!', 'success');
            closeEditModal();
            location.reload();
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`edit${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                showToast(data.message || 'Failed to update product', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update product', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Real-time preview update
document.getElementById('editNameInput').addEventListener('input', updateEditPreview);
document.getElementById('editPriceInput').addEventListener('input', updateEditPreview);
document.getElementById('editStockInput').addEventListener('input', updateEditPreview);

document.getElementById('editImageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editImagePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Clear errors on input
['editNameInput', 'editPriceInput', 'editStockInput', 'editCategorySelect', 'editDescriptionInput', 'editImageInput'].forEach(id => {
    document.getElementById(id).addEventListener('input', function() {
        const errorElement = document.getElementById(id.replace('Input', '').replace('Select', '') + 'Error');
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    });
});
</script>
