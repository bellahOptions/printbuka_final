<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'category',
        'priority',
        'message',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'last_unanswered_reminder_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_unanswered_reminder_at' => 'datetime',
        ];
    }

    // Generate a unique ticket number
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . $year . $month . '-' . $random;
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Status helpers
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    // Priority badge color
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'urgent' => 'error',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'success',
            default => 'info',
        };
    }

    // Status badge color
    public function getStatusColor(): string
    {
        return match($this->status) {
            'open' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'error',
            default => 'info',
        };
    }
}
