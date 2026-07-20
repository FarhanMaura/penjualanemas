<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pawn extends Model
{
    protected $fillable = [
        'transaction_id',
        'pawn_code',
        'gold_description',
        'gold_purity',
        'weight_gram',
        'appraised_value',
        'loan_amount',
        'interest_rate',
        'start_date',
        'due_date',
        'status',
        'redemption_date',
        'redemption_amount',
        'transaction_item_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'weight_gram'       => 'decimal:3',
            'appraised_value'   => 'decimal:2',
            'loan_amount'       => 'decimal:2',
            'interest_rate'     => 'decimal:2',
            'redemption_amount' => 'decimal:2',
            'start_date'        => 'date',
            'due_date'          => 'date',
            'redemption_date'   => 'date',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->isActive() && $this->due_date->isPast();
    }

    /**
     * Total yang harus dibayar jika ditebus sekarang.
     * (Pokok + bunga berjalan)
     */
    public function calculateRedemptionAmount(): float
    {
        $monthsElapsed = $this->start_date->diffInMonths(now());
        $interest = $this->loan_amount * ($this->interest_rate / 100) * $monthsElapsed;

        return round($this->loan_amount + $interest, 2);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Item pembelian asli yang menjadi asal barang gadai/buyback.
     * Nullable — diisi jika emas berasal dari riwayat pembelian di toko.
     */
    public function sourceTransactionItem(): BelongsTo
    {
        return $this->belongsTo(TransactionItem::class, 'transaction_item_id');
    }
}
