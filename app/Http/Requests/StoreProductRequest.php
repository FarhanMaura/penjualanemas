<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'category_id'  => ['required','exists:categories,id'],
            'sku'          => ['required','string','max:50','unique:products,sku'],
            'name'         => ['required','string','max:200'],
            'description'  => ['nullable','string'],
            'gold_purity'  => ['required','in:24K'],
            'weight_gram'  => ['required','numeric','min:0.001','max:9999'],
            'base_price'   => ['required','numeric','min:1'],
            'buy_back_price'=> ['nullable','numeric','min:0'],
            'stock'        => ['required','integer','min:0'],
            'is_available' => ['boolean'],
            'is_reservable'=> ['boolean'],
            'image'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'sku.unique'           => 'SKU sudah digunakan, gunakan kode lain.',
            'weight_gram.min'      => 'Berat minimal 0.001 gram.',
            'base_price.min'       => 'Harga jual tidak boleh nol.',
            'image.max'            => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
