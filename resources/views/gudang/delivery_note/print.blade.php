<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $transaction->ref_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .logo-text { font-size: 28px; font-weight: bold; font-family: 'Georgia', serif; letter-spacing: 1px; }
        .title { font-size: 20px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { border: 1px solid #ddd; padding: 15px; border-radius: 4px; }
        .info-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-value { font-weight: bold; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-center { text-align: center; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center; margin-top: 50px; }
        .sign-box { height: 120px; display: flex; flex-direction: column; justify-content: space-between; }
        .sign-line { border-top: 1px solid #333; padding-top: 5px; }
        @media print {
            body { padding: 0; }
            .container { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Cetak Dokumen</button>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <div class="logo-text">INVENTRA</div>
                <div>Jl. Logistik Utama No. 123, Jakarta</div>
                <div>Telp: (021) 987-6543</div>
            </div>
            <div style="text-align: right;">
                <div class="title">SURAT JALAN</div>
                <div>No: <strong>{{ $transaction->ref_number }}</strong></div>
                <div>Tanggal: {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Kirim Kepada:</div>
                <div class="info-value">Pelanggan / Cabang Tujuan</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">(Tujuan Pengiriman)</div>
            </div>
            <div class="info-box">
                <div class="info-label">Pengirim (Gudang):</div>
                <div class="info-value">Admin Gudang ({{ $transaction->user->name ?? 'System' }})</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">Diproses melalui Sistem INVENTRA</div>
            </div>
        </div>

        <p>Bersama ini kami kirimkan barang-barang tersebut di bawah ini:</p>

        <div class="overflow-x-auto w-full pb-4">
<table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No.</th>
                    <th style="width: 150px;">Kode (SKU)</th>
                    <th>Nama Barang</th>
                    <th style="width: 100px; text-align: center;">Jumlah</th>
                    <th style="width: 150px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->lines as $index => $line)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $line->item->sku }}</td>
                    <td>{{ $line->item->name }}</td>
                    <td class="text-center">{{ $line->quantity }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>

        <div class="signatures">
            <div class="sign-box">
                <div>Diterima Oleh,</div>
                <div class="sign-line">( Nama & Stempel )</div>
            </div>
            <div class="sign-box">
                <div>Pengemudi / Kurir,</div>
                <div class="sign-line">( Nama Terang )</div>
            </div>
            <div class="sign-box">
                <div>Hormat Kami,</div>
                <div class="sign-line">( Admin Gudang )</div>
            </div>
        </div>
    </div>
</body>
</html>
