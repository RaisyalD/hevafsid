@extends('layouts.app')
@section('title', $incoming->transaction_code)
@section('page-title', 'Detail Barang Masuk')
@section('breadcrumb', 'Transaksi → Barang Masuk → ' . $incoming->transaction_code)

@section('content')
<div class="max-w-2xl">
<div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900 font-mono">{{ $incoming->transaction_code }}</h2>
            <p class="text-sm text-gray-500">{{ $incoming->received_date->translatedFormat('d F Y') }}</p>
        </div>
        <span class="badge-green text-base px-4 py-1.5">✓ Masuk</span>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="rounded-xl bg-pink-50 p-4">
                <div class="text-xs text-gray-400 mb-1">Produk</div>
                <div class="font-semibold text-gray-900">{{ $incoming->product?->name }}</div>
                <div class="text-xs font-mono text-gray-500 mt-1">{{ $incoming->product?->sku }}</div>
            </div>
            <div class="rounded-xl bg-pink-50 p-4">
                <div class="text-xs text-gray-400 mb-1">Supplier</div>
                <div class="font-semibold text-gray-900">{{ $incoming->supplier?->name ?? '—' }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $incoming->invoice_number ?? 'Tanpa faktur' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                <div class="text-xs text-gray-500 mb-1">Jumlah</div>
                <div class="text-2xl font-bold text-emerald-600">{{ $incoming->quantity }}</div>
                <div class="text-xs text-gray-400">{{ $incoming->product?->unit }}</div>
            </div>
            <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                <div class="text-xs text-gray-500 mb-1">Modal/Unit</div>
                <div class="text-xl font-bold text-gray-900">Rp {{ number_format($incoming->cost_price, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                <div class="text-xs text-gray-500 mb-1">Total Nilai</div>
                <div class="text-xl font-bold text-rose-600">Rp {{ number_format($incoming->total_cost, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 flex justify-between items-center">
            <div>
                <div class="text-xs text-gray-400 mb-0.5">Batch FIFO Dibuat</div>
                <div class="font-mono font-bold text-blue-700">{{ $incoming->batch?->batch_code }}</div>
            </div>
            <a href="{{ route('batches.show', $incoming->batch) }}"
               class="text-xs text-blue-600 hover:text-blue-800 border border-blue-200 rounded-lg px-3 py-1.5 hover:bg-blue-100 transition">
                Lihat Batch →
            </a>
        </div>

        @if($incoming->notes)
        <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-600">
            <span class="font-medium">Catatan:</span> {{ $incoming->notes }}
        </div>
        @endif

        <div class="text-xs text-gray-400 flex justify-between">
            <span>Diinput oleh: {{ $incoming->user?->name }}</span>
            <span>{{ $incoming->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('incoming.index') }}" class="btn-secondary">← Kembali</a>
</div>
</div>
@endsection
