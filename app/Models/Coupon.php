<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_order_amount',
        'max_uses', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'float',
        'min_order_amount' => 'float',
    ];

    // ── Business Logic ─────────────────────────────────────────────────────

    /**
     * Vérifie si le coupon est utilisable pour un montant donné.
     */
    public function isValid(float $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($amount < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calcule la remise applicable pour un montant donné.
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'percent') {
            return round($amount * ($this->discount_value / 100), 2);
        }

        // Type 'fixed'
        return min($this->discount_value, $amount);
    }
}
