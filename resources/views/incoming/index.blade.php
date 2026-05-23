@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('page-title', 'Riwayat Barang Masuk')
@section('breadcrumb', 'Transaksi → Barang Masuk')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-end gap-3 mb-6">
    <form method="GET" class="flex flex-wrap gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / produk…" class="input w-52">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="input w-36">
        <select name="supplier_id" class="input w-40">
            <option value="">Semua Supplier</option>
            @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','date_from','date_to','supplier_id']))
        <a href="{{ route('incoming.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
    <a href="{{ route('incoming.create') }}" class="btn-primary flex items-center gap-2 whitespace-nowrap">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Input Barang Masuk
    </a>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="table w-full min-w-[800px]">
        <thead class="bg-pink-50/50">
            <tr>
                <th class="w-36">Kode Transaksi</th>
                <th>Produk</th>
                <th class="w-32">Supplier</th>
                <th class="w-24">Tgl Masuk</th>
                <th class="w-16 text-right">Qty</th>
                <th class="w-28 text-right">Modal/Unit</th>
                <th class="w-28 text-right">Total</th>
                <th class="w-28">Batch</th>
                <th class="w-10"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($transactions as $trx)
            <tr>
                <td class="font-mono text-xs text-gray-600">{{ $trx->transaction_code }}</td>
                <td>
                    <div class="font-medium text-gray-900">{{ $trx->product?->name }}</div>
                    <div class="text-xs text-gray-400">{{ $trx->product?->sku }}</div>
                </td>
                <td class="text-sm text-gray-600">{{ $trx->supplier?->name ?? '—' }}</td>
                <td class="text-sm text-gray-600">{{ $trx->received_date->format('d/m/Y') }}</td>
                <td class="text-right font-semibold text-emerald-600">+{{ $trx->quantity }}</td>
                <td class="text-right text-sm text-gray-700">Rp {{ number_format($trx->cost_price, 0, ',', '.') }}</td>
                <td class="text-right font-semibold text-gray-900">Rp {{ number_format($trx->total_cost, 0, ',', '.') }}</td>
                <td>
                    <span class="font-mono text-xs badge-blue">{{ $trx->batch?->batch_code }}</span>
                </td>
                <td>
                    <a href="{{ route('incoming.show', $trx) }}"
                       class="rounded-lg p-1.5 text-gray-400 hover:text-rose-600 hover:bg-pink-50 transition block">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="py-10 text-center text-sm text-gray-400">Belum ada data barang masuk</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($transactions->hasPages())
    <div class="border-t border-pink-50 px-6 py-4">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
