<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'type'                     => ['required', 'in:purchase,installment,pawn'],
            'product_id'               => ['required_if:type,purchase,installment', 'nullable', 'exists:products,id'],
            'quantity'                 => ['required_if:type,purchase,installment', 'nullable', 'integer', 'min:1', 'max:10'],
            'preferred_date'           => ['required','date','after_or_equal:today'],
            'preferred_time'           => ['nullable','date_format:H:i'],
            'payment_method'           => ['required_if:type,purchase,installment', 'nullable', 'in:cash,transfer,debit,credit'],
            'notes'                    => ['nullable','string','max:500'],
            'pawn_gold_description'    => ['required_if:type,pawn', 'nullable', 'string', 'max:500'],
            'pawn_gold_purity'         => ['required_if:type,pawn', 'nullable', 'in:24K'],
            'pawn_weight_gram'         => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0.01'],
            'pawn_amount_requested'    => ['required_if:type,pawn', 'nullable', 'numeric', 'min:1000'],
            'installment_tenure'       => ['required_if:type,installment', 'nullable', 'integer', 'in:3,6,12'],
            'installment_down_payment' => ['required_if:type,installment', 'nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'preferred_date.after_or_equal' => 'Tanggal kunjungan tidak boleh di masa lalu.',
            'preferred_time.date_format'     => 'Format waktu harus HH:MM (contoh: 09:30).',
        ];
    }
}
