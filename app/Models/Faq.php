<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'question', 'answer', 'category', 'order_index', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
