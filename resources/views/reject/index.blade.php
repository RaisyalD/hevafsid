@extends('layouts.app')
@section('title', 'Barang Reject')
@section('page-title', 'Daftar Barang Reject')
@section('breadcrumb', 'Transaksi → Barang Reject')

@section('content')

<div class="flex flex-wrap items-end gap-3 mb-6">
    <div class="rounded-2xl bg-red-50 border border-red-200 px-5 py-4 flex-1 min-w-48">
        <div class="text-xs text-gray-500 mb-1">Total Kerugian Bulan Ini</div>
        <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalLoss, 0, ',', '.') }}</div>
    </div>

    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / produk…" class="input w-48">
        <select name="reject_type" class="input w-36">
            <option value="">Semua Jenis</option>
            @foreach(['damaged' => 'Rusak', 'defective' => 'Cacat', 'expired' => 'Kadaluarsa', 'lost' => 'Hilang', 'other' => 'Lainnya'] as $val => $label)
            <option value="{{ $val }}" {{ request('reject_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="input w-36">
        <button type="submit" class="btn-primary">Filter</button>
    </form>

    <a href="{{ route('reject.create') }}" class="btn-primary flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Input Reject
    </a>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode Reject</th>
                <th>Produk</th>
                <th>Batch</th>
                <th>Tgl Reject</th>
                <th class="text-right">Qty</th>
                <th>Jenis</th>
                <th class="text-right">Kerugian</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($rejects as $rej)
            <tr>
                <td class="font-mono text-xs text-gray-600">{{ $rej->reject_code }}</td>
                <td>
                    <div class="font-medium text-gray-900">{{ $rej->product?->name }}</div>
                    <div class="font-mono text-xs text-gray-400">{{ $rej->product?->sku }}</div>
                </td>
                <td class="font-mono text-xs text-gray-500">{{ $rej->batch?->batch_code ?? '—' }}</td>
                <td class="text-sm text-gray-600">{{ $rej->reject_date->format('d/m/Y') }}</td>
                <td class="text-right font-bold text-red-600">-{{ $rej->quantity }}</td>
                <td>
                    @php
                    $rejectClass = match($rej->reject_type) {
                        'damaged'   => 'badge-red',
                        'defective' => 'badge-yellow',
                        'expired'   => 'badge-yellow',
                        'lost'      => 'badge-red',
                        default     => 'badge-blue',
                    };
                    @endphp
                    <span class="{{ $rejectClass }}">{{ $rej->reject_type_label }}</span>
                </td>
                <td class="text-right font-semibold text-red-600">Rp {{ number_format($rej->total_loss, 0, ',', '.') }}</td>
                <td class="text-sm text-gray-600 max-w-xs truncate" title="{{ $rej->reason }}">{{ $rej->reason }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="py-10 text-center text-sm text-gray-400">Tidak ada data reject</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($rejects->hasPages())
    <div class="border-t border-pink-50 px-6 py-4">{{ $rejects->links() }}</div>
    @endif
</div>
@endsection
