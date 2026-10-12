<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Shift #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }} - {{ $shift->user->name }}</title>
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
            line-height: 1.35;
            color: #000;
            margin: 0 auto;
            padding: 12px 10px;
            width: 58mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 7px 0; }
        .divider-solid { border-bottom: 1px solid #000; margin: 7px 0; }
        .divider-double { border-bottom: 2px solid #000; margin: 7px 0; }
        .flex { display: flex; justify-content: space-between; align-items: flex-start; }
        
        table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        td { padding: 2px 0; vertical-align: top; }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 3px;
        }

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
        <p style="margin: 1px 0; font-size: 9px; font-weight: bold;">*** REKAPITULASI SHIFT KASIR ***</p>
    </div>

    <div class="divider-solid"></div>

    <!-- Metadata Shift -->
    <div style="font-size: 10px;">
        <div class="flex">
            <span>ID Shift:</span>
            <span class="font-bold">#SHF-{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex">
            <span>Kasir:</span>
            <span class="font-bold">{{ $shift->user->name }}</span>
        </div>
        @if($shift->user->nip)
        <div class="flex">
            <span>NIP:</span>
            <span>{{ $shift->user->nip }}</span>
        </div>
        @endif
        <div class="flex">
            <span>Waktu Buka:</span>
            <span>{{ $shift->start_time->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Waktu Tutup:</span>
            <span>{{ $shift->end_time ? $shift->end_time->format('d/m/y H:i') : 'Masih Aktif' }}</span>
        </div>
        @if($shift->end_time)
        <div class="flex">
            <span>Durasi Kerja:</span>
            <span>{{ $shift->start_time->diffInHours($shift->end_time) }}j {{ $shift->start_time->diffInMinutes($shift->end_time) % 60 }}m</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Rincian Penjualan & Pembayaran -->
    <div style="font-size: 10px;">
        <div class="font-bold" style="margin-bottom: 3px; text-transform: uppercase;">RINCIAN PENDAPATAN:</div>
        
        <div class="flex">
            <span>Penjualan Tunai:</span>
            <span class="font-bold">Rp {{ number_format($stats['cash_sales'], 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>Penjualan QRIS:</span>
            <span>Rp {{ number_format($stats['qris_sales'], 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>Penjualan Debit:</span>
            <span>Rp {{ number_format($stats['debit_sales'], 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>Penjualan Transfer:</span>
            <span>Rp {{ number_format($stats['transfer_sales'], 0, ',', '.') }}</span>
        </div>
        @if($stats['other_sales'] > 0)
        <div class="flex">
            <span>Metode Lainnya:</span>
            <span>Rp {{ number_format($stats['other_sales'], 0, ',', '.') }}</span>
        </div>
        @endif
        @if($stats['total_returns'] > 0)
        <div class="flex" style="color: #000;">
            <span>Total Retur/Refund:</span>
            <span>-Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="divider"></div>

        <div class="flex font-bold" style="font-size: 11px;">
            <span>TOTAL OMZET SHIFT:</span>
            <span>Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider-double"></div>

    <!-- Rekonsiliasi Kas Laci (Cash Reconciliation) -->
    <div style="font-size: 10px;">
        <div class="font-bold" style="margin-bottom: 3px; text-transform: uppercase;">REKONSILIASI KAS LACI:</div>
        
        <div class="flex">
            <span>Modal Kas Awal:</span>
            <span>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
        </div>
        <div class="flex">
            <span>(+) Penerimaan Tunai:</span>
            <span>Rp {{ number_format($stats['cash_sales'], 0, ',', '.') }}</span>
        </div>
        @if($stats['total_returns'] > 0)
        <div class="flex">
            <span>(-) Pengembalian Retur:</span>
            <span>Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="divider"></div>

        <div class="flex font-bold">
            <span>Kas Seharusnya:</span>
            <span>Rp {{ number_format($stats['expected_cash'], 0, ',', '.') }}</span>
        </div>
        <div class="flex font-bold" style="margin-top: 2px;">
            <span>Kas Fisik Dihitung:</span>
            <span>Rp {{ number_format($stats['ending_cash'], 0, ',', '.') }}</span>
        </div>

        <div class="text-center" style="margin: 6px 0;">
            @if($stats['discrepancy'] == 0)
                <div class="badge">STATUS: KAS SESUAI / PAS</div>
            @elseif($stats['discrepancy'] > 0)
                <div class="badge">LEBIH KAS: +Rp {{ number_format($stats['discrepancy'], 0, ',', '.') }}</div>
            @else
                <div class="badge">KURANG KAS: -Rp {{ number_format(abs($stats['discrepancy']), 0, ',', '.') }}</div>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Ringkasan Operasional -->
    <div style="font-size: 9px;">
        <div class="flex">
            <span>Total Struk Terbit:</span>
            <span class="font-bold">{{ $stats['tx_count'] }} Transaksi</span>
        </div>
        <div class="flex">
            <span>Total Item Terjual:</span>
            <span class="font-bold">{{ $stats['items_sold'] }} Unit/Pcs</span>
        </div>
        @if($stats['return_count'] > 0)
        <div class="flex">
            <span>Transaksi Retur:</span>
            <span class="font-bold">{{ $stats['return_count'] }} Kali</span>
        </div>
        @endif
        @if($shift->notes)
        <div style="margin-top: 4px; border-top: 1px dotted #000; padding-top: 3px;">
            <span class="font-bold">Catatan Kasir:</span>
            <p style="margin: 1px 0; font-style: italic;">"{{ $shift->notes }}"</p>
        </div>
        @endif
    </div>

    <div class="divider-solid"></div>

    <!-- Tanda Tangan Serah Terima -->
    <table style="font-size: 9px; margin-top: 8px; text-align: center;">
        <tr>
            <td style="width: 50%;">Kasir Yang Bertugas,</td>
            <td style="width: 50%;">Supervisor / Manager,</td>
        </tr>
        <tr>
            <td style="height: 38px;"></td>
            <td style="height: 38px;"></td>
        </tr>
        <tr>
            <td class="font-bold">({{ $shift->user->name }})</td>
            <td class="font-bold">( .................... )</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 8px; margin-top: 6px;">
        <p style="margin: 0;">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p style="margin: 2px 0 0 0; font-family: monospace;">*** DOKUMEN INTERNAL INVENTRA ***</p>
    </div>

    <!-- No-print controls -->
    <div class="text-center no-print" style="margin-top: 20px; display: flex; gap: 8px; justify-content: center;">
        <button onclick="window.print()" class="receipt-btn">Cetak Rekap</button>
        <button onclick="window.close()" class="receipt-btn receipt-btn-secondary">Tutup</button>
    </div>

</body>
</html>
