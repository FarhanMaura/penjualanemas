<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentPlan extends Model
{
    protected $fillable = [
        'transaction_id',
        'down_payment',
        'total_installment',
        'tenure_months',
        'monthly_amount',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'down_payment'      => 'decimal:2',
            'total_installment' => 'decimal:2',
            'monthly_amount'    => 'decimal:2',
            'start_date'        => 'date',
            'end_date'          => 'date',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function paidCount(): int
    {
        return $this->payments()->where('status', 'paid')->count();
    }

    public function remainingMonths(): int
    {
        return $this->tenure_months - $this->paidCount();
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    public function pendingPayments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class)->where('status', 'pending');
    }

    public function overduePayments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class)->where('status', 'overdue');
    }
}
