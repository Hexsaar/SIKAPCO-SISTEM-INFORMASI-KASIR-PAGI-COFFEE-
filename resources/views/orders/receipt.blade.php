<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { font-family: 'Courier New', monospace; font-size: 13px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white rounded-2xl p-6 w-96 shadow-2xl" style="font-family: 'Courier New', monospace; font-size: 13px;">
        <!-- Title -->
        <div class="text-center mb-3 font-bold text-lg">
          Cetak struk
        </div>
        
        <!-- Shop Info -->
        <div class="text-center mb-3 leading-tight">
          <div class="font-bold">Pagi Coffee</div>
          <div class="text-xs">Taman Pagelaran</div>
          <div class="text-xs">085353712877</div>
        </div>
        
        <!-- Separator 1 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Order ID -->
        <div class="text-xs mb-2 text-center">
          Order ID: ORD-IDPGICFFEE{{ $transaction->created_at ? $transaction->created_at->format('Ymd') : date('Ymd') }}
        </div>
        
        <!-- Separator 2 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Kasir Info -->
        <div class="text-xs mb-2">
          Kasir: {{ $transaction->user ? $transaction->user->name : 'Admin' }}
        </div>
        
        <!-- Separator 3 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Items Table -->
        <table class="w-full text-xs mb-2">
          <thead>
            <tr>
              <th class="text-left py-1">Item</th>
              <th class="text-center py-1">Qty</th>
              <th class="text-right py-1">Harga</th>
              <th class="text-right py-1">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @php
                $items = $transaction->items ?? [];
            @endphp
            @if(is_array($items))
              @foreach($items as $item)
                <tr>
                  <td class="py-1">{{ $item['product_name'] ?? 'Unknown' }}</td>
                  <td class="text-center py-1">{{ $item['quantity'] ?? 0 }}</td>
                  <td class="text-right py-1">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                  <td class="text-right py-1">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') }}</td>
                </tr>
              @endforeach
            @endif
          </tbody>
        </table>
        
        <!-- Separator 4 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Summary Section -->
        <div class="text-xs mb-2 space-y-1">
          <div class="flex justify-between">
            <span>Discount (%)</span>
            <span class="text-right">Rp 0</span>
          </div>
          <div class="flex justify-between">
            <span>Sub total</span>
            <span class="text-right">Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between">
            <span>Tax 5%</span>
            <span class="text-right">Rp 0</span>
          </div>
        </div>
        
        <!-- Separator 5 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Total Section -->
        <div class="text-lg font-bold mb-3 text-center">
          Total: Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}
        </div>
        
        <!-- Separator 6 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Payment Section -->
        <div class="text-xs mb-3 space-y-1">
          @if($transaction->payment_method === 'cash' && $transaction->cash_received)
            <div class="flex justify-between">
              <span>Tunai</span>
              <span class="text-right">{{ number_format($transaction->cash_received, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold">
              <span>Kembalian</span>
              <span class="text-right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
          @else
            <div class="flex justify-between">
              <span>Metode Pembayaran</span>
              <span class="text-right font-bold">{{ strtoupper($transaction->payment_method ?? 'CASH') }}</span>
            </div>
          @endif
        </div>
        
        <!-- Separator 7 -->
        <div class="border-t border-dashed border-gray-400 my-2"></div>
        
        <!-- Footer -->
        <div class="text-center text-xs text-gray-600 mb-4">
          <p>Terima kasih telah berbelanja</p>
          <p>Silakan datang kembali</p>
        </div>
        
        <!-- Button -->
        <button class="w-full bg-[#4F2E22] hover:bg-[#3f251b] text-white font-bold py-2 rounded-lg transition active:scale-95 no-print" onclick="window.print()">
          Cetak
        </button>
    </div>
</body>
</html>