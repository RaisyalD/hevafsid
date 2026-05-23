@extends('layouts.app')
@section('title', 'Kartu Persediaan — ' . $product->name)
@section('page-title', 'Kartu Persediaan')
@section('breadcrumb', 'Stok → Kartu Persediaan → ' . $product->name)

@section('content')

{{-- Selector + Filter --}}
<div class="flex flex-wrap gap-3 mb-6 items-end">
    <form method="GET" class="flex flex-wrap gap-2 flex-1">
        <select name="product_id" class="input w-56" onchange="this.form.submit()">
            @foreach($products as $p)
            <option value="{{ $p->id }}" {{ $p->id == $product->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="input w-36">
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('inventory-card.show', $product) }}" class="btn-secondary">Reset</a>
    </form>
    <a href="{{ route('reports.stock', ['product_id' => $product->id]) }}"
       class="btn-secondary text-sm flex items-center gap-1">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export
    </a>
</div>

{{-- Product summary card --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5 col-span-2">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pink-100 text-pink-600 text-xl font-bold flex-shrink-0">
                {{ strtoupper(substr($product->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $product->name }}</h2>
                <div class="flex gap-3 text-sm text-gray-500 mt-1">
                    <span>SKU: <strong class="font-mono text-gray-700">{{ $product->sku }}</strong></span>
                    <span>Kategori: <strong>{{ $product->category?->name }}</strong></span>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
            <div class="text-xs text-gray-400 mb-1">Stok Saat Ini</div>
            <div class="text-2xl font-bold {{ $product->stock_total == 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $product->stock_total }}
            </div>
            <div class="text-xs text-gray-400">{{ $product->unit }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
            <div class="text-xs text-gray-400 mb-1">Valuasi FIFO</div>
            <div class="text-sm font-bold text-gray-900">Rp {{ number_format($valuation, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Inventory Card Ledger --}}
<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <div class="border-b border-pink-50 px-6 py-4">
        <h3 class="text-sm font-semibold text-gray-900">Kartu Stok — Ledger Persediaan</h3>
        <p class="text-xs text-gray-400 mt-0.5">Riwayat lengkap mutasi stok produk ini</p>
    </div>
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Tanggal</th>
                <th>Kode Referensi</th>
                <th>Tipe Transaksi</th>
                <th>Batch</th>
                <th class="text-right text-emerald-600">Masuk</th>
                <th class="text-right text-red-500">Keluar</th>
                <th class="text-right">Saldo</th>
                <th class="text-right">Modal/Unit</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($cards as $card)
            <tr>
                <td class="text-xs text-gray-500">{{ $card->transaction_date->format('d/m/Y') }}</td>
                <td class="font-mono text-xs text-rose-600">{{ $card->reference_code }}</td>
                <td>
                    @php
                    $typeClass = match($card->transaction_type) {
                        'incoming'   => 'badge-green',
                        'outgoing'   => 'badge-red',
                        'reject'     => 'badge-yellow',
                        'adjustment' => 'badge-blue',
                        default      => 'badge-blue',
                    };
                    @endphp
                    <span class="{{ $typeClass }}">{{ $card->transaction_type_label }}</span>
                </td>
                <td class="font-mono text-xs text-gray-400">{{ $card->batch?->batch_code ?? '—' }}</td>
                <td class="text-right font-semibold {{ $card->qty_in > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                    {{ $card->qty_in > 0 ? '+' . $card->qty_in : '—' }}
                </td>
                <td class="text-right font-semibold {{ $card->qty_out > 0 ? 'text-red-500' : 'text-gray-300' }}">
                    {{ $card->qty_out > 0 ? '-' . $card->qty_out : '—' }}
                </td>
                <td class="text-right font-bold {{ $card->balance <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $card->balance }}
                </td>
                <td class="text-right text-xs text-gray-500">
                    {{ $card->cost_price > 0 ? 'Rp ' . number_format($card->cost_price, 0, ',', '.') : '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-10 text-center text-gray-400">
                    Belum ada riwayat mutasi stok untuk produk ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($cards->hasPages())
    <div class="border-t border-pink-50 px-6 py-4">{{ $cards->links() }}</div>
    @endif
</div>

@endsection
