<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    public $timestamps = false;

    protected $fillable = ['email', 'name', 'is_confirmed', 'token', 'source', 'subscribed_at', 'unsubscribed_at'];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }

            if (empty($subscriber->token)) {
                $subscriber->token = (string) Str::uuid();
            }
        });
    }
}
