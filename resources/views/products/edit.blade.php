@extends('layouts.app')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('breadcrumb', 'Master Barang → Edit')

@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
      x-data="{ preview: '{{ $product->image ? $product->image_url : '' }}' }">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="input">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan</label>
                <select name="unit" class="input">
                    @foreach(['pcs', 'lusin', 'dozen', 'box', 'set', 'pasang'] as $u)
                    <option value="{{ $u }}" {{ $product->unit === $u ? 'selected' : '' }}>{{ strtoupper($u) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Stok</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" class="input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Jual <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                    <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" min="0" required class="input pl-8">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Modal Default</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                    <input type="number" name="default_cost_price" value="{{ old('default_cost_price', $product->default_cost_price) }}" min="0" class="input pl-8">
                </div>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" class="input resize-none">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Produk</label>
                <div class="border-2 border-dashed border-pink-200 rounded-xl p-4 text-center cursor-pointer hover:border-rose-400 transition"
                     @click="$refs.fileInput.click()">
                    <template x-if="preview">
                        <img :src="preview" class="mx-auto max-h-32 rounded-lg object-contain mb-2">
                    </template>
                    <p class="text-xs text-gray-400">Klik untuk ganti foto</p>
                    <input type="file" name="image" accept="image/*" x-ref="fileInput" class="hidden"
                           @change="e => { const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = ev => preview = ev.target.result; r.readAsDataURL(f); } }">
                </div>
            </div>
            <div class="col-span-2 flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="h-4 w-4 rounded border-pink-300 text-rose-500"
                       {{ $product->is_active ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Produk Aktif</label>
            </div>
        </div>
    </div>

    <div class="flex justify-between mt-4">
        <div class="text-xs text-gray-400 flex items-center gap-1">
            <span>SKU:</span> <span class="font-mono font-medium text-gray-600">{{ $product->sku }}</span>
            <span class="mx-2">|</span>
            <span>Barcode:</span> <span class="font-mono font-medium text-gray-600">{{ $product->barcode }}</span>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('products.show', $product) }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</form>
</div>
@endsection
