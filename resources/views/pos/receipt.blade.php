<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaction->ref_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0 auto;
            padding: 10px;
            width: 58mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 6px 0; }
        .divider-solid { border-bottom: 1px solid #000; margin: 6px 0; }
        .flex { display: flex; justify-content: space-between; }
        
        table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        td { padding: 1.5px 0; vertical-align: top; }
        .td-qty { width: 12%; text-align: left; font-weight: bold; }
        .td-name { width: 53%; text-align: left; word-break: break-word; }
        .td-price { width: 35%; text-align: right; font-weight: bold; }

        .receipt-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            font-family: sans-serif;
            text-decoration: none;
            display: inline-block;
        }
        .receipt-btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 4px; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header Store Info -->
    <div class="text-center">
        <h2 style="margin: 0; font-size: 14px; font-weight: bold; letter-spacing: 0.5px;">INVENTRA POS</h2>
        <p style="margin: 2px 0 0 0; font-size: 9px;">SISTEM LOGISTIK & KASIR TERPADU</p>
        <p style="margin: 1px 0; font-size: 9px;">Outlet Utama • Telp: (021) 8899-7766</p>
    </div>

    <div class="divider-solid"></div>

    <!-- Metadata -->
    <div style="font-size: 10px;">
        <div class="flex">
            <span>No. Ref:</span>
            <span class="font-bold">{{ $transaction->ref_number }}</span>
        </div>
        <div class="flex">
            <span>Waktu:</span>
            <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Kasir:</span>
            <span>{{ $transaction->user->name ?? 'Kasir Terminal' }}</span>
        </div>
        @if($transaction->notes && str_contains($transaction->notes, 'Pelanggan:'))
            @php
                preg_match('/Pelanggan:\s*([^|]+)/', $transaction->notes, $matches);
                $custName = trim($matches[1] ?? '');
            @endphp
            @if($custName)
                <div class="flex">
                    <span>Pelanggan:</span>
                    <span class="font-bold">{{ $custName }}</span>
                </div>
            @endif
        @endif
    </div>

    <div class="divider"></div>

    <!-- Items List -->
    <table>
        @php $subtotalRaw = 0; @endphp
        @foreach($transaction->lines as $line)
        @php 
            $lineTotal = $line->quantity * $line->unit_price; 
            $subtotalRaw += $lineTotal;
        @endphp
        <tr>
            <td colspan="3" class="td-name font-bold">{{ $line->item->name }}</td>
        </tr>
        <tr>
            <td class="td-qty">{{ $line->quantity }}x</td>
            <td class="td-name text-left" style="font-size: 9px;">@ {{ number_format($line->unit_price, 0, ',', '.') }}</td>
            <td class="td-price">{{ number_format($lineTotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <!-- Calculations -->
    <div style="font-size: 10px;">
        <div class="flex">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($subtotalRaw, 0, ',', '.') }}</span>
        </div>
        @if($transaction->notes && str_contains($transaction->notes, 'Diskon:'))
            @php
                preg_match('/Diskon:\s*Rp\s*([0-9\.]+)/', $transaction->notes, $dMatches);
                $discStr = trim($dMatches[1] ?? '0');
            @endphp
            <div class="flex" style="color: #000;">
                <span>Diskon Promo:</span>
                <span>-Rp {{ $discStr }}</span>
            </div>
        @endif
        <div class="flex">
            <span>PPN (11%):</span>
            <span>Rp {{ number_format(max(0, $transaction->total_amount - ($subtotalRaw * (1 - 0.11))), 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Grand Total & Payment -->
    <div class="flex font-bold" style="font-size: 12px; margin: 4px 0;">
        <span>TOTAL AKHIR</span>
        <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
    
    <div style="font-size: 10px; margin-top: 3px;">
        <div class="flex">
            <span>Metode Bayar:</span>
            <span class="font-bold">{{ strtoupper($transaction->payment_method ?? 'TUNAI') }}</span>
        </div>
        <div class="flex">
            <span>Uang Diterima:</span>
            <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if($transaction->payment_method === 'Cash' || $transaction->payment_method === 'Tunai' || empty($transaction->payment_method))
        <div class="flex font-bold" style="margin-top: 2px;">
            <span>Kembalian:</span>
            <span>Rp {{ number_format(max(0, $transaction->paid_amount - $transaction->total_amount), 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="divider-solid"></div>

    <!-- Footer -->
    <div class="text-center" style="margin-top: 8px;">
        <p style="margin: 0; font-weight: bold; font-size: 10px;">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
        <p style="margin: 2px 0 0 0; font-size: 8px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa struk asli.</p>
        <p style="margin: 3px 0 0 0; font-size: 8px; font-family: monospace;">*** INVENTRA POS CLOUD ***</p>
    </div>

    <!-- No-print controls -->
    <div class="text-center no-print" style="margin-top: 20px; display: flex; gap: 8px; justify-content: center;">
        <button onclick="window.print()" class="receipt-btn">Cetak Ulang</button>
        <button onclick="window.close()" class="receipt-btn receipt-btn-secondary">Tutup</button>
    </div>

</body>
</html>
