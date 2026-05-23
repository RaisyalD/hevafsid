@extends('layouts.app')
@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')
@section('breadcrumb', 'Master Data → Kategori')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Category list --}}
    <div class="lg:col-span-2">
        <div class="flex items-center gap-2 mb-4">
            <form method="GET" class="flex gap-2 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori…" class="input flex-1">
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <table class="table w-full">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Produk</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($categories as $cat)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">{{ $cat->code }}</td>
                        <td class="font-medium text-gray-900">{{ $cat->name }}</td>
                        <td class="text-sm text-gray-500 max-w-xs truncate">{{ $cat->description ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge-blue">{{ $cat->products_count }}</span>
                        </td>
                        <td>
                            <span class="{{ $cat->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-1 justify-end">
                                <button onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}')"
                                        class="rounded-lg p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($cat->products_count == 0)
                                <form id="del-cat-{{ $cat->id }}" method="POST" action="{{ route('categories.destroy', $cat) }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="openConfirm('Hapus Kategori', 'Kategori ini akan dihapus permanen dan tidak dapat dikembalikan.', 'Ya, Hapus', 'del-cat-{{ $cat->id }}')"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-sm text-gray-400">Belum ada kategori</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($categories->hasPages())
            <div class="border-t border-pink-50 px-6 py-4">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Add/Edit form --}}
    <div x-data="{ editing: false, editId: null, editName: '', editDesc: '' }">
        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4" x-text="editing ? 'Edit Kategori' : 'Tambah Kategori'"></h3>

            {{-- Add form --}}
            <form x-show="!editing" method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="input text-sm" placeholder="Hijab Voal…">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="input text-sm resize-none" placeholder="Keterangan kategori…"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full text-sm">Tambah Kategori</button>
            </form>

            {{-- Edit form --}}
            <template x-if="editing">
                <form :action="`/categories/${editId}`" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kategori</label>
                        <input type="text" name="name" x-model="editName" required class="input text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                        <textarea name="description" x-model="editDesc" rows="3" class="input text-sm resize-none"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary flex-1 text-sm">Simpan</button>
                        <button type="button" @click="editing = false" class="btn-secondary text-sm px-3">Batal</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, desc) {
    // Trigger Alpine data via a custom event approach
    document.querySelector('[x-data]').__x.$data.editing = true;
    document.querySelector('[x-data]').__x.$data.editId = id;
    document.querySelector('[x-data]').__x.$data.editName = name;
    document.querySelector('[x-data]').__x.$data.editDesc = desc;
}
</script>
@endsection
