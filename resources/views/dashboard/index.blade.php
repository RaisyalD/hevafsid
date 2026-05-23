@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Ringkasan bisnis hari ini')

@php
    $monthNames = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];
    $periodLabel = $monthNames[$month] . ' ' . $year;
@endphp

@section('content')

{{-- ── Period Selector ────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-5">
    <div>
        @if(!$isCurrentMonth)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Melihat data historis: {{ $periodLabel }}
        </span>
        @endif
    </div>

    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
        <select name="month" class="input text-sm w-36 py-1.5">
            @foreach($monthNames as $m => $name)
                @php $disabled = ($year == now()->year && $m > now()->month); @endphp
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        <select name="year" class="input text-sm w-24 py-1.5">
            @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary py-1.5 text-sm">Tampilkan</button>
        @if(!$isCurrentMonth)
        <a href="{{ route('dashboard') }}" class="btn-secondary py-1.5 text-sm">Hari Ini</a>
        @endif
    </form>
</div>

{{-- ── KPI Widgets ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-4 mb-6">

    {{-- Total Produk --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100">
                <svg class="h-5 w-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <span class="text-xs text-gray-400">Total</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</div>
        <div class="text-xs text-gray-500 mt-1">Produk Aktif</div>
    </div>

    {{-- Total Stok --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <span class="text-xs text-gray-400">Semua SKU</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_stock']) }}</div>
        <div class="text-xs text-gray-500 mt-1">Total Unit Stok</div>
    </div>

    {{-- Unit Masuk --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
                </svg>
            </div>
            <span class="text-xs text-gray-400">{{ $isCurrentMonth ? 'Hari ini' : $periodLabel }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['incoming_today']) }}</div>
        <div class="text-xs text-gray-500 mt-1">Unit Masuk</div>
    </div>

    {{-- Unit Keluar --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100">
                <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m0 0l-5-5m5 5l5-5"/>
                </svg>
            </div>
            <span class="text-xs text-gray-400">{{ $isCurrentMonth ? 'Hari ini' : $periodLabel }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['outgoing_today']) }}</div>
        <div class="text-xs text-gray-500 mt-1">Unit Keluar</div>
    </div>
</div>

{{-- ── Revenue & Profit Cards ────────────────────────────────────────── --}}
<div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">

    <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white">
        <div class="text-sm font-medium text-pink-100 mb-1">
            {{ $isCurrentMonth ? 'Pendapatan Hari Ini' : 'Pendapatan ' . $periodLabel }}
        </div>
        <div class="text-3xl font-bold">
            Rp {{ $isCurrentMonth ? number_format($stats['revenue_today'], 0, ',', '.') : number_format($stats['revenue_month'], 0, ',', '.') }}
        </div>
        <div class="text-xs text-pink-200 mt-2">Dari penjualan produk</div>
    </div>

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
        <div class="text-sm font-medium text-gray-500 mb-1">Omzet {{ $periodLabel }}</div>
        <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</div>
        <div class="text-xs text-gray-400 mt-2">Total penjualan bulan ini</div>
    </div>

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
        <div class="text-sm font-medium text-gray-500 mb-1">Laba Kotor {{ $periodLabel }}</div>
        <div class="text-3xl font-bold {{ $stats['gross_profit_month'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            Rp {{ number_format($stats['gross_profit_month'], 0, ',', '.') }}
        </div>
        <div class="text-xs text-gray-400 mt-2">Setelah HPP (FIFO)</div>
    </div>
</div>

{{-- ── Charts ────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">

    {{-- Sales Chart --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Grafik Penjualan</h3>
                <p class="text-xs text-gray-400">
                    {{ $isCurrentMonth ? '30 hari terakhir' : $periodLabel }}
                </p>
            </div>
        </div>
        <canvas id="salesChart" height="200"></canvas>
    </div>

    {{-- Cashflow Chart --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Arus Kas</h3>
                <p class="text-xs text-gray-400">
                    {{ $isCurrentMonth ? '30 hari terakhir' : $periodLabel }}
                </p>
            </div>
        </div>
        <canvas id="cashflowChart" height="200"></canvas>
    </div>
</div>

{{-- ── Bottom grid: Low Stock + Recent Transactions ──────────────────── --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- Low Stock Warning --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm">
        <div class="flex items-center justify-between border-b border-pink-50 px-6 py-4">
            <h3 class="text-sm font-semibold text-gray-900">Stok Menipis</h3>
            <a href="{{ route('products.index', ['stock_filter' => 'low']) }}"
               class="text-xs text-rose-500 hover:text-rose-700">Lihat semua →</a>
        </div>
        @forelse($lowStockProducts as $product)
        <div class="flex items-center justify-between px-6 py-3 hover:bg-pink-50/50 transition border-b border-pink-50/50 last:border-0">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-100 text-pink-600 text-xs font-bold">
                    {{ strtoupper(substr($product->name, 0, 2)) }}
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                    <div class="text-xs text-gray-400">{{ $product->sku }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold {{ $product->stock_total == 0 ? 'text-red-600' : 'text-amber-600' }}">
                    {{ $product->stock_total }} {{ $product->unit }}
                </div>
                <div class="text-xs text-gray-400">min: {{ $product->min_stock }}</div>
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-sm text-gray-400">
            Tidak ada produk dengan stok menipis
        </div>
        @endforelse
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm">
        <div class="flex items-center justify-between border-b border-pink-50 px-6 py-4">
            <h3 class="text-sm font-semibold text-gray-900">
                Transaksi {{ $isCurrentMonth ? 'Terbaru' : $periodLabel }}
            </h3>
            <a href="{{ route('outgoing.index') }}" class="text-xs text-rose-500 hover:text-rose-700">Lihat semua →</a>
        </div>
        @forelse($recentTransactions as $trx)
        <div class="flex items-center justify-between px-6 py-3 hover:bg-pink-50/50 transition border-b border-pink-50/50 last:border-0">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-600 flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m0 0l-5-5m5 5l5-5"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $trx->product?->name }}</div>
                    <div class="text-xs text-gray-400">{{ $trx->transaction_code }}</div>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($trx->total_revenue, 0, ',', '.') }}</div>
                <div class="text-xs text-gray-400">{{ $trx->transaction_date->format('d/m/Y') }}</div>
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada transaksi bulan ini</div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size = 11;

const labels = @json($chartLabels);

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Pendapatan',
                data: @json($chartRevenue),
                borderColor: '#f43f5e',
                backgroundColor: 'rgba(244,63,94,0.08)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: '#f43f5e',
            },
            {
                label: 'Laba Kotor',
                data: @json($chartProfit),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: '#10b981',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 16 } },
            tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(244,63,94,0.05)' },
                ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' }
            },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('cashflowChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Kas Masuk',
                data: @json($cashIn),
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Kas Keluar',
                data: @json($cashOut),
                backgroundColor: 'rgba(244,63,94,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 16 } },
            tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(244,63,94,0.05)' },
                ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush