<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShopProduct extends Model
{
    protected $fillable = [
        'name', 'slug', 'short_description', 'description',
        'price', 'sale_price', 'sku', 'sku_sequence', 'stock_quantity', 'manage_stock',
        'is_active', 'is_featured', 'featured_image', 'additional_images', 'created_by',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'manage_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'additional_images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ShopProduct $product): void {
            if (empty($product->slug)) {
                $base = Str::slug($product->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $product->slug = $slug;
            }
        });

        // Auto-generate SKU after record is persisted so we have the ID.
        // Format: PBK-YYYY-NNNNN  e.g. PBK-2026-00003
        static::created(function (ShopProduct $product): void {
            if (filled($product->sku)) {
                return;
            }

            $year     = now()->format('Y');
            $sequence = static::max('sku_sequence') ?? 0;
            $sequence++;

            $sku = 'PBK-' . $year . '-' . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);

            $product->updateQuietly(['sku' => $sku, 'sku_sequence' => $sequence]);
        });
    }

    public function optionGroups(): HasMany
    {
        return $this->hasMany(ShopProductOptionGroup::class)->orderBy('sort_order')->orderBy('id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(ShopProductStockLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('view_count');
    }

    public function currentPrice(): float
    {
        if ($this->sale_price !== null && (float) $this->sale_price < (float) $this->price) {
            return (float) $this->sale_price;
        }

        return (float) $this->price;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function isInStock(): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return ($this->stock_quantity ?? 0) > 0;
    }

    public function featuredImageUrl(): ?string
    {
        return MediaUrl::resolve($this->featured_image);
    }

    /** @return string[] */
    public function additionalImageUrls(): array
    {
        return collect((array) ($this->additional_images ?? []))
            ->map(fn ($img) => MediaUrl::resolve((string) $img))
            ->filter()
            ->values()
            ->all();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
