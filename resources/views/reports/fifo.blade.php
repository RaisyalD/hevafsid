@extends('layouts.app')
@section('title', 'Laporan FIFO Batch')
@section('page-title', 'Laporan FIFO Batch')
@section('breadcrumb', 'Laporan → FIFO Batch')

@section('content')

<div class="flex flex-wrap gap-3 mb-6 items-end">
    <form method="GET" class="flex gap-2">
        <select name="status" class="input w-36">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="depleted" {{ request('status') === 'depleted' ? 'selected' : '' }}>Habis</option>
            <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
    <a href="{{ route('reports.fifo', array_merge(request()->all(), ['format' => 'pdf'])) }}"
       class="btn-secondary text-sm">Export PDF</a>
    <a href="{{ route('reports.index') }}" class="btn-secondary text-sm ml-auto">← Kembali</a>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode Batch</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Tgl Masuk</th>
                <th class="text-right">Qty Awal</th>
                <th class="text-right">Qty Sisa</th>
                <th class="text-right">Modal/Unit</th>
                <th class="text-right">Nilai Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($batches as $batch)
            <tr>
                <td class="font-mono text-xs text-gray-600">{{ $batch->batch_code }}</td>
                <td class="font-medium text-gray-900">{{ $batch->product?->name }}</td>
                <td><span class="badge-pink">{{ $batch->product?->category?->name }}</span></td>
                <td class="text-xs text-gray-500">{{ $batch->received_date->format('d/m/Y') }}</td>
                <td class="text-right">{{ $batch->qty_initial }}</td>
                <td class="text-right font-bold {{ $batch->qty_remaining == 0 ? 'text-gray-400' : 'text-emerald-600' }}">
                    {{ $batch->qty_remaining }}
                </td>
                <td class="text-right">Rp {{ number_format($batch->cost_price, 0, ',', '.') }}</td>
                <td class="text-right font-semibold text-rose-600">
                    Rp {{ number_format($batch->qty_remaining * $batch->cost_price, 0, ',', '.') }}
                </td>
                <td>
                    <span class="{{ match($batch->status) { 'active' => 'badge-green', 'depleted' => 'badge-red', default => 'badge-yellow' } }}">
                        {{ ucfirst($batch->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="py-10 text-center text-gray-400">Tidak ada data batch</td></tr>
            @endforelse
        </tbody>
        @if($batches->count() > 0)
        <tfoot class="bg-pink-50/50 font-bold">
            <tr>
                <td colspan="7" class="px-4 py-3 text-right">Total Valuasi Stok</td>
                <td class="text-right px-4 py-3 text-rose-600">
                    Rp {{ number_format($batches->sum(fn($b) => $b->qty_remaining * $b->cost_price), 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
