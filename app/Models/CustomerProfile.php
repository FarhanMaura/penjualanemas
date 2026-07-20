<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'province',
        'postal_code',
        'photo',
        'customer_since',
        'segment',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'  => 'date',
            'customer_since' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
