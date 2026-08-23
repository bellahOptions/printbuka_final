<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'created_by_id',
        'subject',
        'preheader',
        'blocks',
        'recipient_count',
        'emails_sent',
        'emails_failed',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'recipient_count' => 'integer',
            'emails_sent' => 'integer',
            'emails_failed' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
