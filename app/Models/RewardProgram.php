<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardProgram extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'earn_rule',
        'redeem_rule',
        'points_per_transaction',
        'min_transaction_amount',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'earn_rule'               => 'array',
            'redeem_rule'             => 'array',
            'min_transaction_amount'  => 'decimal:2',
            'is_active'               => 'boolean',
            'start_date'              => 'date',
            'end_date'                => 'date',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now()->toDateString();

        if ($this->start_date && $this->start_date->toDateString() > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date->toDateString() < $now) {
            return false;
        }

        return true;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
}
