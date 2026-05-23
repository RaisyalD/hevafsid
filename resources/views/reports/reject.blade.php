@extends('layouts.app')
@section('title', 'Laporan Reject')
@section('page-title', 'Laporan Barang Reject')
@section('breadcrumb', 'Laporan → Reject')

@section('content')

<div class="flex flex-wrap gap-3 mb-6 items-end">
    <form method="GET" class="flex gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="input w-36">
        <button type="submit" class="btn-primary">Filter</button>
    </form>
    <a href="{{ route('reports.reject', array_merge(request()->all(), ['format' => 'pdf'])) }}"
       class="btn-secondary text-sm">Export PDF</a>
    <a href="{{ route('reports.index') }}" class="btn-secondary text-sm ml-auto">← Kembali</a>
</div>

<div class="bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex items-center justify-between mb-6">
    <div>
        <div class="text-xs text-gray-400">Total Kerugian Periode Ini</div>
        <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalLoss, 0, ',', '.') }}</div>
    </div>
    <div class="text-xs text-gray-400">{{ $dateFrom }} s/d {{ $dateTo }}</div>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode Reject</th>
                <th>Produk</th>
                <th>Tgl Reject</th>
                <th>Jenis</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Kerugian</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($rejects as $rej)
            <tr>
                <td class="font-mono text-xs text-gray-500">{{ $rej->reject_code }}</td>
                <td class="font-medium text-gray-900">{{ $rej->product?->name }}</td>
                <td class="text-xs text-gray-500">{{ $rej->reject_date->format('d/m/Y') }}</td>
                <td><span class="badge-yellow">{{ $rej->reject_type_label }}</span></td>
                <td class="text-right font-bold text-red-600">{{ $rej->quantity }}</td>
                <td class="text-right font-semibold text-red-600">Rp {{ number_format($rej->total_loss, 0, ',', '.') }}</td>
                <td class="text-gray-600 max-w-xs truncate">{{ $rej->reason }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-10 text-center text-gray-400">Tidak ada data reject</td></tr>
            @endforelse
        </tbody>
        @if($rejects->count() > 0)
        <tfoot class="bg-pink-50/50 font-bold">
            <tr>
                <td colspan="5" class="px-4 py-3 text-right">Total Kerugian</td>
                <td class="text-right px-4 py-3 text-red-600">Rp {{ number_format($totalLoss, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
