<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    protected function prepareForValidation(): void
    {
        if (in_array($this->type, ['pawn', 'buyback'])) {
            $this->request->remove('items');
        } elseif (is_array($this->items)) {
            $filtered = array_filter($this->items, fn($item) => !empty($item['product_id']));
            if (empty($filtered)) {
                $this->request->remove('items');
            } else {
                $this->merge(['items' => array_values($filtered)]);
            }
        }

        if ($this->filled('admin_fee')) {
            $this->merge([
                'admin_fee' => (float) str_replace(['.', ','], '', (string) $this->admin_fee),
            ]);
        }
        if ($this->filled('pawn_loan_amount')) {
            $this->merge([
                'pawn_loan_amount' => (float) str_replace(['.', ','], '', (string) $this->pawn_loan_amount),
            ]);
        }
        if ($this->filled('pawn_appraised_value')) {
            $this->merge([
                'pawn_appraised_value' => (float) str_replace(['.', ','], '', (string) $this->pawn_appraised_value),
            ]);
        }
        if ($this->type === 'pawn' && !$this->filled('pawn_tenure')) {
            $this->merge([
                'pawn_tenure' => 4,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'user_id'          => ['required','exists:users,id'],
            'type'             => ['required','in:purchase,buyback,installment,pawn'],
            'gold_price_id'    => ['nullable','exists:gold_prices,id'],
            'reservation_id'   => ['nullable','exists:reservations,id'],
            'admin_fee'        => ['nullable','numeric','min:0'],
            'discount'         => ['nullable','numeric','min:0'],
            'payment_method'   => ['required','string','max:50'],
            'payment_date'     => ['required','date'],
            'notes'            => ['nullable','string','max:1000'],
            
            // Items (opsional untuk Gadai dan Buyback, wajib untuk tipe lain)
            'items'               => ['required_unless:type,pawn,buyback', 'nullable', 'array', 'min:1'],
            'items.*.product_id'  => ['required_unless:type,pawn,buyback', 'nullable', 'exists:products,id'],
            'items.*.quantity'    => ['required_unless:type,pawn,buyback', 'nullable', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required_unless:type,pawn,buyback', 'nullable', 'numeric', 'min:0'],

            // Cicilan fields
            'installment_tenure'       => ['required_if:type,installment', 'nullable', 'integer', 'in:3,6,12'],
            'installment_down_payment' => ['nullable', 'numeric', 'min:0'],

            // Gadai fields
            'pawn_gold_description' => ['required_if:type,pawn', 'nullable', 'string', 'max:1000'],
            'pawn_gold_purity'      => ['required_if:type,pawn', 'nullable', 'in:24K'],
            'pawn_weight_gram'      => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0.01'],
            'pawn_appraised_value'  => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0'],
            'pawn_loan_amount'      => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0'],
            'pawn_tenure'           => ['required_if:type,pawn', 'nullable', 'integer', 'min:1', 'max:36'],
            'pawn_interest_rate'    => ['nullable', 'numeric', 'min:0'],
            'pawn_due_date'         => ['required_if:type,pawn', 'nullable', 'date'],
        ];
    }
}
