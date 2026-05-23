<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutgoingTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'required|integer|min:1',
            'sell_price'       => 'required|numeric|min:0',
            'transaction_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'       => 'Produk wajib dipilih.',
            'quantity.required'         => 'Jumlah wajib diisi.',
            'sell_price.required'       => 'Harga jual wajib diisi.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }
}
