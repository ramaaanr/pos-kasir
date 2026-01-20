<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $product->nama }}</title>
    <style>
        @page {
            margin: 0;
            size: 40mm 20mm; /* Standard small barcode label size */
        }
        body {
            margin: 0;
            padding: 2mm;
            width: 40mm;
            height: 20mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            text-align: center;
        }
        .product-name {
            font-size: 8px;
            font-weight: 700;
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 36mm;
            text-transform: uppercase;
        }
        .barcode-container {
            width: 36mm;
            height: 10mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-container svg {
            width: 100%;
            height: 100%;
        }
        .barcode-text {
            font-size: 8px;
            font-weight: 500;
            margin-top: 1px;
            letter-spacing: 1px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
        
        /* Preview helper */
        .preview-container {
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        .label-preview {
            background: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="product-name">{{ $product->nama }}</div>
    <div class="barcode-container">
        @php
            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            echo $generator->getBarcode($product->kode_produk, $generator::TYPE_CODE_128);
        @endphp
    </div>
    <div class="barcode-text">{{ $product->kode_produk }}</div>
</body>
</html>
