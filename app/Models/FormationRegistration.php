<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FormationRegistration extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'formation_id', 'name', 'email', 'phone', 'notes',
        'status', 'payment_status', 'payment_ref', 'token',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reg) {
            if (empty($reg->token)) {
                $reg->token = Str::random(40);
            }
        });

        static::created(function (self $reg) {
            $reg->formation()->increment('participants_count');
        });

        // Décrémenter quand le statut passe à 'cancelled'
        static::updating(function (self $reg) {
            if ($reg->isDirty('status') && $reg->status === 'cancelled' && $reg->getOriginal('status') !== 'cancelled') {
                $reg->formation()->decrement('participants_count');
            }
            // Ré-incrémenter si on remet actif depuis cancelled
            if ($reg->isDirty('status') && $reg->getOriginal('status') === 'cancelled' && $reg->status !== 'cancelled') {
                $reg->formation()->increment('participants_count');
            }
        });

        static::deleted(function (self $reg) {
            if ($reg->status !== 'cancelled') {
                $reg->formation()->decrement('participants_count');
            }
        });
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}
