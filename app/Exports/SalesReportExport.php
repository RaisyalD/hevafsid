<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private $transactions, private array $summary) {}

    public function collection(): Collection
    {
        $rows = $this->transactions->map(fn($t) => [
            $t->transaction_code,
            $t->product?->name,
            $t->transaction_date->format('d/m/Y'),
            $t->quantity,
            number_format($t->sell_price, 0, ',', '.'),
            number_format($t->total_hpp, 0, ',', '.'),
            number_format($t->total_revenue, 0, ',', '.'),
            number_format($t->gross_profit, 0, ',', '.'),
        ]);

        $rows->push([]);
        $rows->push([
            'TOTAL', '', '',
            $this->summary['total_qty'],
            '',
            number_format($this->summary['total_hpp'], 0, ',', '.'),
            number_format($this->summary['total_revenue'], 0, ',', '.'),
            number_format($this->summary['gross_profit'], 0, ',', '.'),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['Kode Transaksi', 'Produk', 'Tanggal', 'Qty', 'Harga Jual', 'HPP FIFO', 'Pendapatan', 'Laba Kotor'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FCE7F3']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
