<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'status',
        'escalation_requested',
        'ticket_id',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'escalation_requested' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function contactName(): string
    {
        return $this->user?->displayName() ?? $this->guest_name ?? 'Guest';
    }

    public function contactEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }
}
