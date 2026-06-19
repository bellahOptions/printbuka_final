<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductStockLog extends Model
{
    protected $fillable = [
        'shop_product_id',
        'shop_product_option_id',
        'change',
        'balance_after',
        'reason',
        'reference',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'change'       => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ShopProductOption::class, 'shop_product_option_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAddition(): bool
    {
        return $this->change > 0;
    }

    public function isDeduction(): bool
    {
        return $this->change < 0;
    }
}
