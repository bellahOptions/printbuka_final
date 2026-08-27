<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'work_location_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'clock_in_lat',
        'clock_in_lng',
        'clock_out_lat',
        'clock_out_lng',
        'clock_in_distance_meters',
        'clock_out_distance_meters',
        'clock_in_accuracy_meters',
        'clock_out_accuracy_meters',
        'clock_in_within_geofence',
        'clock_out_within_geofence',
        'clock_in_photo_path',
        'clock_out_photo_path',
        'overtime_minutes',
        'status',
        'flagged_reason',
        'notes',
        'corrected_by_id',
    ];

    protected function casts(): array
    {
        return [
            // Deliberately NOT cast to 'date' — Laravel's date cast serializes
            // with a "00:00:00" time component on write, which then fails to
            // string-match plain 'Y-m-d' values used in where()/updateOrCreate()
            // lookups throughout this controller/service. Kept as a plain
            // Y-m-d string end to end instead.
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'clock_in_lat' => 'decimal:7',
            'clock_in_lng' => 'decimal:7',
            'clock_out_lat' => 'decimal:7',
            'clock_out_lng' => 'decimal:7',
            'clock_in_within_geofence' => 'boolean',
            'clock_out_within_geofence' => 'boolean',
            'clock_in_accuracy_meters' => 'integer',
            'clock_out_accuracy_meters' => 'integer',
            'overtime_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workLocation(): BelongsTo
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by_id');
    }

    public function isClockedIn(): bool
    {
        return $this->clock_in_at !== null && $this->clock_out_at === null;
    }

    public function durationInMinutes(): ?int
    {
        if (! $this->clock_in_at) {
            return null;
        }

        return (int) $this->clock_in_at->diffInMinutes($this->clock_out_at ?? now());
    }

    public function hasOvertime(): bool
    {
        return $this->overtime_minutes > 0;
    }

    public function overtimeLabel(): string
    {
        $hours = intdiv($this->overtime_minutes, 60);
        $minutes = $this->overtime_minutes % 60;

        return trim(($hours > 0 ? $hours.'h ' : '').$minutes.'m');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'present' => 'Present',
            'late' => 'Late',
            'absent' => 'Absent',
            'on_leave' => 'On Leave',
            'half_day' => 'Half Day',
            default => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'present' => 'bg-emerald-100 text-emerald-800',
            'late' => 'bg-amber-100 text-amber-800',
            'absent' => 'bg-pink-100 text-pink-800',
            'on_leave' => 'bg-cyan-100 text-cyan-800',
            'half_day' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
