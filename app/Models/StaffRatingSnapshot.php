<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffRatingSnapshot extends Model
{
    protected $fillable = [
        'period_type',
        'period_start',
        'period_end',
        'user_id',
        'attendance_score',
        'overtime_score',
        'supervisor_score',
        'activity_score',
        'total_score',
        'rank',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            // Deliberately NOT cast to 'date' — see AttendanceRecord::work_date
            // for why: Laravel's date cast serializes with a "00:00:00" time
            // component on write, which then fails to string-match the plain
            // 'Y-m-d' values used in where()/updateOrCreate() lookups in
            // StaffRatingService. Kept as plain Y-m-d strings end to end.
            'attendance_score' => 'decimal:2',
            'overtime_score' => 'decimal:2',
            'supervisor_score' => 'decimal:2',
            'activity_score' => 'decimal:2',
            'total_score' => 'decimal:2',
            'rank' => 'integer',
            'computed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periodLabel(): string
    {
        return $this->period_type === 'week'
            ? \Carbon\Carbon::parse($this->period_start)->format('M j').' – '.\Carbon\Carbon::parse($this->period_end)->format('M j, Y')
            : \Carbon\Carbon::parse($this->period_start)->format('F Y');
    }
}
