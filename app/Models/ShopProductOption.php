<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductOption extends Model
{
    protected $fillable = ['shop_product_option_group_id', 'name', 'price_modifier', 'is_available', 'sort_order'];

    protected function casts(): array
    {
        return [
            'price_modifier' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ShopProductOptionGroup::class, 'shop_product_option_group_id');
    }
}
