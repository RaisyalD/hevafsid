@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Pusat Laporan')
@section('breadcrumb', 'Laporan')

@section('content')

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

    @php
    $reports = [
        [
            'title'       => 'Laporan Stok',
            'desc'        => 'Daftar semua produk beserta stok saat ini dan valuasi FIFO.',
            'icon'        => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
            'route'       => 'reports.stock',
            'color'       => 'blue',
            'exports'     => ['excel', 'pdf'],
        ],
        [
            'title'       => 'Laporan Penjualan',
            'desc'        => 'Riwayat transaksi penjualan dengan detail HPP FIFO dan laba kotor.',
            'icon'        => 'M17 8V4m0 0l4 4m-4-4l-4 4M7 16v4m0 0l-4-4m4 4l4-4',
            'route'       => 'reports.sales',
            'color'       => 'emerald',
            'exports'     => ['excel', 'pdf'],
        ],
        [
            'title'       => 'Laporan FIFO Batch',
            'desc'        => 'Status semua batch stok, termasuk batch aktif, habis, dan expired.',
            'icon'        => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4',
            'route'       => 'reports.fifo',
            'color'       => 'purple',
            'exports'     => ['pdf'],
        ],
        [
            'title'       => 'Laporan Reject',
            'desc'        => 'Rekap barang reject per periode dengan total nilai kerugian.',
            'icon'        => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
            'route'       => 'reports.reject',
            'color'       => 'red',
            'exports'     => ['pdf'],
        ],
        [
            'title'       => 'Laporan Keuangan',
            'desc'        => 'Jurnal kas masuk dan keluar per periode dengan ringkasan saldo.',
            'icon'        => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
            'route'       => 'reports.financial',
            'color'       => 'rose',
            'exports'     => ['excel', 'pdf'],
        ],
        [
            'title'       => 'Laba Rugi',
            'desc'        => 'Laporan laba rugi bulanan: pendapatan, HPP, laba kotor, dan laba bersih.',
            'icon'        => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'route'       => 'financial.profit-loss',
            'color'       => 'pink',
            'exports'     => ['pdf'],
        ],
    ];

    $colorMap = [
        'blue'    => ['bg' => 'bg-blue-100',   'icon' => 'text-blue-600',   'btn' => 'bg-blue-500 hover:bg-blue-600'],
        'emerald' => ['bg' => 'bg-emerald-100', 'icon' => 'text-emerald-600','btn' => 'bg-emerald-500 hover:bg-emerald-600'],
        'purple'  => ['bg' => 'bg-purple-100',  'icon' => 'text-purple-600', 'btn' => 'bg-purple-500 hover:bg-purple-600'],
        'red'     => ['bg' => 'bg-red-100',     'icon' => 'text-red-600',    'btn' => 'bg-red-500 hover:bg-red-600'],
        'rose'    => ['bg' => 'bg-rose-100',    'icon' => 'text-rose-600',   'btn' => 'bg-rose-500 hover:bg-rose-600'],
        'pink'    => ['bg' => 'bg-pink-100',    'icon' => 'text-pink-600',   'btn' => 'bg-pink-500 hover:bg-pink-600'],
    ];
    @endphp

    @foreach($reports as $report)
    @php $c = $colorMap[$report['color']]; @endphp
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 flex flex-col">
        <div class="flex items-center gap-4 mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $c['bg'] }} flex-shrink-0">
                <svg class="h-6 w-6 {{ $c['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $report['icon'] }}"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ $report['title'] }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $report['desc'] }}</p>
            </div>
        </div>

        <div class="mt-auto pt-4 flex gap-2">
            <a href="{{ route($report['route']) }}"
               class="{{ $c['btn'] }} flex-1 text-center text-sm font-medium text-white rounded-xl py-2 transition">
                Lihat Laporan
            </a>
            @if(in_array('pdf', $report['exports']))
            <a href="{{ route($report['route'], ['format' => 'pdf']) }}"
               class="border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm rounded-xl px-3 py-2 transition" title="Export PDF">
                PDF
            </a>
            @endif
            @if(in_array('excel', $report['exports']))
            <a href="{{ route($report['route'], ['format' => 'excel']) }}"
               class="border border-emerald-200 hover:bg-emerald-50 text-emerald-600 text-sm rounded-xl px-3 py-2 transition" title="Export Excel">
                XLS
            </a>
            @endif
        </div>
    </div>
    @endforeach

</div>
@endsection
