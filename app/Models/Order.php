<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'token', 'status', 'subtotal', 'shipping_cost',
        'discount_amount', 'total', 'currency', 'payment_method',
        'payment_status', 'payment_ref', 'customer_name', 'customer_email',
        'customer_phone', 'shipping_address', 'notes', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'shipped_at'       => 'datetime',
        'delivered_at'     => 'datetime',
        'subtotal'         => 'float',
        'shipping_cost'    => 'float',
        'discount_amount'  => 'float',
        'total'            => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->token)) {
                $order->token = Str::random(40);
            }
        });
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Business Logic ─────────────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        $year = date('Y');

        $lastOrder = static::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        if ($lastOrder) {
            $parts   = explode('-', $lastOrder->order_number);
            $counter = (int) end($parts);
        } else {
            $counter = 0;
        }

        return 'NS-' . $year . '-' . str_pad($counter + 1, 6, '0', STR_PAD_LEFT);
    }
}
