<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_category_id',
        'service_type',
        'moq',
        'price',
        'short_description',
        'description',
        'paper_type',
        'material_price_options',
        'paper_size',
        'is_featured',
        'view_count',
        'size_price_options',
        'finishing',
        'finish_price_options',
        'delivery_price_options',
        'paper_density',
        'featured_image',
        'additional_images',
        'density_price_options',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'material_price_options' => 'array',
            'size_price_options' => 'array',
            'finish_price_options' => 'array',
            'density_price_options' => 'array',
            'delivery_price_options' => 'array',
            'additional_images' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function featuredImageUrl(): ?string
    {
        return MediaUrl::resolve($this->featured_image);
    }

    /**
     * @return array<int, string>
     */
    public function additionalImageUrls(): array
    {
        return collect((array) $this->additional_images)
            ->map(fn ($path): ?string => is_string($path) ? MediaUrl::resolve($path) : null)
            ->filter(fn ($path): bool => filled($path))
            ->values()
            ->all();
    }
}
