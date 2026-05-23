<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1   { color: #f43f5e; margin-bottom: 4px; }
        .sub { color: #9ca3af; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th   { background: #fce7f3; padding: 6px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        td   { padding: 5px 8px; border-bottom: 1px solid #fce7f3; }
        tfoot td { font-weight: bold; background: #fff1f2; }
        .right { text-align: right; }
        .summary { display: flex; gap: 16px; margin-bottom: 16px; }
        .stat { background: #fff1f2; border: 1px solid #fecdd3; padding: 10px 16px; border-radius: 8px; }
        .stat .label { font-size: 9px; color: #9ca3af; }
        .stat .value { font-size: 14px; font-weight: bold; color: #f43f5e; }
    </style>
</head>
<body>
    <h1>hevafsid — Laporan Penjualan</h1>
    <p class="sub">Periode: {{ $dateFrom }} s/d {{ $dateTo }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <tr>
            <td><div class="stat"><div class="label">Total Transaksi</div><div class="value">{{ $transactions->count() }}</div></div></td>
            <td><div class="stat"><div class="label">Total Qty</div><div class="value">{{ number_format($summary['total_qty']) }}</div></div></td>
            <td><div class="stat"><div class="label">Total Pendapatan</div><div class="value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div></div></td>
            <td><div class="stat"><div class="label">Laba Kotor</div><div class="value">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}</div></div></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Kode</th><th>Produk</th><th>Tgl</th>
                <th class="right">Qty</th><th class="right">HPP</th>
                <th class="right">Pendapatan</th><th class="right">Laba</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->transaction_code }}</td>
                <td>{{ $t->product?->name }}</td>
                <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td class="right">{{ $t->quantity }}</td>
                <td class="right">Rp {{ number_format($t->total_hpp, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($t->total_revenue, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($t->gross_profit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right">TOTAL</td>
                <td class="right">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
