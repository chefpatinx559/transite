<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'course_id', 'item_type',
        'name', 'price', 'quantity', 'subtotal', 'image',
    ];

    protected $casts = [
        'price' => 'float',
        'subtotal' => 'float',
    ];

    // ── Relations ──────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
