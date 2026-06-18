<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInteraction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'product_type',
        'product_id',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
