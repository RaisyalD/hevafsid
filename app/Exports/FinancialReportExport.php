<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private $transactions) {}

    public function collection(): Collection
    {
        return $this->transactions->map(fn($t) => [
            $t->transaction_code,
            $t->transaction_date->format('d/m/Y'),
            $t->description,
            $t->type_label,
            $t->category_label,
            $t->type === 'cash_in' ? number_format($t->amount, 0, ',', '.') : '',
            $t->type === 'cash_out' ? number_format($t->amount, 0, ',', '.') : '',
        ]);
    }

    public function headings(): array
    {
        return ['Kode', 'Tanggal', 'Deskripsi', 'Tipe', 'Kategori', 'Kas Masuk (Rp)', 'Kas Keluar (Rp)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FCE7F3']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }
}
