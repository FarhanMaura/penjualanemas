<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentPayment extends Model
{
    protected $fillable = [
        'installment_plan_id',
        'installment_number',
        'due_date',
        'paid_date',
        'amount_due',
        'amount_paid',
        'payment_method',
        'status',
        'installment_transaction_id',
        'proof_image',
        'received_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date'   => 'date',
            'paid_date'  => 'date',
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isWaitingVerification(): bool
    {
        return $this->status === 'waiting_verification';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue'
            || ($this->status === 'pending' && $this->due_date->isPast());
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function installmentPlan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class);
    }

    public function installmentTransaction(): BelongsTo
    {
        return $this->belongsTo(InstallmentTransaction::class);
    }

    /** Admin yang menerima pembayaran cicilan */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
