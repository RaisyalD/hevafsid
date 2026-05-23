@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')
@section('breadcrumb', 'Laporan → Keuangan')

@section('content')

<div class="flex flex-wrap gap-3 mb-6 items-end">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="input w-36">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="input w-36">
        <button type="submit" class="btn-primary">Filter</button>
    </form>
    <a href="{{ route('reports.financial', array_merge(request()->all(), ['format' => 'pdf'])) }}"
       class="btn-secondary text-sm">Export PDF</a>
    <a href="{{ route('reports.financial', array_merge(request()->all(), ['format' => 'excel'])) }}"
       class="border border-emerald-200 hover:bg-emerald-50 text-emerald-600 text-sm font-medium px-4 py-2 rounded-xl transition">
       Export Excel
    </a>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 text-center">
        <div class="text-xs text-gray-400 mb-1">Total Kas Masuk</div>
        <div class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5 text-center">
        <div class="text-xs text-gray-400 mb-1">Total Kas Keluar</div>
        <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
    </div>
    <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-5 text-center text-white">
        <div class="text-xs text-pink-100 mb-1">Saldo Bersih</div>
        <div class="text-2xl font-bold">Rp {{ number_format($net, 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
    <table class="table w-full text-sm">
        <thead class="bg-pink-50/50">
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th class="text-right">Jumlah</th>
                <th>Input oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-pink-50">
            @forelse($transactions as $trx)
            <tr>
                <td class="font-mono text-xs text-gray-500">{{ $trx->transaction_code }}</td>
                <td class="text-xs text-gray-500">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                <td class="text-gray-700 max-w-xs truncate">{{ $trx->description }}</td>
                <td>
                    <span class="{{ $trx->type === 'cash_in' ? 'badge-green' : 'badge-red' }}">
                        {{ $trx->type_label }}
                    </span>
                </td>
                <td><span class="badge-blue">{{ $trx->category_label }}</span></td>
                <td class="text-right font-bold {{ $trx->type === 'cash_in' ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $trx->type === 'cash_in' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                </td>
                <td class="text-xs text-gray-400">{{ $trx->user?->name }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-10 text-center text-gray-400">Tidak ada data keuangan</td></tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot class="bg-pink-50/50 font-bold">
            <tr>
                <td colspan="5" class="px-4 py-3 text-right text-gray-600">SALDO BERSIH</td>
                <td class="text-right px-4 py-3 {{ $net >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($net, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
