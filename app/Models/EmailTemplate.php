<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'mail_class',
        'subject',
        'intro_blocks',
        'outro_blocks',
        'is_active',
        'updated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'intro_blocks' => 'array',
            'outro_blocks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
