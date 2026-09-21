<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - {{ $transaction->ref_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 58mm 100%; /* Thermal printer standard size */
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 58mm;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 8px 0; }
        .divider-solid { border-bottom: 1px solid #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .td-qty { width: 15%; text-align: left; }
        .td-name { width: 50%; text-align: left; }
        .td-price { width: 35%; text-align: right; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h2 style="margin: 0 0 5px 0;">INVENTRA POS</h2>
        <p style="margin: 0 0 5px 0; font-size: 10px;">Jl. Logistik No. 123, Jakarta</p>
        <p style="margin: 0; font-size: 10px;">Telp: 08123456789</p>
    </div>

    <div class="divider-solid"></div>

    <div style="font-size: 11px;">
        <div class="flex">
            <span>No:</span>
            <span>{{ $transaction->ref_number }}</span>
        </div>
        <div class="flex">
            <span>Tgl:</span>
            <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Kasir:</span>
            <span>{{ $transaction->user->name ?? 'Kasir' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <table>
        @foreach($transaction->lines as $line)
        <tr>
            <td colspan="3" class="td-name">{{ $line->item->name }}</td>
        </tr>
        <tr>
            <td class="td-qty">{{ $line->quantity }}x</td>
            <td class="td-name text-center">@ {{ number_format($line->unit_price, 0, ',', '.') }}</td>
            <td class="td-price">{{ number_format($line->quantity * $line->unit_price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div class="flex font-bold" style="font-size: 13px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
    </div>
    
    <div class="flex" style="margin-top: 5px;">
        <span>Tunai/Bayar</span>
        <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
    </div>
    
    <div class="flex">
        <span>Kembali</span>
        <span>Rp {{ number_format($transaction->paid_amount - $transaction->total_amount, 0, ',', '.') }}</span>
    </div>

    <div class="divider-solid"></div>

    <div class="text-center" style="margin-top: 15px;">
        <p style="margin: 0 0 5px 0;">Terima Kasih</p>
        <p style="margin: 0; font-size: 10px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>

    <!-- Print Button for manual printing if needed -->
    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer;">Cetak Ulang</button>
        <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

</body>
</html>
