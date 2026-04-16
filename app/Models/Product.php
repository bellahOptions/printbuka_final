<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_category_id',
        'moq',
        'price',
        'short_description',
        'description',
        'paper_type',
        'material_price_options',
        'paper_size',
        'size_price_options',
        'finishing',
        'finish_price_options',
        'delivery_price_options',
        'paper_density',
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
            'is_active' => 'boolean',
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
}
