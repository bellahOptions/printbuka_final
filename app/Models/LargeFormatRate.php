<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LargeFormatRate extends Model
{
    protected $fillable = [
        'material',
        'rate_per_sqft',
        'is_active',
        'updated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'rate_per_sqft' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Unified large-format pricing rule: convert the given dimensions to
     * square feet (inches are divided by 144 — 12in x 12in = 1 sqft), then
     * price = area_in_sqft x rate x quantity. This single formula reproduces
     * both the flex-banner (ft, qty 1) and per-piece sticker (in, qty N)
     * cases the business uses, since "divide by 144" is just a unit
     * conversion, not a different pricing model.
     */
    public function priceFor(float $width, float $height, string $unit, int $quantity = 1): float
    {
        $areaSqft = ($width * $height) / ($unit === 'in' ? 144 : 1);

        return round($areaSqft * (float) $this->rate_per_sqft * max(1, $quantity), 2);
    }
}
