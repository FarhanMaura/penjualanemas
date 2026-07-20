<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'user_id',
        'type',
        'status',
        'gold_price_id',
        'subtotal',
        'admin_fee',
        'discount',
        'total_amount',
        'payment_method',
        'payment_date',
        'payment_proof',
        'reservation_id',
        'processed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'      => 'decimal:2',
            'admin_fee'     => 'decimal:2',
            'discount'      => 'decimal:2',
            'total_amount'  => 'decimal:2',
            'payment_date'  => 'date',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPurchase(): bool   { return $this->type === 'purchase'; }
    public function isBuyback(): bool    { return $this->type === 'buyback'; }
    public function isInstallment(): bool { return $this->type === 'installment'; }
    public function isPawn(): bool       { return $this->type === 'pawn'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goldPrice(): BelongsTo
    {
        return $this->belongsTo(GoldPrice::class);
    }

    /** Admin yang memproses transaksi */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /** Reservasi asal (jika transaksi berasal dari reservasi online) */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function installmentPlan(): HasOne
    {
        return $this->hasOne(InstallmentPlan::class);
    }

    public function pawn(): HasOne
    {
        return $this->hasOne(Pawn::class);
    }

    public function digitalCertificate(): HasOne
    {
        return $this->hasOne(DigitalCertificate::class);
    }

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
}
