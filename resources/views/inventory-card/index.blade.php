@extends('layouts.app')
@section('title', 'Kartu Persediaan')
@section('page-title', 'Kartu Persediaan')
@section('breadcrumb', 'Stok → Kartu Persediaan')

@section('content')
<div class="max-w-lg mx-auto mt-10 bg-white rounded-2xl border border-pink-100 shadow-sm p-8 text-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-pink-100 mx-auto mb-4">
        <svg class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
    </div>
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Pilih Produk</h2>
    <p class="text-sm text-gray-500 mb-6">Pilih produk untuk melihat kartu persediaan / ledger stok</p>
    <form method="GET" action="{{ route('inventory-card.index') }}">
        <select name="product_id" required class="input mb-4">
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary w-full">Lihat Kartu Stok</button>
    </form>
</div>
@endsection
