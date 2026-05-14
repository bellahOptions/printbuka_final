<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone_whatsapp',
        'email',
        'contact_address',
        'city_state',
        'educational_qualification',
        'desired_skill',
        'employment_status',
        'experience_level',
        'has_laptop',
        'availability',
        'portfolio_url',
        'motivation',
        'referral_source',
        'status',
        'decision_note',
        'decided_at',
        'decided_by_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'has_laptop' => 'boolean',
        'decided_at' => 'datetime',
    ];

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_id');
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Pending',
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
