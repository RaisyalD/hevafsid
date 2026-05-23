<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'product_id'       => 'required|exists:products,id',
            'product_batch_id' => 'nullable|exists:product_batches,id',
            'quantity'         => 'required|integer|min:1',
            'reject_type'      => 'required|in:damaged,defective,expired,lost,other',
            'reason'           => 'required|string|max:500',
            'reject_date'      => 'required|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'  => 'Produk wajib dipilih.',
            'reject_type.required' => 'Jenis reject wajib dipilih.',
            'reason.required'      => 'Alasan reject wajib diisi.',
        ];
    }
}
