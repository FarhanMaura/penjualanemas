<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id'   => ['required','exists:categories,id'],
            'sku'           => ['required','string','max:50', Rule::unique('products','sku')->ignore($productId)],
            'name'          => ['required','string','max:200'],
            'description'   => ['nullable','string'],
            'gold_purity'   => ['required','in:24K,22K,18K,14K,9K'],
            'weight_gram'   => ['required','numeric','min:0.001','max:9999'],
            'base_price'    => ['required','numeric','min:1'],
            'buy_back_price'=> ['nullable','numeric','min:0'],
            'stock'         => ['required','integer','min:0'],
            'is_available'  => ['boolean'],
            'is_reservable' => ['boolean'],
            'image'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
