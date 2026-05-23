@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')
@section('breadcrumb', 'Laporan → Stok')

@section('content')

<div class="flex flex-wrap gap-3 mb-6 items-end">
    <a href="{{ route('reports.stock', array_merge(request()->all(), ['format' => 'pdf'])) }}"
       class="btn-secondary text-sm">Export PDF</a>
    <a href="{{ route('reports.stock', array_merge(request()->all(), ['format' => 'excel'])) }}"
       class="border border-emerald-200 hover:bg-emerald-50 text-emerald-600 text-sm font-medium px-4 py-2 rounded-xl transition">
       Export Excel
    </a>
    <a href="{{ route('reports.index') }}" class="btn-secondary text-sm ml-auto">← Kembali</a>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Min Stok</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">Batch Aktif</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($products as $product)
            <tr>
                <td class="font-mono text-xs text-gray-500">{{ $product->sku }}</td>
                <td class="font-medium text-gray-900">{{ $product->name }}</td>
                <td><span class="badge-pink">{{ $product->category?->name }}</span></td>
                <td class="text-right font-bold {{ $product->stock_total == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-emerald-600') }}">
                    {{ $product->stock_total }} {{ $product->unit }}
                </td>
                <td class="text-right text-gray-500">{{ $product->min_stock }}</td>
                <td class="text-right">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ $product->batches->count() }}</td>
                <td>
                    @if($product->stock_total == 0)
                        <span class="badge-red">Habis</span>
                    @elseif($product->isLowStock())
                        <span class="badge-yellow">Menipis</span>
                    @else
                        <span class="badge-green">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-10 text-center text-gray-400">Tidak ada data stok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
