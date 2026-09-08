<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formation extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'description', 'cover_image',
        'type', 'is_free', 'price',
        'duration', 'instructor_name',
        'date_start', 'date_end',
        'location', 'platform_link',
        'max_participants', 'participants_count',
        'is_published',
    ];

    protected $casts = [
        'is_free'       => 'boolean',
        'is_published'  => 'boolean',
        'price'         => 'float',
        'date_start'    => 'datetime',
        'date_end'      => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(FormationRegistration::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function isFull(): bool
    {
        return $this->max_participants !== null
            && $this->participants_count >= $this->max_participants;
    }
}
