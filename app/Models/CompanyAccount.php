<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyAccount extends Model
{
    protected $fillable = [
        'label',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'note',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function defaultAccount(): ?self
    {
        return static::query()->where('is_default', true)->first()
            ?? static::query()->where('is_active', true)->orderBy('label')->first();
    }
}
