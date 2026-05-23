@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('page-title', 'Input Barang Masuk')
@section('breadcrumb', 'Transaksi → Barang Masuk → Input')

@section('content')
<div class="max-w-2xl" x-data="incomingForm()">

    {{-- Barcode Scanner UI --}}
    <div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-2xl p-5 text-white mb-6">
        <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Scan Barcode (Opsional)
        </h3>
        <div class="flex gap-2">
            <input type="text" x-model="barcodeInput" @keydown.enter.prevent="lookupBarcode()"
                   placeholder="Scan atau ketik barcode produk…"
                   class="flex-1 rounded-xl bg-white/20 border border-white/30 px-4 py-2.5 text-sm text-white placeholder-pink-200 focus:outline-none focus:bg-white/30">
            <button type="button" @click="lookupBarcode()"
                    class="rounded-xl bg-white/20 border border-white/30 px-4 py-2.5 text-sm font-medium hover:bg-white/30 transition">
                Cari
            </button>
        </div>
        <p x-show="scanMessage" x-text="scanMessage" class="text-xs text-pink-200 mt-2"></p>
    </div>

    <form method="POST" action="{{ route('incoming.store') }}">
        @csrf

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-6 space-y-5">

            {{-- Produk --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Produk <span class="text-rose-500">*</span></label>
                <select name="product_id" x-model="selectedProductId" required class="input"
                        @change="onProductChange()">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->default_cost_price }}"
                            {{ (old('product_id', request('product_id')) == $product->id) ? 'selected' : '' }}>
                        {{ $product->name }} (Stok: {{ $product->stock_total }})
                    </option>
                    @endforeach
                </select>

                {{-- Selected product info --}}
                <div x-show="selectedProductName" class="mt-2 rounded-xl bg-pink-50 border border-pink-100 px-4 py-3 text-sm">
                    <span class="font-medium text-rose-600" x-text="selectedProductName"></span>
                    <span class="text-gray-500"> · Stok saat ini: </span>
                    <span class="font-bold text-gray-900" x-text="selectedStock"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Jumlah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" x-model="quantity" min="1" required
                           value="{{ old('quantity', 1) }}" class="input"
                           @input="calcTotal()">
                </div>

                {{-- Harga Modal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Modal/Unit <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-gray-400">Rp</span>
                        <input type="number" name="cost_price" x-model="costPrice" min="0" required
                               value="{{ old('cost_price') }}" class="input pl-8"
                               @input="calcTotal()">
                    </div>
                </div>
            </div>

            {{-- Total Cost Preview --}}
            <div x-show="totalCost > 0"
                 class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex justify-between items-center">
                <span class="text-sm text-emerald-700 font-medium">Total Nilai Pembelian</span>
                <span class="text-lg font-bold text-emerald-700"
                      x-text="'Rp ' + totalCost.toLocaleString('id-ID')"></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Supplier</label>
                    <select name="supplier_id" class="input">
                        <option value="">-- Tanpa Supplier --</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Terima <span class="text-rose-500">*</span></label>
                    <input type="date" name="received_date" value="{{ old('received_date', today()->format('Y-m-d')) }}" required class="input">
                </div>
            </div>

            {{-- No Faktur --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Faktur / Invoice</label>
                <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                       placeholder="INV-001 (opsional)" class="input">
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="input resize-none"
                          placeholder="Catatan tambahan…">{{ old('notes') }}</textarea>
            </div>

            {{-- FIFO info --}}
            <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-xs text-blue-700">
                <strong>ℹ️ FIFO Batch:</strong> Setiap barang masuk akan membuat batch baru secara otomatis.
                Sistem akan menggunakan stok paling lama saat barang keluar (FIFO method).
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('incoming.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Barang Masuk</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function incomingForm() {
    return {
        barcodeInput: '',
        scanMessage: '',
        selectedProductId: '{{ old('product_id', request('product_id')) }}',
        selectedProductName: '',
        selectedStock: 0,
        quantity: {{ old('quantity', 1) }},
        costPrice: {{ old('cost_price', 0) }},
        totalCost: 0,

        init() {
            // Auto-calc if pre-filled
            this.$nextTick(() => this.onProductChange());
        },

        async lookupBarcode() {
            if (!this.barcodeInput) return;
            this.scanMessage = 'Mencari produk…';
            try {
                const res = await fetch(`/api/products/by-barcode?barcode=${encodeURIComponent(this.barcodeInput)}`);
                if (!res.ok) { this.scanMessage = '❌ Produk tidak ditemukan.'; return; }
                const data = await res.json();

                // Set the select value
                this.selectedProductId = String(data.id);
                document.querySelector('select[name=product_id]').value = data.id;
                this.selectedProductName = data.name;
                this.selectedStock = data.stock_total;
                this.costPrice = data.sell_price * 0.6; // suggest 60% of sell price as default cost
                this.calcTotal();
                this.scanMessage = `✅ Produk ditemukan: ${data.name}`;
            } catch (e) {
                this.scanMessage = '❌ Gagal mencari produk.';
            }
        },

        onProductChange() {
            const select = document.querySelector('select[name=product_id]');
            const opt = select?.options[select.selectedIndex];
            if (opt && opt.value) {
                this.selectedProductName = opt.text.split(' (Stok')[0];
                const match = opt.text.match(/Stok: (\d+)/);
                this.selectedStock = match ? match[1] : '-';
                this.costPrice = parseFloat(opt.dataset.price) || this.costPrice;
                this.calcTotal();
            } else {
                this.selectedProductName = '';
            }
        },

        calcTotal() {
            this.totalCost = (parseFloat(this.quantity) || 0) * (parseFloat(this.costPrice) || 0);
        }
    }
}
</script>
@endpush
