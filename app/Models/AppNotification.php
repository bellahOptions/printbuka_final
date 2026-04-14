<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppNotification extends Model
{
    protected $fillable = [
        'user_id',
        'audience',
        'role',
        'title',
        'message',
        'type',
        'display_format',
        'read_at',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AppNotificationRead::class);
    }
}
