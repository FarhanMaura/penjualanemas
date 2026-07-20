<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldPrice extends Model
{
    protected $fillable = [
        'price_date',
        'buy_price_per_gram',
        'sell_price_per_gram',
        'source',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price_date'          => 'date',
            'buy_price_per_gram'  => 'decimal:2',
            'sell_price_per_gram' => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
