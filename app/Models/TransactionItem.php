<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'gold_purity',
        'weight_gram',
        'quantity',
        'price_per_unit',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'weight_gram'    => 'decimal:3',
            'price_per_unit' => 'decimal:2',
            'subtotal'       => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Produk katalog asal item ini.
     * Nullable — bisa null untuk emas bebas atau buyback non-katalog.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Jika item ini dirujuk oleh catatan gadai sebagai asal barang gadai.
     */
    public function pawn(): HasOne
    {
        return $this->hasOne(Pawn::class, 'transaction_item_id');
    }
}
