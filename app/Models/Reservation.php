<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_code',
        'user_id',
        'product_id',
        'price_negotiation_id',
        'quantity',
        'agreed_price',
        'preferred_date',
        'preferred_time',
        'payment_method',
        'status',
        'notes',
        'admin_notes',
        'confirmed_by',
        'confirmed_at',
        'expired_at',
        'transaction_id',
        'type',
        'pawn_gold_description',
        'pawn_gold_purity',
        'pawn_weight_gram',
        'pawn_amount_requested',
        'installment_tenure',
        'installment_down_payment',
    ];

    protected function casts(): array
    {
        return [
            'agreed_price'   => 'decimal:2',
            'preferred_date' => 'date',
            'confirmed_at'   => 'datetime',
            'expired_at'     => 'datetime',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceNegotiation(): BelongsTo
    {
        return $this->belongsTo(PriceNegotiation::class);
    }

    /** Admin yang mengkonfirmasi reservasi */
    public function confirmedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /** Transaksi yang lahir dari reservasi ini */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
