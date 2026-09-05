<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentTransaction extends Model
{
    protected $fillable = [
        'installment_plan_id',
        'user_id',
        'transaction_code',
        'amount',
        'months_paid',
        'month_count',
        'payment_method',
        'proof_image',
        'sender_bank',
        'sender_name',
        'notes',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'months_paid'  => 'array',
            'month_count'  => 'integer',
            'verified_at'  => 'datetime',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isWaitingVerification(): bool
    {
        return $this->status === 'waiting_verification';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function formattedMonths(): string
    {
        $months = (array) ($this->months_paid ?? []);
        if (empty($months)) {
            return '-';
        }

        sort($months);
        $count = count($months);

        if ($count === 1) {
            return "Bulan ke-{$months[0]}";
        }

        if ($count === 2) {
            return "Bulan ke-{$months[0]} & ke-{$months[1]} (2 Bulan)";
        }

        $first = $months[0];
        $last = $months[$count - 1];

        // Check if consecutive
        $isConsecutive = true;
        for ($i = 0; $i < $count - 1; $i++) {
            if ($months[$i + 1] !== $months[$i] + 1) {
                $isConsecutive = false;
                break;
            }
        }

        if ($isConsecutive) {
            return "Bulan ke-{$first} s/d ke-{$last} ({$count} Bulan)";
        }

        return "Bulan ke-" . implode(', ', $months) . " ({$count} Bulan)";
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function installmentPlan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class);
    }
}
