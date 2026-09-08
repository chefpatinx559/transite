<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'whatsapp', 'city',
        'product_description', 'quantity', 'budget', 'source_country',
        'experience_level', 'services', 'status', 'admin_notes',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
