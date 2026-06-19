<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrderItemOption extends Model
{
    protected $fillable = ['shop_order_item_id', 'shop_product_option_id', 'group_name', 'option_name', 'price_modifier'];

    protected function casts(): array
    {
        return ['price_modifier' => 'decimal:2'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ShopOrderItem::class, 'shop_order_item_id');
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ShopProductOption::class, 'shop_product_option_id');
    }
}
