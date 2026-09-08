<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'content', 'price', 'compare_price',
        'sku', 'stock', 'type', 'weight', 'images', 'attributes',
        'is_published', 'is_featured', 'sales_count', 'category_id',
    ];

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'float',
        'compare_price' => 'float',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    /**
     * Retourne le prix formaté : "3 500 FCFA"
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', ' ').' FCFA';
    }
}
