<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    protected function prepareForValidation(): void
    {
        if ($this->preferred_time) {
            $this->merge([
                'preferred_time' => substr($this->preferred_time, 0, 5),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'type'                     => ['required', 'in:purchase,buyback,installment,pawn'],
            'product_id'               => ['required_if:type,purchase,installment', 'nullable', 'exists:products,id'],
            'price_negotiation_id'     => ['nullable', 'exists:price_negotiations,id'],
            'agreed_price'             => ['nullable', 'numeric', 'min:0'],
            'quantity'                 => ['required_if:type,purchase,installment', 'nullable', 'integer', 'min:1', 'max:100'],
            'preferred_date'           => ['required_unless:type,installment', 'nullable', 'date', 'after_or_equal:today'],
            'preferred_time'           => ['nullable', 'date_format:H:i'],
            'payment_method'           => ['required_if:type,purchase,installment', 'nullable', 'string', 'max:50'],
            'notes'                    => ['nullable', 'string', 'max:500'],
            'pawn_gold_description'    => ['required_if:type,pawn,buyback', 'nullable', 'string', 'max:500'],
            'pawn_gold_purity'         => ['required_if:type,pawn', 'nullable', 'string', 'max:20'],
            'pawn_weight_gram'         => ['required_if:type,pawn', 'nullable', 'numeric', 'min:0.01'],
            'pawn_amount_requested'    => ['nullable', 'numeric', 'min:0'],
            'installment_tenure'       => ['required_if:type,installment', 'nullable', 'integer', 'in:3,6,12'],
            'installment_down_payment' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'                     => 'Tipe pengajuan / reservasi wajib dipilih.',
            'product_id.required_if'            => 'Silakan pilih produk emas pada menu dropdown "Pilih Produk Emas".',
            'product_id.exists'                 => 'Produk emas yang dipilih tidak ditemukan dalam sistem.',
            'quantity.required_if'              => 'Jumlah pembelian (Qty) wajib diisi minimal 1.',
            'preferred_date.required'           => 'Rencana tanggal kunjungan wajib diisi.',
            'preferred_date.after_or_equal'     => 'Tanggal kunjungan tidak boleh di masa lalu.',
            'preferred_time.date_format'        => 'Format waktu harus HH:MM (contoh: 09:30).',
            'payment_method.required_if'        => 'Metode pembayaran wajib dipilih.',
            'pawn_gold_description.required_if' => 'Jenis / deskripsi perhiasan emas wajib dipilih.',
            'pawn_gold_purity.required_if'      => 'Kadar emas wajib dipilih.',
            'pawn_weight_gram.required_if'      => 'Berat emas dalam gram wajib diisi.',
            'installment_tenure.required_if'    => 'Tenor cicilan wajib dipilih.',
            'installment_down_payment.required_if' => 'Uang muka / DP cicilan wajib diisi.',
        ];
    }
}
