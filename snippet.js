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