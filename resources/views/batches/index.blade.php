@extends('layouts.app')
@section('title', 'Batch FIFO')
@section('page-title', 'Manajemen Batch FIFO')
@section('breadcrumb', 'Stok → Batch FIFO')

@section('content')
<div class="flex flex-wrap gap-2 mb-6">
    <form method="GET" class="flex flex-wrap gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode batch / produk…" class="input w-52">
        <select name="status" class="input w-36">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="depleted" {{ request('status') === 'depleted' ? 'selected' : '' }}>Habis</option>
            <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
        </select>
        <select name="product_id" class="input w-48">
            <option value="">Semua Produk</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode Batch</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Tgl Masuk</th>
                <th class="text-right">Qty Awal</th>
                <th class="text-right">Qty Sisa</th>
                <th class="text-right">Terpakai</th>
                <th class="text-right">Modal/Unit</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($batches as $batch)
            <tr>
                <td class="font-mono text-xs text-gray-700">{{ $batch->batch_code }}</td>
                <td>
                    <div class="font-medium text-gray-900">{{ $batch->product?->name }}</div>
                    <div class="font-mono text-xs text-gray-400">{{ $batch->product?->sku }}</div>
                </td>
                <td><span class="badge-pink text-xs">{{ $batch->product?->category?->name }}</span></td>
                <td class="text-sm text-gray-600">{{ $batch->received_date->format('d/m/Y') }}</td>
                <td class="text-right font-medium">{{ $batch->qty_initial }}</td>
                <td class="text-right font-bold {{ $batch->qty_remaining == 0 ? 'text-gray-400' : 'text-emerald-600' }}">
                    {{ $batch->qty_remaining }}
                </td>
                <td class="text-right text-sm text-orange-600">{{ $batch->used_qty }}</td>
                <td class="text-right text-sm">Rp {{ number_format($batch->cost_price, 0, ',', '.') }}</td>
                <td>
                    @php
                    $badgeClass = match($batch->status) {
                        'active'   => 'badge-green',
                        'depleted' => 'badge-red',
                        default    => 'badge-yellow',
                    };
                    @endphp
                    <span class="{{ $badgeClass }}">{{ ucfirst($batch->status) }}</span>
                </td>
                <td>
                    <a href="{{ route('batches.show', $batch) }}"
                       class="rounded-lg p-1.5 text-gray-400 hover:text-rose-600 hover:bg-pink-50 transition block">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="py-10 text-center text-sm text-gray-400">Belum ada batch stok</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($batches->hasPages())
    <div class="border-t border-pink-50 px-6 py-4">{{ $batches->links() }}</div>
    @endif
</div>
@endsection
