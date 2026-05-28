<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Struk Pembayaran</title>
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
    
    .no-print {
        width: 100%;
        margin-bottom: 20px;
        background: #4F2E22;
        color: white;
        border: none;
        padding: 10px;
        font-weight: 700;
        cursor: pointer;
    }
    @media print {
      body { width: 48mm; margin: 0 auto; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <button class="no-print" onclick="window.print()">
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
      <td style="font-size: 8pt;">TGL: {{ $transaction->created_at ? $transaction->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
      <td style="font-size: 8pt; text-align: right;">{{ $transaction->created_at ? $transaction->created_at->format('H.i') : now()->format('H.i') }}</td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: 8pt; text-align: center;">{{ $transaction->transaction_number }}</td>
    </tr>
  </table>

  <div class="divider">--------------------------------</div>

  @php
      $items = $transaction->items ?? [];
      $subtotal = $transaction->subtotal_amount ?? collect($items)->sum(function ($item) {
          return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
      });
      $discount = $transaction->global_discount_amount ?? 0;
      $tax = $transaction->tax_amount ?? 0;
      $total = $transaction->total_amount ?? 0;
      $paymentMethod = strtoupper($transaction->payment_method ?? 'CASH');
      $cashReceived = $transaction->cash_received ?? 0;
  @endphp

  <table>
    @if(is_array($items) && count($items) > 0)
        @foreach($items as $item)
            @php
                $itemQty = $item['quantity'] ?? 0;
                $itemPrice = $item['price'] ?? 0;
                $itemDiscountPercent = $item['discount'] ?? 0;
                $itemOriginalTotal = $itemPrice * $itemQty;
                $itemDiscountAmount = $itemOriginalTotal * ($itemDiscountPercent / 100);
                $itemDiscountedTotal = $itemOriginalTotal - $itemDiscountAmount;
            @endphp
            <tr>
              <td style="font-size: 8pt; width: 60%;">{{ $itemQty }}x {{ $item['product_name'] ?? ($item['name'] ?? 'Unknown') }}</td>
              <td style="font-size: 8pt; width: 40%; text-align: right;">{{ number_format($itemDiscountedTotal, 0, ',', '.') }}</td>
            </tr>
            @if($itemDiscountPercent > 0)
            <tr>
              <td style="font-size: 7pt; color: #666;">&nbsp;&nbsp;Disc {{ $itemDiscountPercent }}%</td>
              <td style="font-size: 7pt; color: #666; text-align: right;">-{{ number_format($itemDiscountAmount, 0, ',', '.') }}</td>
            </tr>
            @endif
        @endforeach
    @endif
  </table>

  <div class="divider">--------------------------------</div>

  <table>
    <tr>
      <td style="font-size: 8pt; width: 60%;">SUBTOTAL</td>
      <td style="font-size: 8pt; width: 40%; text-align: right;">{{ number_format($subtotal, 0, ',', '.') }}</td>
    </tr>
    @if($discount > 0)
    <tr>
      <td style="font-size: 8pt; width: 60%;">DISKON</td>
      <td style="font-size: 8pt; width: 40%; text-align: right;">-{{ number_format($discount, 0, ',', '.') }}</td>
    </tr>
    @endif
    @if($tax > 0)
    @php
       $taxPercent = ($subtotal - $discount) > 0 ? round(($tax / ($subtotal - $discount)) * 100) : 11;
    @endphp
    <tr>
      <td style="font-size: 8pt; width: 60%;">PAJAK ({{ $taxPercent }}%)</td>
      <td style="font-size: 8pt; width: 40%; text-align: right;">{{ number_format($tax, 0, ',', '.') }}</td>
    </tr>
    @endif
    <tr>
      <td style="font-size: 10pt; font-weight: bold; width: 60%;">TOTAL</td>
      <td style="font-size: 10pt; font-weight: bold; width: 40%; text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
    </tr>
  </table>

  @if($transaction->notes && trim($transaction->notes) !== '')
  <div class="divider">--------------------------------</div>
  <table>
    <tr>
      <td colspan="2" style="font-size: 8pt; font-style: italic;">{{ $transaction->notes }}</td>
    </tr>
  </table>
  @endif

  <div class="divider">--------------------------------</div>

  <table>
    <tr>
      <td style="font-size: 8pt; width: 60%;">{{ $paymentMethod === 'CASH' ? 'TUNAI' : $paymentMethod }}</td>
      <td style="font-size: 8pt; width: 40%; text-align: right;">{{ number_format($paymentMethod === 'CASH' && $cashReceived ? $cashReceived : $total, 0, ',', '.') }}</td>
    </tr>
    @if($paymentMethod === 'CASH' && $cashReceived)
    <tr>
      <td style="font-size: 8pt; width: 60%;">KEMBALI</td>
      <td style="font-size: 8pt; width: 40%; text-align: right;">{{ number_format($cashReceived - $total, 0, ',', '.') }}</td>
    </tr>
    @endif
  </table>

  @if($discount > 0)
  <div class="divider">--------------------------------</div>
  <div style="font-size: 7pt; text-align: center;">
    * Hemat hari ini: Rp{{ number_format($discount, 0, ',', '.') }} *
  </div>
  @endif

  <div class="divider">--------------------------------</div>
  <div style="margin-top: 5mm; font-size: 7pt; text-align: center;">
    Terima kasih atas kunjungan Anda<br>
    Barang yang sudah dibeli tidak dapat<br>
    dikembalikan
  </div>

</body>
</html>