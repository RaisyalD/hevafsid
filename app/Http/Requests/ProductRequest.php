<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'category_id'        => 'required|exists:categories,id',
            'name'               => 'required|string|max:200',
            'description'        => 'nullable|string|max:1000',
            'unit'               => 'required|string|max:20',
            'sell_price'         => 'required|numeric|min:0',
            'default_cost_price' => 'required|numeric|min:0',
            'min_stock'          => 'required|integer|min:0',
            'image'              => 'nullable|image|max:2048',
            'is_active'          => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'sell_price.required'  => 'Harga jual wajib diisi.',
            'image.image'          => 'File harus berupa gambar.',
            'image.max'            => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
