<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalCertificate extends Model
{
    protected $fillable = [
        'certificate_number',
        'transaction_id',
        'user_id',
        'issued_at',
        'pdf_path',
        'qr_code',
        'is_valid',
        'invalidated_at',
        'invalidation_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'       => 'datetime',
            'invalidated_at'  => 'datetime',
            'is_valid'        => 'boolean',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function invalidate(string $reason): void
    {
        $this->update([
            'is_valid'             => false,
            'invalidated_at'       => now(),
            'invalidation_reason'  => $reason,
        ]);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
