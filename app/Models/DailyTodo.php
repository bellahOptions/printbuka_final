<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTodo extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'assigned_by_id',
        'task',
        'priority',
        'notes',
        'due_date',
        'status',
        'completed_at',
        'reviewed_by_id',
        'reviewed_at',
        'review_comments',
        'review_rating',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'review_rating' => 'integer',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
