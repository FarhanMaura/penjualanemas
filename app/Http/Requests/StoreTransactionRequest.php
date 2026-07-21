<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'user_id'          => ['required','exists:users,id'],
            'type'             => ['required','in:purchase,buyback,installment,pawn'],
            'gold_price_id'    => ['nullable','exists:gold_prices,id'],
            'reservation_id'   => ['nullable','exists:reservations,id'],
            'admin_fee'        => ['nullable','numeric','min:0'],
            'discount'         => ['nullable','numeric','min:0'],
            'payment_method'   => ['required','in:cash,transfer,debit,credit'],
            'payment_date'     => ['required','date'],
            'notes'            => ['nullable','string','max:1000'],
            
            // Items (opsional untuk Gadai, wajib untuk tipe lain)
            'items'               => ['required_unless:type,pawn', 'nullable', 'array'],
            'items.*.product_id'  => ['required_with:items', 'exists:products,id'],
            'items.*.quantity'    => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required_with:items', 'numeric', 'min:0'],

            // Cicilan fields
            'installment_tenure'       => ['required_if:type,installment', 'nullable', 'integer', 'min:1'],
            'installment_down_payment' => ['required_if:type,installment', 'nullable', 'numeric', 'min:0'],

            // Gadai fields
            'pawn_gold_description' => ['required_if:type,pawn', 'nullable', 'string', 'max:1000'],
            'pawn_gold_purity'      => ['required_if:type,pawn', 'nullable', 'in:24K'],
            'pawn_weight_gram'      => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0.01'],
            'pawn_appraised_value'  => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0'],
            'pawn_loan_amount'      => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0'],
            'pawn_interest_rate'    => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0'],
            'pawn_due_date'         => ['required_if:type,pawn', 'nullable', 'date'],
        ];
    }
}
