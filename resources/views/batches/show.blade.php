@extends('layouts.app')
@section('title', 'Detail Batch ' . $batch->batch_code)
@section('page-title', 'Detail Batch FIFO')
@section('breadcrumb', 'Stok → Batch FIFO → ' . $batch->batch_code)

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
            <h2 class="text-lg font-bold font-mono text-gray-900 mb-1">{{ $batch->batch_code }}</h2>
            <p class="text-xs text-gray-400 mb-5">Diterima {{ $batch->received_date->translatedFormat('d F Y') }}</p>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Produk</span>
                    <span class="font-medium text-gray-900">{{ $batch->product?->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">SKU</span>
                    <span class="font-mono text-gray-700">{{ $batch->product?->sku }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kategori</span>
                    <span class="badge-pink">{{ $batch->product?->category?->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Qty Awal</span>
                    <span class="font-bold text-gray-900">{{ $batch->qty_initial }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Qty Sisa</span>
                    <span class="font-bold text-2xl {{ $batch->qty_remaining == 0 ? 'text-gray-400' : 'text-emerald-600' }}">
                        {{ $batch->qty_remaining }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Terpakai</span>
                    <span class="font-medium text-orange-600">{{ $batch->used_qty }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Modal/Unit</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($batch->cost_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nilai Tersisa</span>
                    <span class="font-semibold text-rose-600">Rp {{ number_format($batch->qty_remaining * $batch->cost_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="{{ match($batch->status) { 'active' => 'badge-green', 'depleted' => 'badge-red', default => 'badge-yellow' } }}">
                        {{ ucfirst($batch->status) }}
                    </span>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="mt-5">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Terpakai</span>
                    <span>{{ $batch->qty_initial > 0 ? round($batch->used_qty / $batch->qty_initial * 100) : 0 }}%</span>
                </div>
                <div class="h-2 bg-pink-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-rose-500 to-pink-500 rounded-full transition-all"
                         style="width: {{ $batch->qty_initial > 0 ? ($batch->used_qty / $batch->qty_initial * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- FIFO usage history --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <div class="border-b border-pink-50 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900">Riwayat Pemakaian Batch (FIFO)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Setiap transaksi keluar yang menggunakan batch ini</p>
            </div>
            <table class="table w-full">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tgl Transaksi</th>
                        <th class="text-right">Qty Diambil</th>
                        <th class="text-right">HPP/Unit</th>
                        <th class="text-right">Subtotal HPP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($batch->fifoDetails as $detail)
                    <tr>
                        <td>
                            <a href="{{ route('outgoing.show', $detail->outgoingTransaction) }}"
                               class="font-mono text-xs text-rose-600 hover:underline">
                                {{ $detail->outgoingTransaction?->transaction_code }}
                            </a>
                        </td>
                        <td class="text-xs text-gray-500">
                            {{ $detail->outgoingTransaction?->transaction_date->format('d/m/Y') }}
                        </td>
                        <td class="text-right font-semibold text-orange-600">{{ $detail->qty_taken }}</td>
                        <td class="text-right text-sm">Rp {{ number_format($detail->cost_price, 0, ',', '.') }}</td>
                        <td class="text-right font-semibold text-rose-600">Rp {{ number_format($detail->subtotal_hpp, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-sm text-gray-400">Batch belum digunakan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('batches.index') }}" class="btn-secondary">← Kembali ke Daftar Batch</a>
</div>
@endsection
