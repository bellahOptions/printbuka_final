<?php

namespace App\Models;

use App\Support\PriceList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceListItem extends Model
{
    protected $fillable = [
        'category',
        'product_id',
        'service_slug',
        'component_group',
        'component_key',
        'label',
        'price',
        'sort_order',
        'is_active',
        'notes',
        'updated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    protected static function booted(): void
    {
        static::saved(fn () => PriceList::clearCache());
        static::deleted(fn () => PriceList::clearCache());
    }
}
