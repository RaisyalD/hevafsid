@extends('layouts.app')
@section('title', $outgoing->transaction_code)
@section('page-title', 'Detail Barang Keluar')
@section('breadcrumb', 'Transaksi → Barang Keluar → ' . $outgoing->transaction_code)

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900 font-mono">{{ $outgoing->transaction_code }}</h2>
                <p class="text-sm text-gray-500">{{ $outgoing->transaction_date->translatedFormat('d F Y') }}</p>
            </div>
            <span class="badge-red text-base px-4 py-1.5">↗ Keluar</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div class="rounded-xl bg-pink-50 p-4">
                <div class="text-xs text-gray-400 mb-1">Produk</div>
                <div class="font-semibold text-gray-900">{{ $outgoing->product?->name }}</div>
                <div class="font-mono text-xs text-gray-500 mt-0.5">{{ $outgoing->product?->sku }}</div>
            </div>
            <div class="rounded-xl bg-pink-50 p-4">
                <div class="text-xs text-gray-400 mb-1">No. Referensi</div>
                <div class="font-semibold text-gray-900">{{ $outgoing->reference_number ?? '—' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3 text-center mb-5">
            <div class="rounded-xl bg-orange-50 border border-orange-100 p-3">
                <div class="text-xs text-gray-400">Qty</div>
                <div class="text-2xl font-bold text-orange-600">{{ $outgoing->quantity }}</div>
            </div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3">
                <div class="text-xs text-gray-400">Harga Jual</div>
                <div class="text-sm font-bold text-gray-900">Rp {{ number_format($outgoing->sell_price, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl bg-pink-50 border border-pink-100 p-3">
                <div class="text-xs text-gray-400">HPP (FIFO)</div>
                <div class="text-sm font-bold text-rose-600">Rp {{ number_format($outgoing->total_hpp, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-3">
                <div class="text-xs text-gray-400">Laba Kotor</div>
                <div class="text-sm font-bold {{ $outgoing->gross_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($outgoing->gross_profit, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex justify-between">
            <span class="font-medium text-emerald-700">Total Pendapatan</span>
            <span class="text-xl font-bold text-emerald-700">Rp {{ number_format($outgoing->total_revenue, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- FIFO Details Table --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
        <div class="border-b border-pink-50 px-6 py-4">
            <h3 class="text-sm font-semibold text-gray-900">Detail FIFO — Batch yang Digunakan</h3>
            <p class="text-xs text-gray-400 mt-0.5">Stok diambil dari batch terlama terlebih dahulu</p>
        </div>
        <table class="table w-full">
            <thead class="bg-pink-50/50">
                <tr>
                    <th>Batch</th>
                    <th>Tgl Masuk Batch</th>
                    <th class="text-right">Qty Diambil</th>
                    <th class="text-right">HPP/Unit</th>
                    <th class="text-right">Subtotal HPP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-pink-50">
                @foreach($outgoing->fifoDetails as $detail)
                <tr>
                    <td>
                        <a href="{{ route('batches.show', $detail->batch) }}"
                           class="font-mono text-xs text-rose-600 hover:underline">{{ $detail->batch?->batch_code }}</a>
                    </td>
                    <td class="text-xs text-gray-500">{{ $detail->batch?->received_date->format('d/m/Y') }}</td>
                    <td class="text-right font-semibold text-orange-600">{{ $detail->qty_taken }}</td>
                    <td class="text-right text-sm">Rp {{ number_format($detail->cost_price, 0, ',', '.') }}</td>
                    <td class="text-right font-semibold text-rose-600">Rp {{ number_format($detail->subtotal_hpp, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="bg-pink-50/50">
                    <td colspan="2" class="font-semibold text-gray-700 text-right pr-4">Total HPP</td>
                    <td class="text-right font-bold text-orange-600">{{ $outgoing->quantity }}</td>
                    <td></td>
                    <td class="text-right font-bold text-rose-600">Rp {{ number_format($outgoing->total_hpp, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <a href="{{ route('outgoing.index') }}" class="btn-secondary inline-block">← Kembali</a>
</div>
@endsection
