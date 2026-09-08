<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLesson extends Model
{
    protected $fillable = [
        'section_id', 'course_id', 'title', 'description', 'video_url',
        'video_duration', 'order_index', 'is_free_preview', 'resources',
    ];

    protected $casts = [
        'resources' => 'array',
        'is_free_preview' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
