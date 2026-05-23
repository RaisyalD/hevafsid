<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan FIFO Batch</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1 { color: #f43f5e; }
        .sub { color: #9ca3af; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #fce7f3; padding: 6px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px 8px; border-bottom: 1px solid #fce7f3; }
        tfoot td { font-weight: bold; background: #fff1f2; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>hevafsid — Laporan FIFO Batch</h1>
    <p class="sub">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Kode Batch</th><th>Produk</th><th>Tgl Masuk</th>
                <th class="right">Awal</th><th class="right">Sisa</th>
                <th class="right">Modal/Unit</th><th class="right">Nilai Sisa</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batches as $b)
            <tr>
                <td>{{ $b->batch_code }}</td>
                <td>{{ $b->product?->name }}</td>
                <td>{{ $b->received_date->format('d/m/Y') }}</td>
                <td class="right">{{ $b->qty_initial }}</td>
                <td class="right">{{ $b->qty_remaining }}</td>
                <td class="right">Rp {{ number_format($b->cost_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($b->qty_remaining * $b->cost_price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($b->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="right">Total Valuasi</td>
                <td class="right">Rp {{ number_format($batches->sum(fn($b) => $b->qty_remaining * $b->cost_price), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
