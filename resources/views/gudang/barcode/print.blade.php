<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label {{ ($codeType ?? 'barcode') === 'qrcode' ? 'QR Code' : 'Barcode' }} - INVENTRA</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background: #fff;
            color: #0f172a;
        }

        /* 1. RACK LABEL (100mm x 40mm) */
        @if($template === 'rack_label')
        @page {
            size: 100mm 40mm;
            margin: 0;
        }
        .label-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .label-item {
            width: 100mm;
            height: 40mm;
            padding: 3mm 4.5mm;
            border: 2px solid #0f172a;
            border-radius: 4px;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            background: #fff;
        }
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
        }
        .label-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 65mm;
            color: #0f172a;
        }
        .label-category {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            background: #f1f5f9;
            padding: 1px 5px;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            color: #334155;
        }
        .label-barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1.5mm 0;
            min-height: 18mm;
        }
        .label-barcode svg {
            max-width: 85mm;
            height: 18mm;
        }
        .label-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5px;
            border-top: 1px solid #e2e8f0;
            padding-top: 2px;
        }
        .label-sku {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .label-price {
            font-weight: 800;
            color: #0f172a;
        }

        /* 2. PRODUCT BARCODE (50mm x 30mm) */
        @elseif($template === 'product_barcode')
        @page {
            size: 50mm 30mm;
            margin: 0;
        }
        .label-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .label-item {
            width: 50mm;
            height: 30mm;
            padding: 2mm 2.5mm;
            border: 1px solid #334155;
            border-radius: 3px;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            box-sizing: border-box;
            background: #fff;
        }
        .label-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #0f172a;
            line-height: 1.2;
        }
        .label-barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0.5mm 0;
            min-height: 14mm;
        }
        .label-barcode svg {
            max-width: 45mm;
            height: 14mm;
        }
        .label-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5px;
        }
        .label-sku {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        .label-price {
            font-weight: 800;
            color: #0f172a;
        }

        /* 3. A4 GRID (Sheet 3x8) */
        @else
        @page {
            size: A4;
            margin: 8mm 6mm;
        }
        .label-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3.5mm;
        }
        .label-item {
            height: 33.5mm;
            padding: 2.5mm 3.5mm;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            box-sizing: border-box;
            page-break-inside: avoid;
            background: #fff;
        }
        .label-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #0f172a;
            line-height: 1.2;
        }
        .label-barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0.5mm 0;
            min-height: 15mm;
        }
        .label-barcode svg {
            max-width: 54mm;
            height: 15mm;
        }
        .label-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8.5px;
            border-top: 1px solid #f1f5f9;
            padding-top: 1.5px;
        }
        .label-sku {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 0.3px;
        }
        .label-cat-tag {
            font-size: 8px;
            color: #64748b;
            max-width: 22mm;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .label-price {
            font-weight: 800;
            color: #0f172a;
        }
        @endif

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-container img, .qr-container canvas {
            display: block;
            margin: 0 auto;
        }

        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-radius: 10px;
            font-family: sans-serif;
            font-size: 13px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: background 0.15s;
        }
        .btn-print:hover {
            background: #4338ca;
        }
        .btn-close {
            background: #334155;
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 7px;
            cursor: pointer;
            margin-left: 8px;
            font-size: 13px;
        }
        .btn-close:hover {
            background: #475569;
        }

        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; background: transparent; }
            @if(isset($showBorder) && !$showBorder)
            .label-item { border: none !important; }
            @else
            .label-item { border: 1px dashed #cbd5e1 !important; }
            @endif
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>INVENTRA Barcode Printing</strong> • Total: <strong>{{ count($printList) }} Label</strong> | Format: <strong>{{ ucfirst(str_replace('_', ' ', $template)) }}</strong> | Simbol: <strong>{{ ($codeType ?? 'barcode') === 'qrcode' ? 'QR Code (2D)' : 'Barcode Code128 (1D)' }}</strong>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">Cetak Sekarang</button>
            <button onclick="window.close()" class="btn-close">Tutup</button>
        </div>
    </div>

    <div class="label-container">
        @foreach($printList as $index => $item)
        @php
            $activeSku = $item->display_sku ?? $item->formatted_sku ?? $item->sku;
        @endphp
        <div class="label-item">
            @if($template === 'rack_label')
                <div class="label-header">
                    <span class="label-title">{{ $item->name }}</span>
                    <span class="label-category">{{ $item->category->name ?? 'GUDANG' }}</span>
                </div>
                <div class="label-barcode">
                    @if(($codeType ?? 'barcode') === 'qrcode')
                        <div class="qr-container" data-code="{{ $activeSku }}"></div>
                    @else
                        <svg class="barcode-svg" data-code="{{ $activeSku }}"></svg>
                    @endif
                </div>
                <div class="label-footer">
                    <span class="label-sku">{{ $activeSku }}</span>
                    <span>UoM: <strong>{{ $item->uom->symbol ?? $item->uom->name ?? 'PCS' }}</strong></span>
                    <span class="label-price">Rp {{ number_format($item->standard_price, 0, ',', '.') }}</span>
                </div>
            @elseif($template === 'product_barcode')
                <div class="label-title">{{ $item->name }}</div>
                <div class="label-barcode">
                    @if(($codeType ?? 'barcode') === 'qrcode')
                        <div class="qr-container" data-code="{{ $activeSku }}"></div>
                    @else
                        <svg class="barcode-svg" data-code="{{ $activeSku }}"></svg>
                    @endif
                </div>
                <div class="label-footer">
                    <span class="label-sku">{{ $activeSku }}</span>
                    <span class="label-price">Rp {{ number_format($item->standard_price, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="label-title">{{ $item->name }}</div>
                <div class="label-barcode">
                    @if(($codeType ?? 'barcode') === 'qrcode')
                        <div class="qr-container" data-code="{{ $activeSku }}"></div>
                    @else
                        <svg class="barcode-svg" data-code="{{ $activeSku }}"></svg>
                    @endif
                </div>
                <div class="label-footer">
                    <span class="label-sku">{{ $activeSku }}</span>
                    <span class="label-cat-tag">{{ $item->category->name ?? '' }}</span>
                    <span class="label-price">Rp {{ number_format($item->standard_price, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Generators: JsBarcode & QRCode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isQr = {{ ($codeType ?? 'barcode') === 'qrcode' ? 'true' : 'false' }};

            if (isQr) {
                const qrElements = document.querySelectorAll('.qr-container');
                qrElements.forEach(el => {
                    const code = el.getAttribute('data-code');
                    if (code && typeof QRCode !== 'undefined') {
                        new QRCode(el, {
                            text: code,
                            width: @if($template === 'rack_label') 65 @elseif($template === 'product_barcode') 46 @else 48 @endif,
                            height: @if($template === 'rack_label') 65 @elseif($template === 'product_barcode') 46 @else 48 @endif,
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                });
            } else {
                const barcodeElements = document.querySelectorAll('.barcode-svg');
                barcodeElements.forEach(el => {
                    const code = el.getAttribute('data-code');
                    if (code) {
                        try {
                            JsBarcode(el, code, {
                                format: "CODE128",
                                lineColor: "#000",
                                width: @if($template === 'product_barcode') 1.25 @elseif($template === 'rack_label') 1.65 @else 1.35 @endif,
                                height: @if($template === 'product_barcode') 32 @elseif($template === 'rack_label') 42 @else 34 @endif,
                                displayValue: false,
                                margin: 0
                            });
                        } catch (e) {
                            console.error('Barcode error for', code, e);
                        }
                    }
                });
            }

            // Auto-trigger print after short delay
            setTimeout(() => {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
