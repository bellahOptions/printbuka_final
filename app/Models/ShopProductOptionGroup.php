<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopProductOptionGroup extends Model
{
    protected $fillable = ['shop_product_id', 'name', 'is_required', 'sort_order'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ShopProductOption::class)->orderBy('sort_order')->orderBy('id');
    }
}
