@extends('layouts.app')
@section('title', 'Master Barang')
@section('page-title', 'Master Barang')
@section('breadcrumb', 'Kelola semua produk hijab & fashion')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-1 gap-2 max-w-xl">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, barcode…"
               class="input flex-1">
        <select name="category_id" class="input w-40">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="stock_filter" class="input w-36">
            <option value="">Semua Stok</option>
            <option value="low"   {{ request('stock_filter') === 'low'   ? 'selected' : '' }}>Stok Menipis</option>
            <option value="empty" {{ request('stock_filter') === 'empty' ? 'selected' : '' }}>Stok Habis</option>
        </select>
        <button type="submit" class="btn-primary whitespace-nowrap">Cari</button>
    </form>
    <a href="{{ route('products.create') }}" class="btn-primary flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Produk
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Produk</th>
                <th>SKU / Barcode</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($products as $product)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        @if($product->image)
                        <img src="{{ $product->image_url }}" alt="" class="h-10 w-10 rounded-xl object-cover border border-pink-100">
                        @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100 text-pink-600 text-xs font-bold">
                            {{ strtoupper(substr($product->name, 0, 2)) }}
                        </div>
                        @endif
                        <div>
                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-400">{{ $product->unit }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="font-mono text-xs text-gray-700">{{ $product->sku }}</div>
                    <div class="font-mono text-xs text-gray-400">{{ $product->barcode }}</div>
                </td>
                <td>
                    <span class="badge-pink">{{ $product->category?->name }}</span>
                </td>
                <td class="font-semibold text-gray-900">
                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    <span class="font-bold text-lg {{ $product->stock_total == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $product->stock_total }}
                    </span>
                    @if($product->isLowStock() && $product->stock_total > 0)
                    <div class="text-xs text-amber-500">⚠ Menipis</div>
                    @elseif($product->stock_total == 0)
                    <div class="text-xs text-red-500">Habis</div>
                    @endif
                </td>
                <td class="text-center">
                    <span class="{{ $product->is_active ? 'badge-green' : 'badge-red' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('products.show', $product) }}"
                           class="rounded-lg p-1.5 text-gray-400 hover:text-rose-600 hover:bg-pink-50 transition" title="Detail">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('products.edit', $product) }}"
                           class="rounded-lg p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Edit">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <a href="{{ route('products.label', $product) }}" target="_blank"
                           class="rounded-lg p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 transition" title="Print Label">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->canManageStock())
                        <form id="del-prd-{{ $product->id }}" method="POST"
                              action="{{ route('products.destroy', $product) }}">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" title="Hapus"
                                onclick="openConfirm('Hapus Produk', 'Produk &quot;{{ addslashes($product->name) }}&quot; akan dihapus permanen. Produk yang masih memiliki stok tidak dapat dihapus.', 'Ya, Hapus', 'del-prd-{{ $product->id }}')"
                                class="rounded-lg p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-10 w-10 text-pink-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Tidak ada produk ditemukan</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($products->hasPages())
    <div class="border-t border-pink-50 px-6 py-4">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection
