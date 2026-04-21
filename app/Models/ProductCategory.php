<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ProductCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'tag',
        'description',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeVisibleInCustomerCatalog(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function ($visibilityQuery): void {
                $visibilityQuery
                    ->whereHas('products', fn ($productsQuery) => $productsQuery->where('is_active', true))
                    ->orWhereHas('children', fn ($childrenQuery) => $childrenQuery
                        ->where('is_active', true)
                        ->whereHas('products', fn ($productsQuery) => $productsQuery->where('is_active', true)));
            });
    }

    public function scopeWithActiveProductsCount(Builder $query): Builder
    {
        return $query->withCount([
            'products as active_products_count' => fn ($productsQuery) => $productsQuery->where('is_active', true),
        ]);
    }

    public static function publicTreeQuery(): Builder
    {
        return self::query()
            ->topLevel()
            ->visibleInCustomerCatalog()
            ->withActiveProductsCount()
            ->with([
                'children' => fn ($childrenQuery) => $childrenQuery
                    ->where('is_active', true)
                    ->whereHas('products', fn ($productsQuery) => $productsQuery->where('is_active', true))
                    ->withActiveProductsCount()
                    ->orderBy('name'),
            ])
            ->orderBy('name');
    }

    public static function menuCategories(int $limit = 6): Collection
    {
        return self::publicTreeQuery()
            ->limit($limit)
            ->get();
    }

    public static function homeCategories(int $limit = 5): Collection
    {
        return self::publicTreeQuery()
            ->limit($limit)
            ->get();
    }
}
