
  // ==================== FUNGSIONALITAS JAVASCRIPT ====================
  
  // Data struktur order
  let orders = [];
  const orderListEl = document.getElementById('order-list-container');
  
  // Global variables for discount and notes
  let globalDiscount = 0; // Global discount percentage
  let orderNotes = ''; // Order notes
  let orderCounter = 1; // Counter for sequential order ID
  
  // Get total elements
  const subtotalTextEl = document.getElementById('subtotal-val');
  const totalTextEl = document.getElementById('total-val');
  
  // Cart badge element
  const cartBadge = document.getElementById('cart-badge');
  
  // Fungsi untuk update badge cart
  function updateCartBadge() {
    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
    const count = savedOrders.length;
    
    if (count > 0) {
      cartBadge.textContent = count;
      cartBadge.classList.remove('hidden');
    } else {
      cartBadge.classList.add('hidden');
    }
  }
  
  // Menu item click handler
  document.querySelectorAll('.menu-card').forEach(card => {
    card.addEventListener('click', (e) => {
      const productData = JSON.parse(card.dataset.product);
      const name = productData.name;
      const price = parseInt(productData.price);
      const stock = parseInt(productData.stock);
      const productId = parseInt(productData.id);
      const isAvailable = productData.is_available === true;
      
      // Cek availability dan stok
      if (!isAvailable || stock <= 0) {
        showModal('Menu Tidak Tersedia', 'Maaf, menu ini tidak tersedia!', 'warning');
        return;
      }
      
      // Cek if item sudah ada
      const existingOrder = orders.find(o => o.name === name);
      
      if (existingOrder) {
        // Cek stok tersisa
        const totalQty = orders.reduce((sum, o) => o.name === name ? sum + o.qty : sum, 0);
        if (totalQty < stock) {
          existingOrder.qty++;
        } else {
          showModal('Stok Tidak Mencukupi', 'Stok tidak mencukupi!', 'error');
          return;
        }
      } else {
        orders.push({ 
          productId: productId,
          name: name, 
          price: price, 
          qty: 1, 
          stock: stock,
          discount: 0 // Individual item discount percentage
        });
      }
      
      renderOrderList();
    });
  });

  // Real-time stock update function with enhanced loading
  async function updateProductStock(productId, newStock, isAvailable, action = 'unknown') {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (!productCard) return;

    // Add loading animation immediately
    productCard.classList.add('stock-update-loading');
    
    // Show appropriate sync indicator based on action
    let syncMessage = 'Updating stock...';
    if (action === 'admin_update') {
      syncMessage = 'Admin updating stock...';
    } else if (action === 'transaction_update') {
      syncMessage = 'Processing transaction...';
    } else if (action === 'poll_update') {
      syncMessage = 'Syncing stock...';
    } else if (action === 'manual_update') {
      syncMessage = 'Manual refresh...';
    }
    
    showSyncIndicator(syncMessage, 'updating');

    // Update data attributes
    productCard.dataset.productStock = newStock;
    productCard.dataset.productAvailable = isAvailable ? 'true' : 'false';
    
    // Update product data in dataset
    const productData = JSON.parse(productCard.dataset.product);
    productData.stock = newStock;
    productData.is_available = isAvailable;
    productCard.dataset.product = JSON.stringify(productData);

    // Faster loading delay (300ms for better visual feedback)
    await new Promise(resolve => setTimeout(resolve, 300));

    // Update stock display
    const stockDiv = productCard.querySelector('.rounded-full.border');
    if (stockDiv) {
      if (!isAvailable || newStock <= 0) {
        stockDiv.className = 'rounded-full border border-red-200 bg-red-50 text-red-600 font-semibold px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = !isAvailable ? 'Tidak Tersedia' : 'Stok Habis';
        
        // Disable the card
        productCard.style.opacity = '0.5';
        productCard.style.pointerEvents = 'none';
        productCard.style.filter = 'grayscale(100%)';
      } else if (newStock <= 5) {
        stockDiv.className = 'rounded-full border border-orange-200 bg-orange-50 text-orange-600 px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = `Stok tersisa ${newStock}`;
        
        // Enable the card
        productCard.style.opacity = '1';
        productCard.style.pointerEvents = 'auto';
        productCard.style.filter = 'none';
      } else {
        stockDiv.className = 'rounded-full border border-dashed border-slate-200 text-slate-500 px-2 py-0.5 md:py-1 text-[10px] md:text-[11px]';
        stockDiv.textContent = `Stok tersisa ${newStock}`;
        
        // Enable the card
        productCard.style.opacity = '1';
        productCard.style.pointerEvents = 'auto';
        productCard.style.filter = 'none';
      }
    }

    // Remove loading animation
    productCard.classList.remove('stock-update-loading');
    
    // Show success message based on action
    let successMessage = 'Stock updated!';
    if (action === 'admin_update') {
      successMessage = 'Admin update completed!';
    } else if (action === 'transaction_update') {
      successMessage = 'Transaction processed!';
    } else if (action === 'poll_update') {
      successMessage = 'Stock synced!';
    } else if (action === 'manual_update') {
      successMessage = 'Manual refresh completed!';
    }
    
    showSyncIndicator(successMessage, 'success');
    
    // Hide sync indicator faster (1.5 seconds for better visibility)
    setTimeout(() => {
      hideSyncIndicator();
    }, 1500);
  }

  // Version-based fast polling (no persistent connections — compatible with php artisan serve)
  let lastStockVersion = 0;
  let pollTimerId = null;
  let idleCycles = 0;
  const POLL_FAST_MS   = 400;   // right after a change
  const POLL_NORMAL_MS = 800;   // default
  const POLL_SLOW_MS   = 2000;  // after several idle cycles
  const IDLE_SLOW_THRESHOLD = 4;

  async function pollStockVersion() {
    try {
      const res = await fetch('/api/products/stock-version', { cache: 'no-store' });
      const json = await res.json();
      const version = json.version || 0;

      if (lastStockVersion === 0) {
        // First run — baseline, no fetch
        lastStockVersion = version;
        idleCycles = 0;
      } else if (version !== lastStockVersion) {
        lastStockVersion = version;
        idleCycles = 0;
        // Fetch full product list only when something changed
        const prodRes = await fetch('/api/products', { cache: 'no-store' });
        const prodJson = await prodRes.json();
        if (prodJson.success) {
          prodJson.data.forEach(p => {
            updateProductStock(p.id, p.stock, p.is_available, 'poll_update');
          });
        }
      } else {
        idleCycles++;
      }
    } catch (e) {
      // swallow errors silently — will retry
    }

    // Adaptive interval: go slow when nothing is happening
    const delay = idleCycles >= IDLE_SLOW_THRESHOLD ? POLL_SLOW_MS : POLL_NORMAL_MS;
    pollTimerId = setTimeout(pollStockVersion, delay);
  }

  function listenForStockUpdates() {
    lastStockVersion = 0;
    idleCycles = 0;
    pollStockVersion();
  }

  function stopStockUpdates() {
    if (pollTimerId !== null) {
      clearTimeout(pollTimerId);
      pollTimerId = null;
    }
  }

  // After a successful transaction, immediately update the kasir UI without waiting for the next poll
  function applyLocalStockUpdateAfterTransaction(items) {
    items.forEach(item => {
      const card = document.querySelector(`[data-product-id="${item.product_id}"]`);
      if (!card) return;
      const currentStock = parseInt(card.dataset.productStock) || 0;
      const newStock = Math.max(0, currentStock - item.quantity);
      updateProductStock(item.product_id, newStock, newStock > 0, 'transaction_update');
    });
  }

  // Sync indicator functions
  function showSyncIndicator(text, type = 'updating') {
    const indicator = document.getElementById('syncIndicator');
    const syncText = document.getElementById('syncText');
    
    syncText.textContent = text;
    indicator.className = `sync-indicator show ${type}`;
  }

  function hideSyncIndicator() {
    const indicator = document.getElementById('syncIndicator');
    indicator.classList.remove('show');
  }

  // Manual refresh function (hanya untuk testing - real-time does this automatically)
  async function refreshStockNow() {
    try {
      showSyncIndicator('Manual refresh...', 'updating');
      
      // Force refresh from API
      const currentHost = window.location.hostname;
      const currentPort = window.location.port || '80';
      const serverUrl = `${window.location.protocol}//${currentHost}:${currentPort}`;
      
      const response = await fetch(`${serverUrl}/api/products`);
      const result = await response.json();
      
      if (result.success) {
        result.data.forEach(product => {
          updateProductStock(product.id, product.stock, product.is_available_for_sale, 'manual_update');
        });
        console.log('🔄 Manual refresh completed');
        
        showSyncIndicator('Refresh completed!', 'success');
        setTimeout(() => {
          hideSyncIndicator();
        }, 1500);
      }
    } catch (error) {
      console.error('Error refreshing stock:', error);
      hideSyncIndicator();
    }
  }

  // Add keyboard shortcut untuk manual refresh (Ctrl+R)
  document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'r') {
      e.preventDefault();
      refreshStockNow();
    }
  });

  // Start listening for stock updates when page loads
  listenForStockUpdates();

  // Stop listening when page unloads
  window.addEventListener('beforeunload', stopStockUpdates);
  
  function renderOrderList() {
    // Kosongkan container
    orderListEl.innerHTML = '';
    
    if (orders.length === 0) {
      // Tampilkan pesan kosong
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'text-center text-slate-400 py-8';
      emptyDiv.textContent = 'Belum ada pesanan';
      orderListEl.appendChild(emptyDiv);
      updateTotal();
      return;
    }
    
    // Render setiap order
    orders.forEach((order, index) => {
      const rowDiv = document.createElement('div');
      rowDiv.className = 'flex items-center justify-between text-sm';
      
      const itemTotal = order.price * order.qty * (1 - order.discount/100);
      
      // Buat struktur HTML seperti contoh asli
      rowDiv.innerHTML = `
        <div class="flex items-center gap-2 flex-1">
          <svg class="w-5 h-5 text-red-500 cursor-pointer delete-order" data-index="${index}" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z"/>
          </svg>
          <div class="flex-1">
            <div class="font-medium">${order.name}</div>
            ${order.discount > 0 ? `<div class="text-xs text-green-600">Diskon: ${order.discount}%</div>` : ''}
          </div>
        </div>
        <div class="flex items-center gap-1 w-[110px] justify-center">
          <button class="w-6 h-6 rounded-full border flex items-center justify-center active:scale-95 transition-transform qty-dec" data-index="${index}">−</button>
          <span class="qty-display px-2">${order.qty}</span>
          <button class="w-6 h-6 rounded-full border flex items-center justify-center active:scale-95 transition-transform qty-inc" data-index="${index}">+</button>
        </div>
        <div class="flex items-center gap-1 w-[140px] justify-end">
          <button class="w-6 h-6 rounded-full border flex items-center justify-center active:scale-95 transition-transform discount-btn" data-index="${index}" title="Diskon">
            <span class="text-xs">%</span>
          </button>
          <div class="text-right">
            ${order.discount > 0 ? 
              `<div class="text-xs text-gray-400 line-through">Rp ${(order.price * order.qty).toLocaleString('id-ID')}</div>
               <div class="font-medium text-green-600">Rp ${itemTotal.toLocaleString('id-ID')}</div>` :
              `<div class="font-medium">Rp ${itemTotal.toLocaleString('id-ID')}</div>`
            }
          </div>
        </div>
      `;
      
      orderListEl.appendChild(rowDiv);
    });
    
    // Attach event listeners untuk delete
    document.querySelectorAll('.delete-order').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          orders.splice(index, 1);
          renderOrderList();
        }
      });
    });
    
    // Attach event listeners untuk increment
    document.querySelectorAll('.qty-inc').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          const order = orders[index];
          const totalQty = orders.reduce((sum, o) => o.name === order.name ? sum + o.qty : sum, 0);
          if (totalQty < order.stock) {
            orders[index].qty++;
          } else {
            showModal('Stok Tidak Mencukupi', 'Stok tidak mencukupi!', 'error');
          }
          renderOrderList();
        }
      });
    });
    
    // Attach event listeners untuk decrement
    document.querySelectorAll('.qty-dec').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          if (orders[index].qty > 1) {
            orders[index].qty--;
          } else {
            orders.splice(index, 1);
          }
          renderOrderList();
        }
      });
    });
    
    // Attach event listeners untuk discount
    document.querySelectorAll('.discount-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        if (!isNaN(index) && index >= 0 && index < orders.length) {
          showDiscountModal(index, 'item');
        }
      });
    });
    
    updateTotal();
  }
  
  function updateTotal() {
    console.log('Debug - updateTotal called');
    
    if (orders.length === 0) {
      if (totalTextEl) totalTextEl.textContent = 'Rp 0';
      return 0;
    }

    const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty * (1 - o.discount/100)), 0);
    const discountPercent = parseFloat(document.getElementById('global-discount-input').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax-input').value) || 0;
    
    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    const taxAmount = afterDiscount * (taxPercent / 100);
    const total = afterDiscount + taxAmount;

    console.log('Debug - updateTotal calculation:', { subtotal, discountPercent, taxPercent, total });

    // Update global variable for backend
    globalDiscount = discountPercent;
    
    // Update total display
    if (totalTextEl) totalTextEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    return total;
  }
  
  // Global discount button handler
  const globalDiscountBtn = document.getElementById('global-discount-btn');
  if (globalDiscountBtn) {
    globalDiscountBtn.addEventListener('click', () => {
      showDiscountModal(null, 'global');
    });
  }

  // Function to show universal modal (replace alert)
  function showModal(title, message, type = 'info') {
    // Check existing modal
    if (document.getElementById('universal-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'universal-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    let icon, iconColor, buttonColor;
    switch(type) {
      case 'success':
        icon = '✓';
        iconColor = 'text-green-500';
        buttonColor = 'bg-green-500 hover:bg-green-600';
        break;
      case 'error':
        icon = '✗';
        iconColor = 'text-red-500';
        buttonColor = 'bg-red-500 hover:bg-red-600';
        break;
      case 'warning':
        icon = '⚠';
        iconColor = 'text-yellow-500';
        buttonColor = 'bg-yellow-500 hover:bg-yellow-600';
        break;
      default:
        icon = 'ℹ';
        iconColor = 'text-blue-500';
        buttonColor = 'bg-blue-500 hover:bg-blue-600';
    }

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-[400px] shadow-2xl">
        <div class="text-center">
          <div class="text-5xl ${iconColor} mb-4">${icon}</div>
          <h2 class="text-xl font-bold mb-3 text-gray-800">${title}</h2>
          <p class="text-gray-600 mb-6">${message}</p>
          <button id="modal-ok-btn" class="${buttonColor} text-white px-6 py-3 rounded-lg font-medium transition-colors">
            OK
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // OK button
    document.getElementById('modal-ok-btn').addEventListener('click', () => {
      modal.remove();
    });

    // Close on outside click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }

  // Function to show discount modal
  function showDiscountModal(itemIndex, type) {
    // Check existing modal
    const modalId = type === 'global' ? 'global-discount-modal' : 'item-discount-modal';
    if (document.getElementById(modalId)) return;

    const modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    let title, currentDiscount, itemName;
    if (type === 'global') {
      title = 'Diskon Keseluruhan';
      currentDiscount = globalDiscount;
      itemName = '';
    } else {
      title = 'Diskon Item';
      currentDiscount = orders[itemIndex].discount || 0;
      itemName = orders[itemIndex].name;
    }

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-[400px] shadow-2xl">
        <h2 class="text-xl font-bold mb-4 text-[#4F2E22]">
          <span class="material-symbols-outlined align-middle mr-2">sell</span>
          ${title}
        </h2>
        ${itemName ? `<p class="text-sm text-gray-600 mb-4">${itemName}</p>` : ''}
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan Diskon (%)</label>
            <div class="relative">
              <input 
                type="number" 
                id="discount-input" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] text-lg font-semibold"
                min="0" 
                max="100" 
                step="1" 
                value="${currentDiscount}"
                placeholder="0"
              >
              <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">%</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Masukkan angka 0-100</p>
          </div>
          
          <!-- Quick discount buttons -->
          <div>
            <p class="text-sm font-medium text-gray-700 mb-2">Cepat:</p>
            <div class="grid grid-cols-5 gap-2">
              <button class="quick-discount-btn px-3 py-2 bg-gray-100 hover:bg-[#4F2E22] hover:text-white rounded-lg text-sm font-medium transition-colors" data-discount="0">0%</button>
              <button class="quick-discount-btn px-3 py-2 bg-gray-100 hover:bg-[#4F2E22] hover:text-white rounded-lg text-sm font-medium transition-colors" data-discount="5">5%</button>
              <button class="quick-discount-btn px-3 py-2 bg-gray-100 hover:bg-[#4F2E22] hover:text-white rounded-lg text-sm font-medium transition-colors" data-discount="10">10%</button>
              <button class="quick-discount-btn px-3 py-2 bg-gray-100 hover:bg-[#4F2E22] hover:text-white rounded-lg text-sm font-medium transition-colors" data-discount="15">15%</button>
              <button class="quick-discount-btn px-3 py-2 bg-gray-100 hover:bg-[#4F2E22] hover:text-white rounded-lg text-sm font-medium transition-colors" data-discount="20">20%</button>
            </div>
          </div>
        </div>
        
        <div class="flex gap-3 mt-6">
          <button id="cancel-discount-btn" class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
            Batal
          </button>
          <button id="apply-discount-btn" class="flex-1 px-4 py-3 bg-[#4F2E22] text-white rounded-lg hover:bg-[#3f251b] font-medium transition-colors">
            Terapkan
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Focus on input
    setTimeout(() => {
      document.getElementById('discount-input').focus();
      document.getElementById('discount-input').select();
    }, 100);

    // Quick discount buttons
    document.querySelectorAll('.quick-discount-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('discount-input').value = btn.dataset.discount;
      });
    });

    // Apply discount
    document.getElementById('apply-discount-btn').addEventListener('click', () => {
      const discountInput = document.getElementById('discount-input');
      const discount = parseFloat(discountInput.value);
      
      if (!isNaN(discount) && discount >= 0 && discount <= 100) {
        if (type === 'global') {
          globalDiscount = discount;
        } else {
          orders[itemIndex].discount = discount;
        }
        updateTotal();
        renderOrderList();
        modal.remove();
      } else {
        discountInput.classList.add('border-red-500');
        discountInput.classList.add('animate-pulse');
        setTimeout(() => {
          discountInput.classList.remove('border-red-500');
          discountInput.classList.remove('animate-pulse');
        }, 1000);
      }
    });

    // Cancel button
    document.getElementById('cancel-discount-btn').addEventListener('click', () => {
      modal.remove();
    });

    // Close on outside click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });

    // Enter key to apply
    document.getElementById('discount-input').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        document.getElementById('apply-discount-btn').click();
      }
    });
  }

  // Order notes handler
  const orderNotesInput = document.getElementById('order-notes');
  if (orderNotesInput) {
    orderNotesInput.addEventListener('input', (e) => {
      orderNotes = e.target.value;
    });
  }

  // Discount and tax input handlers
  const globalDiscountInput = document.getElementById('global-discount-input');
  const taxInput = document.getElementById('tax-input');
  
  if (globalDiscountInput) {
    globalDiscountInput.addEventListener('input', () => {
      updateTotal();
    });
  }
  
  if (taxInput) {
    taxInput.addEventListener('input', () => {
      updateTotal();
    });
  }

  // Payment button handler
  const payBtn = document.getElementById('pay-btn');
  
  if (payBtn) {
    payBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        showModal('Pesanan Kosong', 'Tambahkan pesanan terlebih dahulu!', 'warning');
        return;
      }
      showPaymentMethodModal();
    });
  }
  
  function showPaymentMethodModal() {
    console.log('Debug - showPaymentMethodModal called');
    
    // Check existing modal
    if (document.getElementById('payment-method-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'payment-method-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    // Get totals the same way as getTotalsFromContainer
    const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty * (1 - o.discount/100)), 0);
    const discountPercent = parseFloat(document.getElementById('global-discount-input').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax-input').value) || 0;
    
    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    const taxAmount = afterDiscount * (taxPercent / 100);
    const total = afterDiscount + taxAmount;

    console.log('Debug - Payment modal totals:', { subtotal, discountPercent, taxPercent, total });

    modal.innerHTML = `
      <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl transform transition-all scale-100">
        <h2 class="text-2xl font-bold text-center mb-2">Total: Rp ${total.toLocaleString('id-ID')}</h2>
        <p class="text-center text-gray-500 mb-6">Pilih Metode Pembayaran</p>
        <div class="flex gap-4">
          <button class="flex-1 flex flex-col items-center gap-2 bg-amber-50 border-2 border-[#4F2E22] text-[#4F2E22] hover:bg-[#4F2E22] hover:text-white p-4 rounded-xl transition-colors" id="cash-method-btn">
            <span class="material-symbols-outlined text-3xl">payments</span>
            <span class="font-bold">CASH</span>
          </button>
          <button class="flex-1 flex flex-col items-center gap-2 bg-amber-50 border-2 border-[#4F2E22] text-[#4F2E22] hover:bg-[#4F2E22] hover:text-white p-4 rounded-xl transition-colors" id="qris-method-btn">
            <span class="material-symbols-outlined text-3xl">qr_code_scanner</span>
            <span class="font-bold">QRIS</span>
          </button>
        </div>
        <button class="mt-6 w-full py-2 text-gray-400 hover:text-gray-600" id="close-payment-method-modal">Batal</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Get totals from container inputs
    function getTotalsFromContainer() {
      const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty * (1 - o.discount/100)), 0);
      const discountPercent = parseFloat(document.getElementById('global-discount-input').value) || 0;
      const taxPercent = parseFloat(document.getElementById('tax-input').value) || 0;
      
      const discountAmount = subtotal * (discountPercent / 100);
      const afterDiscount = subtotal - discountAmount;
      const taxAmount = afterDiscount * (taxPercent / 100);
      const finalTotal = afterDiscount + taxAmount;
      
      const totals = { subtotal, discountPercent, discountAmount, taxPercent, taxAmount, finalTotal };
      console.log('Debug - getTotalsFromContainer:', totals);
      
      return totals;
    }
    
    document.getElementById('cash-method-btn').addEventListener('click', () => {
      console.log('Debug - Cash button clicked');
      try {
        const totals = getTotalsFromContainer();
        console.log('Debug - Calling showCashPaymentModal with:', totals);
        modal.remove();
        showCashPaymentModal(totals);
      } catch (error) {
        console.error('Debug - Error in cash button:', error);
        alert('Error: ' + error.message);
      }
    });
    
    document.getElementById('qris-method-btn').addEventListener('click', () => {
      console.log('Debug - QRIS button clicked');
      try {
        const totals = getTotalsFromContainer();
        console.log('Debug - Calling printReceipt with:', totals);
        modal.remove();
        // Untuk QRIS langsung ke struk (tanpa input uang)
        printReceipt('QRIS', null, totals);
      } catch (error) {
        console.error('Debug - Error in QRIS button:', error);
        alert('Error: ' + error.message);
      }
    });

    document.getElementById('close-payment-method-modal').addEventListener('click', () => {
      modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }
  
  // Fungsi untuk format Rupiah
  function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
  
  // Fungsi untuk parse Rupiah ke number
  function parseRupiah(rupiah) {
    if (!rupiah) return 0;
    // Hapus "Rp " dan titik, lalu konversi ke number
    return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
  }
  
  function showCashPaymentModal(totals = null) {
    console.log('Debug - showCashPaymentModal called with:', totals);
    
    try {
      // Check existing modal
      if (document.getElementById('cash-payment-modal')) {
        console.log('Debug - Cash payment modal already exists');
        return;
      }

      console.log('Debug - Creating cash payment modal');
      
      // Simple total calculation
      const total = updateTotal();
      console.log('Debug - Using simple total:', total);

      // Create modal element
      const modal = document.createElement('div');
      modal.id = 'cash-payment-modal';
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      
      // Simple HTML without template literals
      modal.innerHTML = '<div class="bg-white rounded-2xl p-6 w-96 shadow-2xl">' +
        '<h2 class="text-xl font-bold text-center mb-4">Pembayaran Tunai</h2>' +
        '<div class="mb-4 p-4 bg-slate-50 rounded-xl">' +
        '<div class="flex justify-between mb-2">' +
        '<span class="text-slate-600">Total</span>' +
        '<span class="font-bold text-lg">Rp ' + total.toLocaleString('id-ID') + '</span>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="block text-sm text-slate-600 mb-1">Uang diterima</label>' +
        '<input type="text" id="cash-amount" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-lg font-bold" placeholder="Rp 0">' +
        '</div>' +
        '<div class="flex justify-between text-lg border-t pt-3">' +
        '<span class="text-slate-600">Uang kembali</span>' +
        '<span id="change-amount" class="font-bold text-green-600">Rp 0</span>' +
        '</div>' +
        '</div>' +
        '<div class="flex gap-3">' +
        '<button class="flex-1 bg-blue-500 text-white font-bold py-3 rounded-xl" id="continue-cash-btn">Bayar</button>' +
        '<button class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl" id="cancel-cash-btn">Batal</button>' +
        '</div>' +
        '</div>';
    
      console.log('Debug - Appending cash payment modal to body');
      document.body.appendChild(modal);
      
      // Simple event handlers
      document.getElementById('continue-cash-btn').onclick = function() {
        console.log('Debug - Continue button clicked');
        modal.remove();
        printReceipt('CASH', total, null);
      };
      
      document.getElementById('cancel-cash-btn').onclick = function() {
        console.log('Debug - Cancel button clicked');
        modal.remove();
      };
      
      modal.onclick = function(e) {
        if (e.target === modal) modal.remove();
      };
    
    console.log('Debug - Cash payment modal setup completed');
    
    } catch (error) {
      console.error('Debug - Error in showCashPaymentModal:', error);
      alert('Error: ' + error.message);
    }
  }
  
  function printReceipt(method, cashReceived = null, totals = null) {
    console.log('Debug - printReceipt called with:', { method, cashReceived, totals });
    
    try {
      // Get totals from container
      const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty * (1 - o.discount/100)), 0);
      const discountPercent = parseFloat(document.getElementById('global-discount-input').value) || 0;
      const taxPercent = parseFloat(document.getElementById('tax-input').value) || 0;
      
      const discountAmount = subtotal * (discountPercent / 100);
      const afterDiscount = subtotal - discountAmount;
      const taxAmount = afterDiscount * (taxPercent / 100);
      const total = afterDiscount + taxAmount;
      
      console.log('Debug - Receipt totals:', { subtotal, discountPercent, discountAmount, taxPercent, taxAmount, total });
      
      const now = new Date();
      const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
      const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
      const orderId = 'ORD-IDPGICFFEE' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + '-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
      
      // Build receipt HTML
      let receiptHTML = '<div class="bg-white rounded-2xl p-6 w-96 max-h-[80vh] overflow-y-auto shadow-2xl print-receipt">' +
        '<div class="text-center mb-6">' +
        '<h1 class="text-2xl font-bold text-[#4F2E22] mb-2">Pagi Coffee</h1>' +
        '<p class="text-sm text-gray-600">Jl. Contoh No. 123, Jakarta</p>' +
        '<p class="text-sm text-gray-600">Tel: (021) 123-4567</p>' +
        '</div>' +
        
        '<div class="text-center mb-6 border-b pb-4">' +
        '<div class="text-sm text-gray-600 mb-1">No: ' + orderId + '</div>' +
        '<div class="text-sm text-gray-600 mb-1">' + dateStr + ' ' + timeStr + '</div>' +
        '<div class="font-semibold">' + method + '</div>' +
        '</div>' +
        
        '<div class="mb-4">';
      
      // Add items
      orders.forEach(order => {
        const originalItemTotal = order.price * order.qty;
        const discountedItemTotal = originalItemTotal * (1 - order.discount/100);
        const qtyStr = order.qty > 1 ? order.qty + ' x Rp ' + order.price.toLocaleString('id-ID') : '';
        
        receiptHTML += 
          '<div class="flex justify-between items-start mb-2 text-sm">' +
          '<div class="flex-1">' +
          '<div class="font-medium">' + order.name + '</div>';
        
        if (order.discount > 0) {
          receiptHTML += '<div class="text-green-600">Diskon ' + order.discount + '%</div>';
        }
        
        receiptHTML += '</div>' +
          '<div>' + qtyStr + '</div>' +
          '<div class="text-right">';
        
        if (order.discount > 0) {
          receiptHTML += 
            '<div class="line-through text-gray-400">Rp ' + originalItemTotal.toLocaleString('id-ID') + '</div>' +
            '<div class="font-semibold text-green-600">Rp ' + discountedItemTotal.toLocaleString('id-ID') + '</div>';
        } else {
          receiptHTML += '<div>Rp ' + discountedItemTotal.toLocaleString('id-ID') + '</div>';
        }
        
        receiptHTML += '</div></div>';
      });
      
      receiptHTML += '</div>';
      
      // Summary section
      receiptHTML += 
        '<div class="text-xs mb-2 space-y-1">' +
        '<div class="flex justify-between">' +
        '<span>Sub total</span>' +
        '<span class="text-right">Rp ' + subtotal.toLocaleString('id-ID') + '</span>' +
        '</div>';
      
      if (discountAmount > 0) {
        receiptHTML += 
          '<div class="flex justify-between">' +
          '<span>Diskon ' + discountPercent + '%</span>' +
          '<span class="text-right text-red-600">-Rp ' + discountAmount.toLocaleString('id-ID') + '</span>' +
          '</div>';
      }
      
      receiptHTML += '</div>';
      
      // Tax section
      if (taxAmount > 0) {
        receiptHTML += 
          '<div class="text-xs mb-4 space-y-1">' +
          '<div class="flex justify-between">' +
          '<span>Pajak ' + taxPercent + '%</span>' +
          '<span class="text-right">Rp ' + taxAmount.toLocaleString('id-ID') + '</span>' +
          '</div>' +
          '</div>';
      }
      
      receiptHTML += 
        '<div class="border-t border-dashed border-gray-400 my-2"></div>' +
        
        '<div class="text-center mb-3 font-bold text-lg">' +
        '<div>TOTAL</div>' +
        '<div class="text-2xl text-[#4F2E22]">Rp ' + total.toLocaleString('id-ID') + '</div>' +
        '</div>';
      
      // Add notes if any
      if (orderNotes.trim()) {
        receiptHTML += 
          '<div class="text-xs text-gray-600 mb-4 p-2 bg-gray-50 rounded">' +
          '<div class="font-semibold mb-1">Catatan:</div>' +
          '<div>' + orderNotes + '</div>' +
          '</div>';
      }
      
      // Payment info
      if (method === 'CASH' && cashReceived) {
        const change = cashReceived - total;
        receiptHTML += 
          '<div style="display: flex; justify-content: space-between; margin-bottom: 4px;">' +
          '<span style="font-size: 8pt;">TUNAI</span>' +
          '<span style="font-size: 8pt; text-align: right;">Rp ' + cashReceived.toLocaleString('id-ID') + '</span>' +
          '</div>' +
          '<div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 4px;">' +
          '<span style="font-size: 8pt;">KEMBALI</span>' +
          '<span style="font-size: 8pt; text-align: right;">Rp ' + change.toLocaleString('id-ID') + '</span>' +
          '</div>';
      } else {
        receiptHTML += 
          '<div style="display: flex; justify-content: space-between; margin-bottom: 4px;">' +
          '<span style="font-size: 8pt;">' + method.toUpperCase() + '</span>' +
          '<span style="font-size: 8pt; text-align: right;">Rp ' + total.toLocaleString('id-ID') + '</span>' +
          '</div>';
      }
      
      receiptHTML += 
        '<div style="text-align: center; margin-top: 10px; padding-top: 10px; border-top: 1px solid #ccc;">' +
        '<p style="font-size: 8pt; color: #666; margin-bottom: 4px;">Terima kasih atas kunjungan Anda</p>' +
        '<p style="font-size: 7pt; color: #999;">Barang yang sudah dibeli tidak dapat dikembalikan</p>' +
        '</div>' +
        
        '<div class="flex gap-3 mt-4 border-t pt-4">' +
        '<button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-md active:scale-95" id="print-close-btn">Tutup</button>' +
        '<button class="flex-1 bg-[#4F2E22] hover:bg-[#3f251b] text-white px-4 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-md active:scale-95" id="print-receipt-btn">Cetak</button>' +
        '</div>' +
        '</div>';
      
      // Create modal
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
      modal.innerHTML = receiptHTML;
      
      document.body.appendChild(modal);
      
      // Store payment data for print
      const paymentData = {
        method: method,
        cashReceived: cashReceived,
        total: total
      };
      
      // Event handlers
      document.getElementById('print-close-btn').onclick = function() {
        console.log('Debug - Receipt close button clicked');
        
        // Save transaction to database
        const finalTotals = {
          subtotal: subtotal,
          discountPercent: discountPercent,
          discountAmount: discountAmount,
          taxPercent: taxPercent,
          taxAmount: taxAmount,
          finalTotal: total
        };
        
        saveTransactionToDatabase(method, cashReceived, finalTotals);
        
        // Reset orders and UI
        orders = [];
        globalDiscount = 0;
        orderNotes = '';
        // orderCounter tidak di-reset agar terus berurutan
        
        const orderNotesInput = document.getElementById('order-notes');
        if (orderNotesInput) orderNotesInput.value = '';
        
        const discountInput = document.getElementById('global-discount-input');
        const taxInput = document.getElementById('tax-input');
        if (discountInput) discountInput.value = 0;
        if (taxInput) taxInput.value = 0;
        
        modal.remove();
        renderOrderList();
        updateTotal();
      };
      
      document.getElementById('print-receipt-btn').onclick = function() {
        console.log('Debug - Print button clicked');
        printReceiptContent(paymentData);
      };
      
      modal.onclick = function(e) {
        if (e.target === modal) {
          modal.remove();
        }
      };
      
      console.log('Debug - Receipt modal setup completed');
      
    } catch (error) {
      console.error('Debug - Error in printReceipt:', error);
      alert('Error: ' + error.message);
    }
  }
  
  // Function to print receipt content in new window
  function printReceiptContent(paymentData) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get current data for receipt
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const orderId = 'ORD-IDPGICFFEE' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + '-' + String(orderCounter).padStart(3, '0');
    
    // Increment counter for next order
    orderCounter++;
    
    // Get payment method and data
    const method = paymentData.method;
    const cashReceived = paymentData.cashReceived;
    const total = paymentData.total;
    
    // Get totals from container
    const subtotal = orders.reduce((sum, o) => sum + (o.price * o.qty * (1 - o.discount/100)), 0);
    const discountPercent = parseFloat(document.getElementById('global-discount-input').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax-input').value) || 0;
    
    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    const taxAmount = afterDiscount * (taxPercent / 100);
    
    // Build modern receipt content with tables
    let itemsHTML = '';
    orders.forEach(order => {
      const originalItemTotal = order.price * order.qty;
      const discountedItemTotal = originalItemTotal * (1 - order.discount/100);
      
      itemsHTML += `
        <tr>
          <td style="font-size: 8pt; width: 60%;">${order.qty}x ${order.name}</td>
          <td style="font-size: 8pt; width: 40%; text-align: right;">${discountedItemTotal.toLocaleString('id-ID')}</td>
        </tr>
      `;
      
      if (order.discount > 0) {
        itemsHTML += `
          <tr>
            <td style="font-size: 7pt; color: #666;">&nbsp;&nbsp;Disc ${order.discount}%</td>
            <td style="font-size: 7pt; color: #666; text-align: right;">-${(originalItemTotal - discountedItemTotal).toLocaleString('id-ID')}</td>
          </tr>
        `;
      }
    });
    
    let notesHTML = '';
    if (orderNotes.trim()) {
      notesHTML = `
        <div class="divider">--------------------------------</div>
        <table>
          <tr>
            <td colspan="2" style="font-size: 8pt; font-style: italic;">${orderNotes}</td>
          </tr>
        </table>
      `;
    }
    
    // Create modern receipt HTML
    const printHTML = `
      <!DOCTYPE html>
      <html lang="id">
      <head>
        <title>Struk Pagi Coffee</title>
        <style>
          @page { size: 58mm auto; margin: 0; }
          body {
            font-family: 'Courier New', Courier, monospace;
            width: 48mm;
            margin: 0 auto;
            padding: 5mm 0;
            font-size: 9pt;
            color: #000;
          }
          .divider {
            margin: 2mm 0;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
            letter-spacing: -1px;
          }
          table { width: 100%; border-collapse: collapse; }
          td { padding: 0.5mm 0; vertical-align: top; }
          
          @media print {
            body { width: 48mm; margin: 0 auto; }
            .no-print { display: none; }
          }
        </style>
      </head>
      <body>

        <button class="no-print" onclick="window.print()" style="margin-bottom: 20px; width: 100%; padding: 10px; cursor: pointer;">
          CETAK STRUK SEKARANG
        </button>

        <div style="text-align: center;">
          <div style="font-size: 14pt; font-weight: bold;">PAGI COFFEE</div>
          <div style="font-size: 9pt;">Jl. Contoh No. 123, Jakarta</div>
        </div>

        <div class="divider">--------------------------------</div>

        <table>
          <tr>
            <td colspan="2" style="font-size: 10pt; font-weight: bold; text-align: center;">INVOICE KASIR</td>
          </tr>
          <tr>
            <td style="font-size: 8pt;">TGL: ${dateStr}</td>
            <td style="font-size: 8pt; text-align: right;">${timeStr}</td>
          </tr>
          <tr>
            <td colspan="2" style="font-size: 8pt; text-align: center;">${orderId}</td>
          </tr>
        </table>

        <div class="divider">--------------------------------</div>

        <table>
          ${itemsHTML}
        </table>

        <div class="divider">--------------------------------</div>

        <table>
          <tr>
            <td style="font-size: 8pt; width: 60%;">SUBTOTAL</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${subtotal.toLocaleString('id-ID')}</td>
          </tr>
          ${discountAmount > 0 ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">DISKON</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">-${discountAmount.toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
          ${taxAmount > 0 ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">PAJAK (${taxPercent}%)</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${taxAmount.toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
          <tr>
            <td style="font-size: 10pt; font-weight: bold; width: 60%;">TOTAL</td>
            <td style="font-size: 10pt; font-weight: bold; width: 40%; text-align: right;">${total.toLocaleString('id-ID')}</td>
          </tr>
        </table>

        ${notesHTML}

        <div class="divider">--------------------------------</div>

        <table>
          <tr>
            <td style="font-size: 8pt; width: 60%;">${method === 'CASH' ? 'TUNAI' : method.toUpperCase()}</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${method === 'CASH' && cashReceived ? cashReceived.toLocaleString('id-ID') : total.toLocaleString('id-ID')}</td>
          </tr>
          ${method === 'CASH' && cashReceived ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">KEMBALI</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${(cashReceived - total).toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
        </table>

        ${discountAmount > 0 ? `
        <div class="divider">--------------------------------</div>
        <div style="font-size: 7pt; text-align: center;">
          * Hemat hari ini: Rp${discountAmount.toLocaleString('id-ID')} *
        </div>
        ` : ''}

        <div class="divider">--------------------------------</div>
        <div style="margin-top: 5mm; font-size: 7pt; text-align: center;">
          Terima kasih atas kunjungan Anda<br>
          Barang yang sudah dibeli tidak dapat<br>
          dikembalikan
        </div>

      </body>
      </html>
    `;
    
    printWindow.document.write(printHTML);
    printWindow.document.close();
    
    // Wait for content to load, then print
    printWindow.onload = function() {
      printWindow.print();
      printWindow.close();
    };
  }
  
  // Function to save transaction to database
  async function saveTransactionToDatabase(paymentMethod, cashReceived = null, totals = null) {
    try {
      showSyncIndicator('Processing transaction...', 'updating');
      
      // Use provided totals or fallback to container inputs
      const finalTotals = totals || {
        discountPercent: globalDiscount,
        taxPercent: parseFloat(document.getElementById('tax-input').value) || 0
      };
      
      // Prepare items data
      const items = orders.map(order => ({
        product_id: order.productId,
        quantity: order.qty,
        price: order.price,
        discount: order.discount || 0
      }));
      
      const payload = {
        items: items,
        payment_method: paymentMethod,
        cash_received: cashReceived,
        global_discount: finalTotals.discountPercent,
        tax_percentage: finalTotals.taxPercent,
        notes: orderNotes
      };
      
      const response = await fetch('/api/transactions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(payload)
      });
      
      const result = await response.json();
      
      if (!result.success) {
        console.error('Failed to save transaction:', result.message);
        showModal('Transaksi Gagal', 'Gagal menyimpan transaksi: ' + result.message, 'error');
        hideSyncIndicator();
      } else {
        console.log('Transaction saved successfully:', result.data);
        showSyncIndicator('Transaction completed!', 'success');
        
        // Immediately update stock in UI without waiting for next poll
        applyLocalStockUpdateAfterTransaction(items);

        // Hide indicator quickly after transaction
        setTimeout(() => {
          hideSyncIndicator();
        }, 800);

        // Speed up next poll so admin-side changes also reflect quickly
        idleCycles = 0;
      }
      
    } catch (error) {
      console.error('Error saving transaction:', error);
      showModal('Kesalahan', 'Terjadi kesalahan saat menyimpan transaksi', 'error');
      hideSyncIndicator();
    }
  }
  
  // Cancel button
  const cancelBtn = document.getElementById('cancel-btn');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        showModal('Tidak Ada Pesanan', 'Tidak ada pesanan untuk dibatalkan!', 'warning');
        return;
      }
      if (confirm('Batalkan seluruh pesanan?')) {
        orders = [];
        renderOrderList();
        updateTotal();
      }
    });
  }
  
  // Hold button logic
  const holdBtn = document.getElementById('hold-btn');
  if (holdBtn) {
    holdBtn.addEventListener('click', () => {
      if (orders.length === 0) {
        showModal('Tidak Ada Pesanan', 'Tidak ada pesanan untuk di-hold!', 'warning');
        return;
      }
      
      const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
      const holdId = Date.now();
      
      savedOrders.push({
        id: holdId,
        timestamp: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        items: orders.map(o => ({ ...o })), // copy array
        totalRaw: orders.reduce((sum, o) => sum + (o.price * o.qty), 0),
        globalDiscount: globalDiscount,
        notes: orderNotes
      });
      
      localStorage.setItem('heldOrders', JSON.stringify(savedOrders));
      
      showModal('Berhasil', 'Pesanan masuk ke Cart!', 'success');
      orders = [];
      renderOrderList();
      updateTotal();
      updateCartBadge(); // Update badge setelah hold
    });
  }

  // Cart Button Logic (Sidebar)
  const cartLink = document.getElementById('cart-sidebar-link');
  if (cartLink) {
    cartLink.addEventListener('click', (e) => {
      e.preventDefault();
      showCartModal();
    });
  }

  // History Button Logic (Sidebar)
  const historyLink = document.getElementById('history-sidebar-link');
  if (historyLink) {
    historyLink.addEventListener('click', (e) => {
      e.preventDefault();
      showHistoryModal();
    });
  }

  // Settings Button Logic (Sidebar)
  const settingsLink = document.getElementById('settings-sidebar-link');
  if (settingsLink) {
    settingsLink.addEventListener('click', (e) => {
      e.preventDefault();
      showSettingsModal();
    });
  }

  function showCartModal() {
    // Check existing modal
    if (document.getElementById('cart-modal')) return;

    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];

    const modal = document.createElement('div');
    modal.id = 'cart-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4';
    
    let contentHtml = '';
    
    if (savedOrders.length === 0) {
      contentHtml = `
        <div class="text-center py-12 px-6">
          <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Cart Masih Kosong</h3>
          <p class="text-gray-500 text-sm">Belum ada pesanan yang di-hold. Tekan "HOLD ORDER" untuk menyimpan pesanan sementara.</p>
        </div>
      `;
    } else {
      contentHtml = `
        <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
          <div class="bg-gradient-to-r from-[#4F2E22]/5 to-[#3f251b]/5 rounded-lg p-3 mb-4 border border-[#4F2E22]/20">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-600">Total Pesanan Disimpan</span>
              <span class="bg-[#4F2E22] text-white px-3 py-1 rounded-full text-xs font-bold">${savedOrders.length} Order</span>
            </div>
          </div>
      `;
      
      savedOrders.forEach((order, idx) => {
        const itemCount = order.items.reduce((acc, curr) => acc + curr.qty, 0);
        const hasDiscount = order.globalDiscount > 0;
        contentHtml += `
          <div class="group bg-white border-2 border-gray-100 hover:border-[#4F2E22]/50 hover:shadow-lg rounded-xl p-4 cursor-pointer transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.02]" onclick="restoreOrder(${idx})">
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[#4F2E22]/20 to-[#3f251b]/20 rounded-lg flex items-center justify-center group-hover:from-[#4F2E22]/30 group-hover:to-[#3f251b]/30 transition-colors">
                  <span class="text-[#4F2E22] font-bold text-sm">${idx + 1}</span>
                </div>
                <div>
                  <div class="font-bold text-gray-800 group-hover:text-[#4F2E22] transition-colors">Order #${idx + 1}</div>
                  <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>${order.timestamp}</span>
                    <span>•</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>${itemCount} Items</span>
                    ${hasDiscount ? `
                      <span>•</span>
                      <span class="text-green-600 font-medium">Diskon ${order.globalDiscount}%</span>
                    ` : ''}
                  </div>
                </div>
              </div>
              <button class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-red-500 hover:text-red-700 p-1" onclick="event.stopPropagation(); deleteOrder(${idx})" title="Hapus Order">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
              <div class="flex items-center gap-2">
                <div class="text-xs text-gray-500">Total:</div>
                <div class="font-bold text-lg text-gray-800">${formatRupiah(order.totalRaw)}</div>
                ${hasDiscount ? `
                  <div class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">-${order.globalDiscount}%</div>
                ` : ''}
              </div>
              <div class="flex items-center gap-2 text-xs text-[#4F2E22] font-medium group-hover:text-[#3f251b] transition-colors">
                <span>Klik untuk restore</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </div>
            </div>
          </div>
        `;
      });
      contentHtml += `</div>`;
    }

    modal.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100 opacity-100" id="cart-modal-content">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6 rounded-t-2xl relative">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-white">Hold Orders</h2>
                <p class="text-white/80 text-sm">Kelola pesanan yang ditahan</p>
              </div>
            </div>
            <button id="close-cart-btn" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          ${contentHtml}
        </div>
        
        <!-- Footer -->
        ${savedOrders.length > 0 ? `
          <div class="border-t border-gray-100 p-6 bg-gray-50 rounded-b-2xl">
            <div class="flex gap-3">
              <button onclick="clearAllOrders()" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-md active:scale-95">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Semua
              </button>
              <button onclick="document.getElementById('cart-modal').remove()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-md active:scale-95">
                Tutup
              </button>
            </div>
          </div>
        ` : ''}
      </div>
    `;

    document.body.appendChild(modal);

    document.getElementById('close-cart-btn').addEventListener('click', () => {
      modal.remove();
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }

  // Fungsi untuk menghapus order individual dari cart
  function deleteOrder(index) {
    if (confirm('Apakah Anda yakin ingin menghapus order ini?')) {
      let savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
      savedOrders.splice(index, 1);
      localStorage.setItem('heldOrders', JSON.stringify(savedOrders));
      
      // Refresh modal
      const modal = document.getElementById('cart-modal');
      if (modal) modal.remove();
      showCartModal();
      
      // Update badge
      updateCartBadge();
      
      showModal('Berhasil', 'Order berhasil dihapus dari cart', 'success');
    }
  }

  // Fungsi untuk menghapus semua orders
  function clearAllOrders() {
    if (confirm('Apakah Anda yakin ingin menghapus semua order yang ditahan?')) {
      localStorage.removeItem('heldOrders');
      
      // Refresh modal
      const modal = document.getElementById('cart-modal');
      if (modal) modal.remove();
      showCartModal();
      
      // Update badge
      updateCartBadge();
      
      showModal('Berhasil', 'Semua order berhasil dihapus dari cart', 'success');
    }
  }

  function showHistoryModal() {
    // Check existing modal
    if (document.getElementById('history-modal')) return;

    // Show loading modal first
    const modal = document.createElement('div');
    modal.id = 'history-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4';

    modal.innerHTML = `
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6 flex items-center justify-between">
          <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="material-symbols-outlined">history</span>
            Riwayat Transaksi
          </h2>
          <button onclick="document.getElementById('history-modal').remove()" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6 text-center" id="history-content">
          <div class="text-gray-500">Memuat data...</div>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Fetch data with proper error handling
    fetch('/kasir/api/transactions/history')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        const contentDiv = document.getElementById('history-content');
        
        if (!data.transactions || data.transactions.length === 0) {
          contentDiv.innerHTML = `
            <div class="text-center py-8">
              <div class="text-gray-400 text-lg">Belum ada transaksi hari ini</div>
              <div class="text-gray-400 text-sm mt-2">Transaksi akan muncul di sini setelah melakukan pembayaran</div>
            </div>
          `;
          return;
        }

        // Build table HTML
        let tableRows = data.transactions.map((transaction, index) => `
          <tr class="border-b border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#4F2E22]/10 rounded-lg flex items-center justify-center">
                  <span class="text-[#4F2E22] font-bold text-xs">${index + 1}</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-800">${transaction.transaction_number}</div>
                  <div class="text-xs text-gray-500">${transaction.user || 'System'}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 text-xs font-semibold rounded-full ${
                transaction.payment_method === 'cash' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-blue-100 text-blue-800'
              }">
                ${transaction.payment_method?.toUpperCase() || 'CASH'}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="font-bold text-gray-800">Rp ${Number(transaction.total_amount || 0).toLocaleString('id-ID')}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600">
                ${new Date(transaction.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}
              </div>
              <div class="text-xs text-gray-500">
                ${new Date(transaction.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'})}
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <button onclick="printReceiptFromHistory('${transaction.order_number}')" class="bg-[#4F2E22] hover:bg-[#3f251b] text-white px-4 py-2 rounded-lg text-sm">
                Cetak
              </button>
            </td>
          </tr>
        `).join('');

        contentDiv.innerHTML = `
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y">
                ${tableRows}
              </tbody>
            </table>
          </div>
          <div class="mt-4 text-sm text-gray-500 text-center">
            Menampilkan ${data.transactions.length} transaksi hari ini
          </div>
        `;
      })
      .catch(error => {
        console.error('Error:', error);
        const contentDiv = document.getElementById('history-content');
        contentDiv.innerHTML = `
          <div class="text-center py-8">
            <div class="text-red-500 text-lg">Gagal memuat data</div>
            <div class="text-gray-400 text-sm mt-2">Silakan coba lagi nanti</div>
          </div>
        `;
      });

    // Close on outside click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.remove();
    });
  }

  function printReceiptFromHistory(orderNumber) {
    // Open receipt in new tab for kasir order number
    const printWindow = window.open(`/kasir/receipt/number/${encodeURIComponent(orderNumber)}`, '_blank');
    if (printWindow) {
      printWindow.focus();
    }
  }

  function showSettingsModal() {
    // Check existing modal
    if (document.getElementById('settings-modal')) return;

    // Fetch current user data
    fetch('/api/user/profile')
      .then(response => response.json())
      .then(user => {
        const modal = document.createElement('div');
        modal.id = 'settings-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 p-4';
        let capturedPhotoFile = null;
        
        modal.innerHTML = `
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all duration-300 scale-100 opacity-100" id="settings-modal-content">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] p-6 relative">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-xl font-bold text-white">Pengaturan Profile</h2>
                    <p class="text-white/80 text-sm">Kelola informasi akun Anda</p>
                  </div>
                </div>
                <button id="close-settings-modal-header" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <div class="p-6 flex-1 min-h-0 overflow-y-auto custom-scrollbar">
              <form id="settings-form" class="space-y-8">
                <!-- Profile Photo Section -->
                <div class="bg-gradient-to-br from-[#4F2E22]/5 to-[#3f251b]/5 rounded-2xl p-6 border border-[#4F2E22]/10">
                  <div class="text-center">
                    <div class="relative inline-block group">
                      <div class="absolute -inset-1 bg-gradient-to-r from-[#4F2E22] to-[#3f251b] rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                      <img id="profile-preview" src="${user.profile_photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF'}" 
                           alt="Profile" class="relative w-28 h-28 rounded-full object-cover border-4 border-white shadow-xl group-hover:scale-105 transition-transform duration-300">
                      <div class="absolute bottom-0 right-0 flex gap-2">
                        <label for="profile-photo" class="bg-[#4F2E22] text-white p-3 rounded-full cursor-pointer hover:bg-[#3f251b] transition-all duration-200 hover:scale-110 shadow-lg" title="Upload Foto">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                          </svg>
                          <input type="file" id="profile-photo" name="profile_photo" accept="image/*" class="hidden">
                        </label>
                        <button type="button" id="camera-btn" class="bg-blue-600 text-white p-3 rounded-full cursor-pointer hover:bg-blue-700 transition-all duration-200 hover:scale-110 shadow-lg" title="Ambil Foto Kamera">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-4 font-medium">Upload atau ambil foto kamera</p>
                  </div>
                </div>

                <!-- Hidden camera input -->
                <input type="file" id="camera-input" accept="image/*" capture="camera" class="hidden">

                <!-- User Information -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-6">
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-[#4F2E22]/10 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-[#4F2E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Pribadi</h3>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Nama Lengkap
                      </label>
                      <input type="text" name="name" value="${user.name}" required
                             class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] transition-all duration-200 hover:border-gray-300">
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        Username
                      </label>
                      <input type="text" name="username" value="${user.username}" required
                             class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] transition-all duration-200 hover:border-gray-300">
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      Email
                    </label>
                    <input type="email" name="email" value="${user.email}" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] transition-all duration-200 hover:border-gray-300">
                  </div>
                </div>

                <!-- Password Section -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-6">
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Keamanan Password</h3>
                  </div>
                  
                  <div class="space-y-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Password Baru
                      </label>
                      <input type="password" name="password" 
                             class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] transition-all duration-200 hover:border-gray-300"
                             placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Konfirmasi Password Baru
                      </label>
                      <input type="password" name="password_confirmation" 
                             class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#4F2E22]/20 focus:border-[#4F2E22] transition-all duration-200 hover:border-gray-300"
                             placeholder="Ulangi password baru">
                    </div>
                  </div>
                </div>

                <!-- Current Password Required -->
                <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl border border-red-200 p-6">
                  <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-red-800">Konfirmasi Password Saat Ini</h3>
                  </div>
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-red-700 mb-2">Password Saat Ini *</label>
                    <input type="password" name="current_password" required
                           class="w-full border-2 border-red-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-all duration-200 hover:border-red-300"
                           placeholder="Masukkan password saat ini">
                    <p class="text-xs text-red-600 mt-2 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Wajib diisi untuk konfirmasi perubahan profile
                    </p>
                  </div>
                </div>
              </form>
            </div>
            
            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-6 border-t border-gray-200">
              <div class="flex gap-3">
                <button type="submit" form="settings-form" class="flex-1 bg-[#4F2E22] hover:bg-[#3f251b] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 hover:shadow-lg active:scale-95 btn-scale">
                  <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Simpan Perubahan
                </button>
                <button type="button" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200 hover:shadow-md active:scale-95 btn-scale" id="close-settings-modal">
                  Batal
                </button>
              </div>
            </div>
          </div>
        `;

        document.body.appendChild(modal);

        // Event listeners
        document.getElementById('close-settings-modal').addEventListener('click', () => {
          modal.remove();
        });

        document.getElementById('close-settings-modal-header').addEventListener('click', () => {
          modal.remove();
        });

        // Profile photo preview
        document.getElementById('profile-photo').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        });

        // Camera button functionality
        document.getElementById('camera-btn').addEventListener('click', function() {
          // Check if device supports camera
          if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Try to access camera directly
            navigator.mediaDevices.getUserMedia({ 
              video: { facingMode: 'user' } // Front camera by default
            })
            .then(function(stream) {
              // Create video element to show camera feed
              const video = document.createElement('video');
              video.srcObject = stream;
              video.autoplay = true;
              
              // Create camera modal
              const cameraModal = document.createElement('div');
              cameraModal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
              cameraModal.innerHTML = `
                <div class="bg-white rounded-xl p-4 max-w-sm w-full mx-4">
                  <h3 class="text-lg font-semibold mb-4 text-center">Ambil Foto Profile</h3>
                  <div class="relative">
                    <video id="camera-video" class="w-full rounded-lg" autoplay></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                      <div class="w-32 h-32 border-4 border-white rounded-full"></div>
                    </div>
                  </div>
                  <div class="flex gap-2 mt-4">
                    <button id="capture-btn" class="flex-1 bg-[#4F2E22] text-white py-2 rounded-lg hover:bg-[#3f251b] transition-colors">
                      📸 Ambil Foto
                    </button>
                    <button id="cancel-camera-btn" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors">
                      Batal
                    </button>
                  </div>
                </div>
              `;
              
              document.body.appendChild(cameraModal);
              
              // Set video stream
              const videoElement = cameraModal.querySelector('#camera-video');
              videoElement.srcObject = stream;
              
              // Capture photo
              document.getElementById('capture-btn').addEventListener('click', function() {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                
                // Convert to blob and update preview
                canvas.toBlob(function(blob) {
                    const fileName = `profile-${Date.now()}.jpg`;
                    const file = new File([blob], fileName, { type: 'image/jpeg' });
                    capturedPhotoFile = file;
                    const url = URL.createObjectURL(blob);
                    document.getElementById('profile-preview').src = url;
                    
                    // Update file input for form submission if supported
                    try {
                      const dataTransfer = new DataTransfer();
                      dataTransfer.items.add(file);
                      document.getElementById('profile-photo').files = dataTransfer.files;
                    } catch (error) {
                      console.warn('Unable to set hidden input files directly:', error);
                    }
                  }, 'image/jpeg');
                });

                document.getElementById('cancel-camera-btn').addEventListener('click', function() {
                  stream.getTracks().forEach(track => track.stop());
                  cameraModal.remove();
                });
                
              })
              .catch(function(error) {
              console.error('Camera access denied:', error);
              // Fallback to file input if camera access denied
              document.getElementById('camera-input').click();
            });
          } else {
            // Fallback to file input if getUserMedia not supported
            document.getElementById('camera-input').click();
          }
        });

        // Profile photo input preview (upload fallback)
        document.getElementById('profile-photo').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            capturedPhotoFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
          }
        });

        // Camera input preview
        document.getElementById('camera-input').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            capturedPhotoFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
              document.getElementById('profile-preview').src = e.target.result;
              // Also update the file input for form submission
              const dataTransfer = new DataTransfer();
              dataTransfer.items.add(file);
              document.getElementById('profile-photo').files = dataTransfer.files;
            };
            reader.readAsDataURL(file);
          }
        });

        // Form submission
        document.getElementById('settings-form').addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          const profilePhotoInput = document.getElementById('profile-photo');
          if (profilePhotoInput && profilePhotoInput.files.length > 0) {
            formData.set('profile_photo', profilePhotoInput.files[0]);
          } else if (capturedPhotoFile) {
            formData.set('profile_photo', capturedPhotoFile, capturedPhotoFile.name);
          }
          const submitBtn = modal.querySelector('button[type="submit"]');
          const originalText = submitBtn ? submitBtn.textContent : 'Menyimpan...';
          
          if (submitBtn) {
            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;
          }

          // Add method override for PUT/PATCH if needed
          formData.append('_method', 'POST');

          fetch('/api/user/profile', {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            },
            credentials: 'same-origin'
          })
          .then(response => {
            console.log('Response status:', response.status);
            return response.json().then(data => {
              if (!response.ok) {
                throw new Error(data.message || 'Server error');
              }
              return data;
            });
          })
          .then(data => {
            console.log('Success response:', data);
            if (data.success) {
              showModal('Berhasil', 'Profile berhasil diperbarui!', 'success');
              modal.remove();
              // Refresh page to show updated profile
              setTimeout(() => location.reload(), 500);
            } else {
              showModal('Gagal', data.message || 'Gagal memperbarui profile', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showModal('Kesalahan', 'Terjadi kesalahan: ' + error.message, 'error');
          })
          .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
          });
        });

        modal.addEventListener('click', (e) => {
          if (e.target === modal) modal.remove();
        });
      })
      .catch(error => {
        console.error('Error fetching profile:', error);
        showModal('Error', 'Gagal memuat data profile', 'error');
      });
  }

  // Fungsi global agar bisa dipanggil dari HTML string onclick
  window.restoreOrder = function(index) {
    const savedOrders = JSON.parse(localStorage.getItem('heldOrders')) || [];
    if (index >= 0 && index < savedOrders.length) {
      if (orders.length > 0) {
        if (!confirm('Meja kasir sedang terisi. Timpa pesanan saat ini dengan pesanan dari Cart?')) {
          return;
        }
      }
      
      const selectedOrder = savedOrders[index];
      orders = selectedOrder.items.map(item => ({ ...item })); // copy deep
      globalDiscount = selectedOrder.globalDiscount || 0;
      orderNotes = selectedOrder.notes || '';
      
      // Update UI untuk diskon dan catatan
      const orderNotesInput = document.getElementById('order-notes');
      if (orderNotesInput) {
        orderNotesInput.value = orderNotes;
      }
      
      // Hapus dari saved
      savedOrders.splice(index, 1);
      localStorage.setItem('heldOrders', JSON.stringify(savedOrders));
      
      renderOrderList();
      updateTotal();
      updateCartBadge(); // Update badge setelah restore
      
      // Tutup modal cart
      const modal = document.getElementById('cart-modal');
      if (modal) modal.remove();
    }
  };
  
  // Initialize - tampilkan pesan kosong dan update badge
  renderOrderList();
  updateCartBadge(); // Cek localStorage saat load
  
  // Category Dropdown functionality
  const categoryDropdownBtn = document.getElementById('category-dropdown-btn');
  const categoryDropdown = document.getElementById('category-dropdown');
  const dropdownChevron = document.getElementById('dropdown-chevron');
  const selectedCategoryText = document.getElementById('selected-category-text');
  
  if (categoryDropdownBtn) {
    // Toggle dropdown
    categoryDropdownBtn.addEventListener('click', () => {
      const isHidden = categoryDropdown.classList.contains('hidden');
      
      if (isHidden) {
        categoryDropdown.classList.remove('hidden');
        dropdownChevron.style.transform = 'rotate(180deg)';
      } else {
        categoryDropdown.classList.add('hidden');
        dropdownChevron.style.transform = 'rotate(0deg)';
      }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!categoryDropdownBtn.contains(e.target) && !categoryDropdown.contains(e.target)) {
        categoryDropdown.classList.add('hidden');
        dropdownChevron.style.transform = 'rotate(0deg)';
      }
    });
  }
  
  // Category dropdown items click handler
  document.querySelectorAll('.category-dropdown-item').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      
      const selectedCategory = item.dataset.category;
      const categoryName = item.querySelector('span').textContent;
      
      // Update button text
      selectedCategoryText.textContent = categoryName;
      
      // Close dropdown
      categoryDropdown.classList.add('hidden');
      dropdownChevron.style.transform = 'rotate(0deg)';
      
      // Filter products based on category
      document.querySelectorAll('.menu-card').forEach(card => {
        const productCategoryId = card.dataset.categoryId;
        
        if (selectedCategory === 'all') {
          card.style.display = 'flex';
        } else {
          // Filter berdasarkan category_id dari database
          if (selectedCategory === productCategoryId) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        }
      });
      
      // Update old radio buttons for compatibility
      document.querySelectorAll('.category-radio').forEach(radio => {
        radio.checked = false;
        if (selectedCategory !== 'all' && radio.dataset.category === selectedCategory) {
          radio.checked = true;
        }
      });
    });
  });
  
  // Search functionality
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const search = this.value.toLowerCase();
      document.querySelectorAll('.menu-card').forEach(card => {
        const name = card.dataset.productName.toLowerCase();
        if (name.includes(search)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
  
  // Category filter functionality
  document.querySelectorAll('.category-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      const selectedCategory = this.dataset.category;
      
      document.querySelectorAll('.menu-card').forEach(card => {
        const productCategoryId = card.dataset.categoryId;
        
        if (!selectedCategory || selectedCategory === 'all') {
          card.style.display = 'flex';
        } else {
          // Filter berdasarkan category_id dari database
          if (selectedCategory === productCategoryId) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        }
      });
    });
  });
