<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopOrder extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'customer_name', 'customer_email', 'customer_phone',
        'shipping_name', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_notes',
        'subtotal', 'total', 'payment_status', 'paystack_reference', 'paystack_data', 'fulfillment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'paystack_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
