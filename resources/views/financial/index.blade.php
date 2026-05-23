@extends('layouts.app')
@section('title', 'Kas & Keuangan')
@section('page-title', 'Kas & Keuangan')
@section('breadcrumb', 'Keuangan → Jurnal Umum')

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5">
        <div class="text-xs text-gray-500 mb-1">Total Kas Masuk</div>
        <div class="text-2xl font-bold text-emerald-600">Rp {{ number_format($summary->total_in ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5">
        <div class="text-xs text-gray-500 mb-1">Total Kas Keluar</div>
        <div class="text-2xl font-bold text-red-600">Rp {{ number_format($summary->total_out ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-5 text-white">
        <div class="text-xs text-pink-100 mb-1">Saldo Bersih</div>
        <div class="text-2xl font-bold">Rp {{ number_format($summary->net ?? 0, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Transaction list --}}
    <div class="lg:col-span-2">
        <div class="flex flex-wrap gap-2 mb-4">
            <form method="GET" class="flex flex-wrap gap-2 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / deskripsi…" class="input w-52">
                <select name="type" class="input w-32">
                    <option value="">Semua Tipe</option>
                    <option value="cash_in"  {{ request('type') === 'cash_in'  ? 'selected' : '' }}>Kas Masuk</option>
                    <option value="cash_out" {{ request('type') === 'cash_out' ? 'selected' : '' }}>Kas Keluar</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input w-36">
                <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="input w-36">
                <button type="submit" class="btn-primary">Filter</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="table w-full min-w-[720px]">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th class="w-36">Kode</th>
                        <th>Deskripsi</th>
                        <th class="w-24">Tgl</th>
                        <th class="w-28">Tipe</th>
                        <th class="w-28">Kategori</th>
                        <th class="w-36 text-right">Jumlah</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="font-mono text-xs text-gray-500 whitespace-nowrap">{{ $trx->transaction_code }}</td>
                        <td class="text-sm text-gray-700">
                            <div class="truncate max-w-[200px]">{{ $trx->description }}</div>
                        </td>
                        <td class="text-xs text-gray-500 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="{{ $trx->type === 'cash_in' ? 'badge-green' : 'badge-red' }}">
                                {{ $trx->type_label }}
                            </span>
                        </td>
                        <td><span class="badge-blue">{{ $trx->category_label }}</span></td>
                        <td class="text-right font-bold {{ $trx->type === 'cash_in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $trx->type === 'cash_in' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @if(in_array($trx->category, ['operational', 'other']))
                            <form id="del-fin-{{ $trx->id }}" method="POST" action="{{ route('financial.destroy', $trx) }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openConfirm('Hapus Transaksi', 'Transaksi keuangan ini akan dihapus permanen dan tidak dapat dikembalikan.', 'Ya, Hapus', 'del-fin-{{ $trx->id }}')"
                                        class="rounded-lg p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-sm text-gray-400">Belum ada transaksi keuangan</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            @if($transactions->hasPages())
            <div class="border-t border-pink-50 px-6 py-4">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Quick input form --}}
    <div>
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Input Transaksi Manual</h3>
            <form method="POST" action="{{ route('financial.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe <span class="text-rose-500">*</span></label>
                    <select name="type" required class="input text-sm">
                        <option value="cash_in">Kas Masuk</option>
                        <option value="cash_out">Kas Keluar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" required class="input text-sm">
                        <option value="operational">Operasional</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-gray-400">Rp</span>
                        <input type="number" name="amount" min="0" required class="input pl-8 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi <span class="text-rose-500">*</span></label>
                    <input type="text" name="description" required class="input text-sm" placeholder="Keterangan transaksi…">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ today()->format('Y-m-d') }}" required class="input text-sm">
                </div>
                <button type="submit" class="btn-primary w-full text-sm">Simpan Transaksi</button>
            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('financial.profit-loss') }}"
               class="block bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-5 text-white hover:shadow-lg transition">
                <div class="text-sm font-semibold mb-1">Laporan Laba Rugi</div>
                <div class="text-xs text-pink-100">Lihat laporan profit/loss bulanan →</div>
            </a>
        </div>
    </div>
</div>

@endsection
