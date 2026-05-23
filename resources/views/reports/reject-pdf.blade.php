<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Reject</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1 { color: #f43f5e; }
        .sub { color: #9ca3af; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #fce7f3; padding: 6px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px 8px; border-bottom: 1px solid #fce7f3; }
        tfoot td { font-weight: bold; background: #fff1f2; }
        .right { text-align: right; }
        .loss { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h1>hevafsid — Laporan Barang Reject</h1>
    <p class="sub">Periode: {{ $dateFrom }} s/d {{ $dateTo }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    <p>Total Kerugian: <strong class="loss">Rp {{ number_format($totalLoss, 0, ',', '.') }}</strong></p>

    <table>
        <thead>
            <tr>
                <th>Kode</th><th>Produk</th><th>Tanggal</th><th>Jenis</th>
                <th class="right">Qty</th><th class="right">Kerugian</th><th>Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rejects as $r)
            <tr>
                <td>{{ $r->reject_code }}</td>
                <td>{{ $r->product?->name }}</td>
                <td>{{ $r->reject_date->format('d/m/Y') }}</td>
                <td>{{ $r->reject_type_label }}</td>
                <td class="right">{{ $r->quantity }}</td>
                <td class="right loss">Rp {{ number_format($r->total_loss, 0, ',', '.') }}</td>
                <td>{{ $r->reason }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="right">TOTAL KERUGIAN</td>
                <td class="right loss">Rp {{ number_format($totalLoss, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
