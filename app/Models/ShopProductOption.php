<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopProductOption extends Model
{
    protected $fillable = [
        'shop_product_option_group_id',
        'name',
        'price_modifier',
        'is_available',
        'sort_order',
        'stock_quantity',
        'image',
    ];

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

    public function stockLogs(): HasMany
    {
        return $this->hasMany(ShopProductStockLog::class, 'shop_product_option_id');
    }

    /** null stock_quantity means unlimited */
    public function isInStock(int $requested = 1): bool
    {
        if ($this->stock_quantity === null) {
            return true;
        }

        return $this->stock_quantity >= $requested;
    }

    /** Decrement stock and write a log entry. No-op when stock is unmanaged (null). */
    public function decrementStock(int $qty, string $reference, int $shopProductId): void
    {
        if ($this->stock_quantity === null) {
            return;
        }

        $newBalance = max(0, $this->stock_quantity - $qty);
        $this->decrement('stock_quantity', $qty);

        ShopProductStockLog::create([
            'shop_product_id'        => $shopProductId,
            'shop_product_option_id' => $this->id,
            'change'                 => -$qty,
            'balance_after'          => $newBalance,
            'reason'                 => 'sale',
            'reference'              => $reference,
        ]);
    }

    public function imageUrl(): ?string
    {
        return MediaUrl::resolve($this->image);
    }
}
