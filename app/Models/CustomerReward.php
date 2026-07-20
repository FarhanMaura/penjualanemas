<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerReward extends Model
{
    protected $fillable = [
        'user_id',
        'current_points',
        'total_earned_points',
        'total_redeemed_points',
        'tier',
        'tier_updated_at',
        'lifetime_spending',
    ];

    protected function casts(): array
    {
        return [
            'tier_updated_at'  => 'datetime',
            'lifetime_spending' => 'decimal:2',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function canRedeem(int $points): bool
    {
        return $this->current_points >= $points;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class, 'user_id', 'user_id');
    }
}
