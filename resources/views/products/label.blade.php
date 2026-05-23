<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Barcode — {{ $product->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: white; }
        .page { display: flex; flex-wrap: wrap; gap: 8px; padding: 12px; }
        .label {
            width: 200px; border: 1px solid #e5e7eb; border-radius: 8px;
            padding: 8px; text-align: center; page-break-inside: avoid;
        }
        .brand { font-size: 7px; color: #ec4899; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px; }
        .name { font-size: 11px; font-weight: bold; color: #111; margin-bottom: 2px; line-height: 1.2; }
        .sku { font-size: 9px; color: #9ca3af; font-family: monospace; margin-bottom: 4px; }
        .barcode-img { width: 180px; height: 55px; margin: 0 auto 3px; }
        .barcode-num { font-size: 9px; color: #6b7280; font-family: monospace; margin-bottom: 4px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#f9fafb;padding:12px;display:flex;gap:8px;align-items:center;border-bottom:1px solid #e5e7eb;">
    <button onclick="window.print()"
            style="background:#f43f5e;color:white;border:none;padding:8px 20px;border-radius:8px;font-weight:bold;cursor:pointer;">
        🖨 Cetak Label
    </button>
    <select id="qty" style="border:1px solid #e5e7eb;padding:6px 10px;border-radius:8px;" onchange="generateLabels(this.value)">
        <option value="1">1 Label</option>
        <option value="4">4 Label</option>
        <option value="8" selected>8 Label</option>
        <option value="12">12 Label</option>
        <option value="24">24 Label</option>
    </select>
    <a href="{{ route('products.show', $product) }}"
       style="color:#6b7280;font-size:13px;">← Kembali ke Detail</a>
</div>

<div class="page" id="labels-container">
    @for($i = 0; $i < 8; $i++)
    <div class="label">
        <div class="brand">hevafsid</div>
        <div class="name">{{ $product->name }}</div>
        <div class="sku">{{ $product->sku }}</div>
        <img src="data:image/png;base64,{{ $barcodeBase64 }}" class="barcode-img" alt="barcode">
        <div class="barcode-num">{{ $product->barcode }}</div>
    </div>
    @endfor
</div>

<script>
function generateLabels(qty) {
    const container = document.getElementById('labels-container');
    const labelHtml = `
        <div class="label">
            <div class="brand">hevafsid</div>
            <div class="name">{{ $product->name }}</div>
            <div class="sku">{{ $product->sku }}</div>
            <img src="data:image/png;base64,{{ $barcodeBase64 }}" class="barcode-img" alt="barcode">
            <div class="barcode-num">{{ $product->barcode }}</div>
            </div>`;
    container.innerHTML = labelHtml.repeat(parseInt(qty));
}
</script>
</body>
</html>
