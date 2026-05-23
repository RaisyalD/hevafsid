<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1 { color: #f43f5e; }
        .sub { color: #9ca3af; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #fce7f3; padding: 6px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px 8px; border-bottom: 1px solid #fce7f3; }
        tfoot td { font-weight: bold; background: #fff1f2; }
        .right { text-align: right; }
        .in  { color: #059669; }
        .out { color: #dc2626; }
    </style>
</head>
<body>
    <h1>hevafsid — Laporan Keuangan</h1>
    <p class="sub">Periode: {{ $dateFrom }} s/d {{ $dateTo }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table style="margin-bottom: 12px;">
        <tr>
            <td>Total Kas Masuk: <strong class="in">Rp {{ number_format($totalIn, 0, ',', '.') }}</strong></td>
            <td>Total Kas Keluar: <strong class="out">Rp {{ number_format($totalOut, 0, ',', '.') }}</strong></td>
            <td>Saldo Bersih: <strong>Rp {{ number_format($net, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Kode</th><th>Tanggal</th><th>Deskripsi</th><th>Tipe</th><th class="right">Masuk</th><th class="right">Keluar</th></tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->transaction_code }}</td>
                <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $t->description }}</td>
                <td>{{ $t->type_label }}</td>
                <td class="right in">{{ $t->type === 'cash_in' ? 'Rp ' . number_format($t->amount, 0, ',', '.') : '' }}</td>
                <td class="right out">{{ $t->type === 'cash_out' ? 'Rp ' . number_format($t->amount, 0, ',', '.') : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right">SALDO BERSIH</td>
                <td class="right in">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
                <td class="right out">Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
