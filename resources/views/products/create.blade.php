@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')
@section('breadcrumb', 'Master Barang → Tambah')

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
      x-data="productForm()">
    @csrf

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 space-y-5">

        <div class="grid grid-cols-2 gap-4">
            {{-- Kategori --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="input">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Produk --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Hijab Segi Empat Premium…" class="input">
            </div>

            {{-- Satuan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                <select name="unit" class="input">
                    @foreach(['pcs', 'lusin', 'dozen', 'box', 'set', 'pasang'] as $u)
                    <option value="{{ $u }}" {{ old('unit', 'pcs') === $u ? 'selected' : '' }}>{{ strtoupper($u) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Min Stok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Stok <span class="text-rose-500">*</span></label>
                <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required class="input">
                <p class="text-xs text-gray-400 mt-1">Peringatan stok menipis</p>
            </div>

            {{-- Harga Jual --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Jual <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                    <input type="number" name="sell_price" value="{{ old('sell_price') }}" min="0" required
                           class="input pl-8" placeholder="0">
                </div>
            </div>

            {{-- Harga Modal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Modal Default</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                    <input type="number" name="default_cost_price" value="{{ old('default_cost_price') }}" min="0"
                           class="input pl-8" placeholder="0">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" class="input resize-none"
                          placeholder="Deskripsi produk (opsional)…">{{ old('description') }}</textarea>
            </div>

            {{-- Foto --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Produk</label>
                <div class="border-2 border-dashed border-pink-200 rounded-xl p-6 text-center hover:border-rose-400 transition cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <template x-if="!preview">
                        <div>
                            <svg class="mx-auto h-10 w-10 text-pink-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG max 2MB</p>
                        </div>
                    </template>
                    <template x-if="preview">
                        <img :src="preview" class="mx-auto max-h-40 rounded-xl object-contain">
                    </template>
                    <input type="file" name="image" accept="image/*" x-ref="fileInput" class="hidden"
                           @change="previewImage($event)">
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-4">
        <a href="{{ route('products.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Produk</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function productForm() {
    return {
        preview: null,
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => this.preview = e.target.result;
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
@endpush
