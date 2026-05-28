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