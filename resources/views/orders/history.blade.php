@extends('layouts.admin')

@section('title', 'History Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Transaksi / Produk" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.orders.history') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-[#4F2E22] to-[#3f251b] border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">ORDER ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">PEMBAYARAN</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">STATUS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">TOTAL</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">WAKTU</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-[#4F2E22] hover:bg-opacity-5 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $transaction->transaction_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($transaction->payment_method == 'cash') bg-green-100 text-green-800 border border-green-200
                                    @elseif($transaction->payment_method == 'qris') bg-blue-100 text-blue-800 border border-blue-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($transaction->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    @if($transaction->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200
                                    @endif">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $transaction->created_at->format('H:i') }}</div>
                                <div class="text-xs text-gray-400">{{ $transaction->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="javascript:alert('Link clicked! ID: {{ $transaction->id }}');" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors duration-200 flex items-center gap-1 inline-block mr-1">
                                    <i class="fas fa-link"></i>
                                    Link
                                </a>

                                <button type="button" onclick="alert('Button clicked! ID: {{ $transaction->id }}');" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors duration-200 flex items-center gap-1 mr-1">
                                    <i class="fas fa-mouse"></i>
                                    Button
                                </button>

                                <button type="button" class="print-receipt-btn bg-[#4F2E22] hover:bg-[#3f251b] text-white px-2 py-1 rounded-lg text-xs font-medium transition-colors duration-200 flex items-center gap-1" onclick="testSimplePrint('{{ $transaction->id }}', '{{ $transaction->transaction_number }}'); return false;">
                                    <i class="fas fa-print"></i>
                                    Cetak
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h3>
                                <p class="text-sm text-gray-500">Transaksi akan muncul di sini setelah pelanggan melakukan pembayaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links('pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Debug: Check if script loads
console.log('History script loaded');

// Super simple test function without API
function testSimplePrint(transactionId, transactionNumber) {
    console.log('=== TEST SIMPLE PRINT CALLED ===');
    console.log('Transaction ID:', transactionId);
    console.log('Transaction Number:', transactionNumber);
    console.log('Current URL:', window.location.href);
    
    alert('TEST: Tombol cetak berhasil diklik!\nTransaction ID: ' + transactionId + '\nTransaction Number: ' + transactionNumber);
    
    // Create simple test print window
    try {
        const printWindow = window.open('', '_blank', 'width=400,height=600');
        
        if (!printWindow) {
            alert('Popup blocker terdeteksi! Silakan izinkan popup.');
            return;
        }
        
        const simpleHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Test Struk</title>
            <style>
                body { font-family: Arial; padding: 20px; text-align: center; }
                button { padding: 10px 20px; margin: 10px; }
            </style>
        </head>
        <body>
            <h2>TEST STRUK BERHASIL!</h2>
            <p>Transaction ID: ${transactionId}</p>
            <p>Transaction Number: ${transactionNumber}</p>
            <p>Ini adalah test untuk memastikan print window berfungsi.</p>
            <button onclick="window.print()">Cetak Test</button>
            <button onclick="window.close()">Tutup</button>
        </body>
        </html>
        `;
        
        printWindow.document.write(simpleHTML);
        printWindow.document.close();
        
        console.log('Test print window opened successfully');
        
    } catch (error) {
        console.error('Error creating test print window:', error);
        alert('Error: ' + error.message);
    }
}

function printHistoryReceipt(transaction) {
    console.log('=== PRINT HISTORY RECEIPT CALLED ===');
    console.log('Transaction data:', transaction);
    
    try {
        // Create print window
        console.log('Creating print window...');
        const printWindow = window.open('', '_blank', 'width=400,height=600');
        
        if (!printWindow) {
            alert('Popup blocker terdeteksi! Silakan izinkan popup untuk mencetak struk.');
            return;
        }
        
        console.log('Print window created successfully');
        
        // Format date
        const date = new Date(transaction.created_at);
        const dateStr = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        
        console.log('Formatted date:', dateStr, timeStr);
    
    // Build receipt HTML with new design
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
            <td colspan="2" style="font-size: 8pt; text-align: center;">${transaction.transaction_number}</td>
          </tr>
        </table>

        <div class="divider">--------------------------------</div>

        <table>
          ${transaction.items.map(item => `
            <tr>
              <td style="font-size: 8pt; width: 60%;">${item.quantity}x ${item.name}</td>
              <td style="font-size: 8pt; width: 40%; text-align: right;">${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
            </tr>
          `).join('')}
        </table>

        <div class="divider">--------------------------------</div>

        <table>
          <tr>
            <td style="font-size: 8pt; width: 60%;">SUBTOTAL</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${parseInt(transaction.subtotal).toLocaleString('id-ID')}</td>
          </tr>
          ${transaction.discount_amount > 0 ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">DISKON</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">-${parseInt(transaction.discount_amount).toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
          ${transaction.tax_amount > 0 ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">PAJAK</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${parseInt(transaction.tax_amount).toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
          <tr>
            <td style="font-size: 10pt; font-weight: bold; width: 60%;">TOTAL</td>
            <td style="font-size: 10pt; font-weight: bold; width: 40%; text-align: right;">${parseInt(transaction.total_amount).toLocaleString('id-ID')}</td>
          </tr>
        </table>

        <div class="divider">--------------------------------</div>

        <table>
          <tr>
            <td style="font-size: 8pt; width: 60%;">${transaction.payment_method.toUpperCase()}</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${parseInt(transaction.total_amount).toLocaleString('id-ID')}</td>
          </tr>
          ${transaction.cash_received ? `
          <tr>
            <td style="font-size: 8pt; width: 60%;">KEMBALI</td>
            <td style="font-size: 8pt; width: 40%; text-align: right;">${parseInt(transaction.cash_received - transaction.total_amount).toLocaleString('id-ID')}</td>
          </tr>
          ` : ''}
        </table>

        <div class="divider">--------------------------------</div>
        <div style="margin-top: 5mm; font-size: 7pt; text-align: center;">
          Terima kasih atas kunjungan Anda<br>
          Barang yang sudah dibeli tidak dapat<br>
          dikembalikan
        </div>

      </body>
      </html>
    `;
    
    console.log('Writing HTML to print window...');
    printWindow.document.write(printHTML);
    printWindow.document.close();
    
    console.log('Setting up print window onload...');
    // Auto print
    printWindow.onload = function() {
        console.log('Print window loaded, triggering print...');
        try {
            printWindow.print();
            console.log('Print dialog opened successfully');
        } catch (error) {
            console.error('Error printing:', error);
            alert('Gagal membuka dialog print. Silakan gunakan Ctrl+P untuk mencetak.');
        }
    };
    
    printWindow.onerror = function() {
        console.error('Error loading print window');
        alert('Gagal memuat window print. Silakan coba lagi.');
    };
    
    console.log('Print receipt setup completed');
    
    } catch (error) {
        console.error('Error in printHistoryReceipt:', error);
        alert('Terjadi kesalahan saat mencetak struk: ' + error.message);
    }
}
</script>
@endpush