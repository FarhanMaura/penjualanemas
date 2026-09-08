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
        'pickup_reservation_id',
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

    public function settledOrSubmittedCount(): int
    {
        return $this->payments()->whereIn('status', ['paid', 'waiting_verification'])->count();
    }

    public function remainingMonths(): int
    {
        return max(0, $this->tenure_months - $this->paidCount());
    }

    /**
     * Jadwal reservasi pengambilan emas baru bisa dibuka ketika memasuki pembayaran bulan terakhir
     * (misal tenor 3 bulan: pembayaran bulan 1 dan 2 harus kelar/lunas/diajukan dulu, menyisakan bulan terakhir).
     */
    public function canSchedulePickup(): bool
    {
        $requiredPaid = $this->requiredPaidForPickup();
        return $this->settledOrSubmittedCount() >= $requiredPaid;
    }

    public function requiredPaidForPickup(): int
    {
        return max(1, $this->tenure_months - 1);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function pickupReservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'pickup_reservation_id');
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

    public function installmentTransactions(): HasMany
    {
        return $this->hasMany(InstallmentTransaction::class)->latest();
    }

    public function waitingVerificationCount(): int
    {
        return $this->payments()->where('status', 'waiting_verification')->count();
    }
}
