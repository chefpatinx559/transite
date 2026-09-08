<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt',
        'content', 'cover_image', 'reading_time', 'status', 'published_at', 'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ── Relations ──────────────────────────────────
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ─────────────────────────────────────
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategory(Builder $query, ?string $slug): Builder
    {
        if (! $slug) {
            return $query;
        }

        return $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
    }

    // ── Accessors ──────────────────────────────────
    public function getReadingTimeTextAttribute(): string
    {
        return $this->reading_time.' min de lecture';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->published_at
            ? $this->published_at->translatedFormat('d F Y')
            : $this->created_at->translatedFormat('d F Y');
    }
}
