<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomingTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'product_id'     => 'required|exists:products,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'quantity'       => 'required|integer|min:1',
            'cost_price'     => 'required|numeric|min:0',
            'received_date'  => 'required|date|before_or_equal:today',
            'invoice_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'    => 'Produk wajib dipilih.',
            'quantity.required'      => 'Jumlah wajib diisi.',
            'quantity.min'           => 'Jumlah minimal 1.',
            'cost_price.required'    => 'Harga modal wajib diisi.',
            'received_date.required' => 'Tanggal terima wajib diisi.',
        ];
    }
}
