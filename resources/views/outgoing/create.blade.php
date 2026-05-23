@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Input Barang Keluar')
@section('breadcrumb', 'Transaksi → Barang Keluar → Input')

@section('content')
<div class="max-w-2xl" x-data="outgoingForm()">

    {{-- Scanner --}}
    <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-5 text-white mb-6">
        <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Scan Barcode Produk
        </h3>
        <div class="flex gap-2">
            <input type="text" x-model="barcodeInput" @keydown.enter.prevent="lookupBarcode()"
                   placeholder="Scan atau ketik barcode…"
                   class="flex-1 rounded-xl bg-white/20 border border-white/30 px-4 py-2.5 text-sm text-white placeholder-amber-200 focus:outline-none focus:bg-white/30">
            <button type="button" @click="lookupBarcode()"
                    class="rounded-xl bg-white/20 border border-white/30 px-4 py-2.5 text-sm font-medium hover:bg-white/30 transition">
                Cari
            </button>
        </div>
        <p x-show="scanMessage" x-text="scanMessage" class="text-xs text-amber-200 mt-2"></p>
    </div>

    <form method="POST" action="{{ route('outgoing.store') }}">
        @csrf

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 space-y-5">

            {{-- Produk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Produk <span class="text-rose-500">*</span></label>
                <select name="product_id" x-model="selectedProductId" required class="input"
                        @change="onProductChange()">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}"
                            data-stock="{{ $product->stock_total }}"
                            data-price="{{ $product->sell_price }}"
                            data-unit="{{ $product->unit }}"
                            {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (Stok: {{ $product->stock_total }})
                    </option>
                    @endforeach
                </select>

                <div x-show="selectedProductName"
                     class="mt-2 rounded-xl bg-orange-50 border border-orange-100 px-4 py-3 flex justify-between items-center text-sm">
                    <span class="font-medium text-orange-700" x-text="selectedProductName"></span>
                    <span class="text-gray-500">Stok: <strong class="text-gray-900" x-text="availableStock + ' ' + unit"></strong></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Jumlah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Keluar <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" x-model="quantity" min="1" :max="availableStock" required
                           value="{{ old('quantity', 1) }}" class="input" @input="calcProfit()">
                    <p x-show="quantity > availableStock" class="text-xs text-red-500 mt-1">Melebihi stok tersedia!</p>
                </div>

                {{-- Harga Jual --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Jual/Unit <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                        <input type="number" name="sell_price" x-model="sellPrice" min="0" required
                               value="{{ old('sell_price') }}" class="input pl-8" @input="calcProfit()">
                    </div>
                </div>
            </div>

            {{-- Revenue preview --}}
            <div x-show="totalRevenue > 0"
                 class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex justify-between">
                <span class="text-sm font-medium text-emerald-700">Estimasi Pendapatan</span>
                <span class="text-lg font-bold text-emerald-700"
                      x-text="'Rp ' + totalRevenue.toLocaleString('id-ID')"></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date"
                           value="{{ old('transaction_date', today()->format('Y-m-d')) }}" required class="input">
                </div>

                {{-- No Referensi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Nota / Referensi</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                           placeholder="NOTA-001" class="input">
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="input resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-xs text-blue-700">
                <strong>ℹ️ FIFO Otomatis:</strong> Sistem akan mengambil stok dari batch paling lama secara otomatis
                dan menghitung HPP secara akurat.
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('outgoing.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" :disabled="quantity > availableStock && availableStock > 0"
                    class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                Proses Barang Keluar
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function outgoingForm() {
    return {
        barcodeInput: '',
        scanMessage: '',
        selectedProductId: '{{ old('product_id', request('product_id')) }}',
        selectedProductName: '',
        availableStock: 0,
        unit: '',
        quantity: {{ old('quantity', 1) }},
        sellPrice: {{ old('sell_price', 0) }},
        totalRevenue: 0,

        init() {
            this.$nextTick(() => this.onProductChange());
        },

        async lookupBarcode() {
            if (!this.barcodeInput) return;
            this.scanMessage = 'Mencari…';
            try {
                const res = await fetch(`/api/products/by-barcode?barcode=${encodeURIComponent(this.barcodeInput)}`);
                if (!res.ok) { this.scanMessage = '❌ Tidak ditemukan'; return; }
                const data = await res.json();
                this.selectedProductId = String(data.id);
                document.querySelector('select[name=product_id]').value = data.id;
                this.selectedProductName = data.name;
                this.availableStock = data.stock_total;
                this.unit = data.unit;
                this.sellPrice = data.sell_price;
                this.calcProfit();
                this.scanMessage = `✅ ${data.name} — Stok: ${data.stock_total}`;
            } catch(e) {
                this.scanMessage = '❌ Error';
            }
        },

        onProductChange() {
            const sel = document.querySelector('select[name=product_id]');
            const opt = sel?.options[sel.selectedIndex];
            if (opt && opt.value) {
                this.selectedProductName = opt.text.split(' (Stok')[0];
                this.availableStock = parseInt(opt.dataset.stock) || 0;
                this.sellPrice = parseFloat(opt.dataset.price) || this.sellPrice;
                this.unit = opt.dataset.unit || '';
                this.calcProfit();
            } else {
                this.selectedProductName = '';
            }
        },

        calcProfit() {
            this.totalRevenue = (parseFloat(this.quantity) || 0) * (parseFloat(this.sellPrice) || 0);
        }
    }
}
</script>
@endpush
