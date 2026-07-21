<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'gold_purity',
        'weight_gram',
        'base_price',
        'buy_back_price',
        'stock',
        'images',
        'is_available',
        'is_reservable',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images'       => 'array',
            'weight_gram'  => 'decimal:3',
            'base_price'   => 'decimal:2',
            'buy_back_price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_reservable' => 'boolean',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Ambil path gambar pertama sebagai thumbnail utama.
     */
    public function getThumbnailAttribute(): ?string
    {
        $images = $this->images;

        return (is_array($images) && count($images) > 0) ? $images[0] : null;
    }

    /**
     * Ambil URL gambar thumbnail secara dinamis.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnail = $this->thumbnail;
        if (! $thumbnail) {
            return null;
        }

        // Jika path berupa URL luar atau file statis dari public/images
        if (str_starts_with($thumbnail, 'http') || str_starts_with($thumbnail, '/images')) {
            return $thumbnail;
        }

        // Jika gambar di-upload dan disimpan di storage/app/public/products
        return \Illuminate\Support\Facades\Storage::url($thumbnail);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
