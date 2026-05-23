@extends('layouts.app')
@section('title', 'Input Reject')
@section('page-title', 'Input Barang Reject')
@section('breadcrumb', 'Transaksi → Barang Reject → Input')

@section('content')
<div class="max-w-2xl" x-data="rejectForm()">
<form method="POST" action="{{ route('reject.store') }}">
    @csrf

    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 space-y-5">

        {{-- Produk --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Produk <span class="text-rose-500">*</span></label>
            <select name="product_id" x-model="selectedProductId" required class="input"
                    @change="loadBatches()">
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-stock="{{ $product->stock_total }}"
                        {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                    {{ $product->name }} (Stok: {{ $product->stock_total }})
                </option>
                @endforeach
            </select>
        </div>

        {{-- Batch --}}
        <div x-show="batches.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dari Batch (opsional)</label>
            <select name="product_batch_id" class="input">
                <option value="">-- Otomatis (FIFO) --</option>
                <template x-for="b in batches" :key="b.id">
                    <option :value="b.id"
                            x-text="`${b.batch_code} — Sisa: ${b.qty_remaining} — Rp ${Number(b.cost_price).toLocaleString('id-ID')}/unit`">
                    </option>
                </template>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- Jumlah --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Reject <span class="text-rose-500">*</span></label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="input">
            </div>

            {{-- Jenis Reject --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Reject <span class="text-rose-500">*</span></label>
                <select name="reject_type" required class="input">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="damaged"   {{ old('reject_type') === 'damaged'   ? 'selected' : '' }}>Rusak / Damaged</option>
                    <option value="defective" {{ old('reject_type') === 'defective' ? 'selected' : '' }}>Cacat Produksi</option>
                    <option value="expired"   {{ old('reject_type') === 'expired'   ? 'selected' : '' }}>Kadaluarsa</option>
                    <option value="lost"      {{ old('reject_type') === 'lost'      ? 'selected' : '' }}>Hilang / Lost</option>
                    <option value="other"     {{ old('reject_type') === 'other'     ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
        </div>

        {{-- Tanggal --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Reject <span class="text-rose-500">*</span></label>
            <input type="date" name="reject_date" value="{{ old('reject_date', today()->format('Y-m-d')) }}" required class="input">
        </div>

        {{-- Alasan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan / Keterangan <span class="text-rose-500">*</span></label>
            <textarea name="reason" rows="3" required class="input resize-none"
                      placeholder="Jelaskan penyebab reject secara rinci…">{{ old('reason') }}</textarea>
        </div>

        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-700">
            <strong>⚠️ Perhatian:</strong> Barang reject akan mengurangi stok secara otomatis dan mencatat kerugian ke laporan keuangan.
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-4">
        <a href="{{ route('reject.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded-xl transition">
            Konfirmasi Reject
        </button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function rejectForm() {
    return {
        selectedProductId: '{{ old('product_id', request('product_id')) }}',
        batches: [],

        init() {
            if (this.selectedProductId) this.loadBatches();
        },

        async loadBatches() {
            if (!this.selectedProductId) { this.batches = []; return; }
            try {
                const res = await fetch(`/api/products/${this.selectedProductId}/batches`);
                this.batches = await res.json();
            } catch(e) {
                this.batches = [];
            }
        }
    }
}
</script>
@endpush
