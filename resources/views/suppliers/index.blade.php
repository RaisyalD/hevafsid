@extends('layouts.app')
@section('title', 'Supplier')
@section('page-title', 'Manajemen Supplier')
@section('breadcrumb', 'Master Data → Supplier')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    <div class="lg:col-span-2">
        <div class="flex items-center gap-2 mb-4">
            <form method="GET" class="flex gap-2 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, kota…" class="input flex-1">
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <table class="table w-full">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Supplier</th>
                        <th>Contact</th>
                        <th>Kota</th>
                        <th class="text-center">Transaksi</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($suppliers as $sup)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $sup->code }}</td>
                        <td>
                            <div class="font-medium text-gray-900">{{ $sup->name }}</div>
                            <div class="text-xs text-gray-400">{{ $sup->contact_person ?? '—' }}</div>
                        </td>
                        <td class="text-sm text-gray-600">
                            <div>{{ $sup->phone ?? '—' }}</div>
                            @if($sup->email)<div class="text-xs text-gray-400">{{ $sup->email }}</div>@endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $sup->city ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge-blue">{{ $sup->incoming_transactions_count }}</span>
                        </td>
                        <td>
                            <span class="{{ $sup->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $sup->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            @if($sup->incoming_transactions_count == 0)
                            <form id="del-sup-{{ $sup->id }}" method="POST" action="{{ route('suppliers.destroy', $sup) }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openConfirm('Hapus Supplier', 'Supplier ini akan dihapus permanen dan tidak dapat dikembalikan.', 'Ya, Hapus', 'del-sup-{{ $sup->id }}')"
                                        class="rounded-lg p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-sm text-gray-400">Belum ada supplier</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($suppliers->hasPages())
            <div class="border-t border-pink-50 px-6 py-4">{{ $suppliers->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Add Supplier form --}}
    <div>
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Tambah Supplier</h3>
            <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Supplier <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="input text-sm" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" class="input text-sm" value="{{ old('contact_person') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Telepon</label>
                    <input type="text" name="phone" class="input text-sm" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" class="input text-sm" value="{{ old('email') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kota</label>
                    <input type="text" name="city" class="input text-sm" value="{{ old('city') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                    <textarea name="address" rows="2" class="input text-sm resize-none">{{ old('address') }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full text-sm">Tambah Supplier</button>
            </form>
        </div>
    </div>
</div>
@endsection
