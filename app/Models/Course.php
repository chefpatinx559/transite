<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'content', 'cover_image',
        'preview_video_url', 'price', 'original_price', 'total_videos',
        'total_duration', 'level', 'language', 'is_published', 'is_featured',
        'students_count', 'rating_avg',
    ];

    protected $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'rating_avg' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order_index');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->orderBy('order_index');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
