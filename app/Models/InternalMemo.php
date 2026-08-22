<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalMemo extends Model
{
    protected $fillable = [
        'subject',
        'blocks',
        'recipient_scope',
        'recipient_count',
        'emails_sent',
        'emails_failed',
        'sent_by_id',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'recipient_scope' => 'array',
            'recipient_count' => 'integer',
            'emails_sent' => 'integer',
            'emails_failed' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_id');
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }
}
