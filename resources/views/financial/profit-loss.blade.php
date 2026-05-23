@extends('layouts.app')
@section('title', 'Laporan Laba Rugi')
@section('page-title', 'Laporan Laba Rugi')
@section('breadcrumb', 'Keuangan → Laba Rugi')

@section('content')
<div class="max-w-2xl">

<div class="flex items-center gap-4 mb-6">
    <form method="GET" class="flex gap-2">
        <input type="month" name="month" value="{{ $month }}" class="input">
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
    <a href="{{ route('reports.financial', ['date_from' => $month . '-01', 'date_to' => $month . '-31', 'format' => 'pdf']) }}"
       class="btn-secondary text-sm">Export PDF</a>
</div>

<div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-1">Laporan Laba Rugi</h2>
    <p class="text-sm text-gray-500 mb-6">
        Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
    </p>

    {{-- Revenue section --}}
    <div class="mb-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pendapatan</div>
        <div class="flex justify-between py-2 border-b border-pink-50">
            <span class="text-sm text-gray-700">Pendapatan Penjualan</span>
            <span class="font-semibold text-emerald-700">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 font-bold">
            <span class="text-sm">Total Pendapatan</span>
            <span class="text-emerald-700">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- HPP section --}}
    <div class="mb-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Harga Pokok Penjualan (HPP)</div>
        <div class="flex justify-between py-2 border-b border-pink-50">
            <span class="text-sm text-gray-700">HPP (FIFO method)</span>
            <span class="font-semibold text-red-600">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 font-bold">
            <span class="text-sm">Total HPP</span>
            <span class="text-red-600">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Gross Profit --}}
    <div class="rounded-xl bg-pink-50 border border-pink-100 px-4 py-3 flex justify-between items-center mb-4">
        <span class="font-semibold text-gray-700">Laba Kotor</span>
        <span class="text-xl font-bold {{ $grossProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            Rp {{ number_format($grossProfit, 0, ',', '.') }}
        </span>
    </div>

    {{-- Expenses --}}
    <div class="mb-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Beban Operasional</div>
        <div class="flex justify-between py-2 border-b border-pink-50">
            <span class="text-sm text-gray-700">Kerugian Barang Reject</span>
            <span class="font-semibold text-red-600">Rp {{ number_format($rejectLoss, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 border-b border-pink-50">
            <span class="text-sm text-gray-700">Beban Operasional</span>
            <span class="font-semibold text-red-600">Rp {{ number_format($operational, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 font-bold">
            <span class="text-sm">Total Beban</span>
            <span class="text-red-600">Rp {{ number_format($rejectLoss + $operational, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Net Profit --}}
    <div class="rounded-xl {{ $netProfit >= 0 ? 'bg-emerald-500' : 'bg-red-500' }} p-5 text-white">
        <div class="text-sm text-white/80 mb-1">Laba Bersih</div>
        <div class="text-3xl font-bold">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
        <div class="text-xs text-white/60 mt-2">
            {{ $netProfit >= 0 ? '📈 Bisnis berjalan dengan baik!' : '📉 Perlu evaluasi pengeluaran.' }}
        </div>
    </div>
</div>

</div>
@endsection
