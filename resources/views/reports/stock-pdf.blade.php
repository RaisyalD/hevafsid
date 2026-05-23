<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1   { color: #f43f5e; }
        .sub { color: #9ca3af; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th    { background: #fce7f3; padding: 6px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        td    { padding: 5px 8px; border-bottom: 1px solid #fce7f3; }
        .right { text-align: right; }
        .badge-red    { color: #dc2626; font-weight: bold; }
        .badge-yellow { color: #d97706; font-weight: bold; }
        .badge-green  { color: #059669; }
    </style>
</head>
<body>
    <h1>hevafsid — Laporan Stok</h1>
    <p class="sub">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>SKU</th><th>Nama Produk</th><th>Kategori</th>
                <th class="right">Stok</th><th class="right">Min</th>
                <th class="right">Harga Jual</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name }}</td>
                <td class="right">{{ $p->stock_total }}</td>
                <td class="right">{{ $p->min_stock }}</td>
                <td class="right">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                <td>
                    @if($p->stock_total == 0)
                        <span class="badge-red">HABIS</span>
                    @elseif($p->isLowStock())
                        <span class="badge-yellow">MENIPIS</span>
                    @else
                        <span class="badge-green">Normal</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
