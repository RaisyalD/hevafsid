<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private $products) {}

    public function collection(): Collection
    {
        return $this->products->map(fn($p) => [
            $p->sku,
            $p->name,
            $p->category?->name,
            $p->stock_total,
            $p->min_stock,
            number_format($p->sell_price, 0, ',', '.'),
            number_format($p->default_cost_price, 0, ',', '.'),
            $p->stock_total <= $p->min_stock ? 'MENIPIS' : 'NORMAL',
        ]);
    }

    public function headings(): array
    {
        return ['SKU', 'Nama Produk', 'Kategori', 'Stok', 'Min Stok', 'Harga Jual', 'Modal Default', 'Status Stok'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FCE7F3']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }
}
