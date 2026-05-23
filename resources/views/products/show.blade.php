@extends('layouts.app')
@section('title', $product->name)
@section('page-title', $product->name)
@section('breadcrumb', 'Master Barang → Detail')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Left: Product info + barcode --}}
    <div class="space-y-6">

        {{-- Product Card --}}
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
            @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 class="w-full h-48 object-contain rounded-xl border border-pink-100 mb-4">
            @else
            <div class="w-full h-48 flex items-center justify-center rounded-xl bg-pink-50 border border-pink-100 mb-4">
                <div class="text-4xl font-bold text-pink-200">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
            </div>
            @endif

            <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $product->name }}</h2>
            <span class="badge-pink">{{ $product->category?->name }}</span>

            <div class="mt-4 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">SKU</span>
                    <span class="font-mono font-medium text-gray-900">{{ $product->sku }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Harga Jual</span>
                    <span class="font-semibold text-rose-600">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Harga Modal</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($product->default_cost_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Satuan</span>
                    <span class="font-medium text-gray-900">{{ strtoupper($product->unit) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Min Stok</span>
                    <span class="font-medium text-gray-900">{{ $product->min_stock }}</span>
                </div>
                <div class="flex justify-between text-sm items-center">
                    <span class="text-gray-500">Total Stok</span>
                    <span class="text-2xl font-bold {{ $product->stock_total == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $product->stock_total }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status</span>
                    <span class="{{ $product->is_active ? 'badge-green' : 'badge-red' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="flex gap-2 mt-5">
                <a href="{{ route('products.edit', $product) }}" class="btn-primary flex-1 text-center text-sm">Edit</a>
                <a href="{{ route('products.label', $product) }}" target="_blank" class="btn-secondary flex-1 text-center text-sm">🖨 Label</a>
            </div>
        </div>

        {{-- Barcode Card --}}
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 text-center">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Barcode Produk</h3>
            <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="barcode"
                 class="mx-auto max-w-full" style="image-rendering: pixelated;">
            <div class="font-mono text-sm text-gray-600 mt-2">{{ $product->barcode }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $product->sku }}</div>
        </div>
    </div>

    {{-- Right: Batches --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <a href="{{ route('incoming.create', ['product_id' => $product->id]) }}"
               class="flex flex-col items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-center hover:bg-emerald-100 transition">
                <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
                </svg>
                <span class="text-xs font-medium text-emerald-700">Barang Masuk</span>
            </a>
            <a href="{{ route('outgoing.create', ['product_id' => $product->id]) }}"
               class="flex flex-col items-center gap-2 rounded-xl bg-orange-50 border border-orange-200 p-4 text-center hover:bg-orange-100 transition">
                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V4m0 0l4 4m-4-4l-4 4"/>
                </svg>
                <span class="text-xs font-medium text-orange-700">Barang Keluar</span>
            </a>
            <a href="{{ route('inventory-card.show', $product) }}"
               class="flex flex-col items-center gap-2 rounded-xl bg-blue-50 border border-blue-200 p-4 text-center hover:bg-blue-100 transition">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs font-medium text-blue-700">Kartu Stok</span>
            </a>
            <a href="{{ route('reject.create', ['product_id' => $product->id]) }}"
               class="flex flex-col items-center gap-2 rounded-xl bg-red-50 border border-red-200 p-4 text-center hover:bg-red-100 transition">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <span class="text-xs font-medium text-red-700">Reject</span>
            </a>
        </div>

        {{-- FIFO Batches --}}
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <div class="border-b border-pink-50 px-6 py-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Batch Stok (FIFO)</h3>
                <span class="text-xs text-gray-400">{{ $product->batches->count() }} batch</span>
            </div>
            <table class="table w-full">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th>Kode Batch</th>
                        <th>Tgl Masuk</th>
                        <th>Awal</th>
                        <th>Sisa</th>
                        <th>Modal/Unit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($product->batches as $batch)
                    <tr>
                        <td>
                            <a href="{{ route('batches.show', $batch) }}"
                               class="font-mono text-xs text-rose-600 hover:text-rose-800">{{ $batch->batch_code }}</a>
                        </td>
                        <td class="text-xs text-gray-500">{{ $batch->received_date->format('d/m/Y') }}</td>
                        <td class="text-sm font-medium">{{ $batch->qty_initial }}</td>
                        <td class="text-sm font-bold {{ $batch->qty_remaining == 0 ? 'text-gray-400' : 'text-emerald-600' }}">
                            {{ $batch->qty_remaining }}
                        </td>
                        <td class="text-sm">Rp {{ number_format($batch->cost_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="{{ match($batch->status) { 'active' => 'badge-green', 'depleted' => 'badge-red', default => 'badge-yellow' } }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-sm text-gray-400">Belum ada batch stok</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
