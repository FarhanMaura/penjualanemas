<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceNegotiation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'negotiation_code',
        'user_id',
        'product_id',
        'original_price',
        'offered_price',
        'agreed_price',
        'quantity',
        'status',
        'notes',
        'admin_notes',
        'responded_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'original_price' => 'decimal:2',
            'offered_price'  => 'decimal:2',
            'agreed_price'   => 'decimal:2',
            'responded_at'   => 'datetime',
        ];
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

    public function respondedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isUsed(): bool
    {
        return $this->status === 'used';
    }
}
