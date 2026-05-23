@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('breadcrumb', 'Laporan → Penjualan')

@section('content')

<div class="flex flex-wrap gap-3 mb-6 items-end">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="input w-36">
        <button type="submit" class="btn-primary">Filter</button>
    </form>
    <a href="{{ route('reports.sales', array_merge(request()->all(), ['format' => 'pdf'])) }}"
       class="btn-secondary text-sm">Export PDF</a>
    <a href="{{ route('reports.sales', array_merge(request()->all(), ['format' => 'excel'])) }}"
       class="border border-emerald-200 hover:bg-emerald-50 text-emerald-600 text-sm font-medium px-4 py-2 rounded-xl transition">
       Export Excel
    </a>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
        <div class="text-xs text-gray-400 mb-1">Total Transaksi</div>
        <div class="text-2xl font-bold text-gray-900">{{ $transactions->count() }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
        <div class="text-xs text-gray-400 mb-1">Total Unit Terjual</div>
        <div class="text-2xl font-bold text-orange-600">{{ number_format($summary['total_qty']) }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
        <div class="text-xs text-gray-400 mb-1">Total Pendapatan</div>
        <div class="text-xl font-bold text-emerald-600">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 text-center">
        <div class="text-xs text-gray-400 mb-1">Laba Kotor</div>
        <div class="text-xl font-bold {{ $summary['gross_profit'] >= 0 ? 'text-rose-600' : 'text-red-600' }}">
            Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode</th>
                <th>Produk</th>
                <th>Tgl</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">HPP FIFO</th>
                <th class="text-right">Pendapatan</th>
                <th class="text-right">Laba</th>
                <th>Input oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($transactions as $trx)
            <tr>
                <td class="font-mono text-xs text-gray-500">{{ $trx->transaction_code }}</td>
                <td>
                    <div class="font-medium text-gray-900">{{ $trx->product?->name }}</div>
                    <span class="badge-pink text-xs">{{ $trx->product?->category?->name }}</span>
                </td>
                <td class="text-xs text-gray-500">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                <td class="text-right">{{ $trx->quantity }}</td>
                <td class="text-right">Rp {{ number_format($trx->sell_price, 0, ',', '.') }}</td>
                <td class="text-right text-rose-600">Rp {{ number_format($trx->total_hpp, 0, ',', '.') }}</td>
                <td class="text-right font-semibold text-emerald-700">Rp {{ number_format($trx->total_revenue, 0, ',', '.') }}</td>
                <td class="text-right font-bold {{ $trx->gross_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($trx->gross_profit, 0, ',', '.') }}
                </td>
                <td class="text-xs text-gray-400">{{ $trx->user?->name }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="py-10 text-center text-gray-400">Tidak ada data penjualan</td></tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot class="bg-pink-50/50">
            <tr class="font-bold text-sm">
                <td colspan="3" class="px-4 py-3 text-right text-gray-600">TOTAL</td>
                <td class="text-right px-4 py-3">{{ number_format($summary['total_qty']) }}</td>
                <td></td>
                <td class="text-right px-4 py-3 text-rose-600">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
                <td class="text-right px-4 py-3 text-emerald-700">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                <td class="text-right px-4 py-3 {{ $summary['gross_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
