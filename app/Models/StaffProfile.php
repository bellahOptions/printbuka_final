<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id', 'other_names', 'designation', 'date_of_employment',
        'sex', 'marital_status', 'state_of_origin', 'local_govt_area',
        'present_address', 'home_telephone',
        'next_of_kin_name', 'next_of_kin_relationship',
        'next_of_kin_home_address', 'next_of_kin_office_address',
        'bank_name', 'bank_account_number',
        'emergency_contact_notes', 'kyc_completed_at',
        'kyc_status', 'kyc_review_notes', 'kyc_reviewed_by_id', 'kyc_reviewed_at',
        'work_mode', 'onsite_days', 'work_mode_set_at',
    ];

    public const WORK_MODES = ['onsite', 'hybrid', 'remote'];

    /**
     * Weekday abbreviations accepted for the hybrid onsite_days selection,
     * matching Carbon's ->format('D') output (Mon, Tue, ... Sat). Attendance
     * is Monday–Saturday only (see AttendanceProcessingService), so Sunday
     * isn't offered as a selectable onsite day.
     */
    public const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    protected function casts(): array
    {
        return [
            'date_of_employment' => 'date',
            'kyc_completed_at'   => 'datetime',
            'kyc_reviewed_at'    => 'datetime',
            'onsite_days'        => 'array',
            'work_mode_set_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'kyc_reviewed_by_id');
    }

    public function isComplete(): bool
    {
        return $this->kyc_status === 'approved' || $this->kyc_completed_at !== null;
    }

    public function kycStatusLabel(): string
    {
        return match ($this->kyc_status ?? 'pending') {
            'approved'             => 'Approved',
            'correction_requested' => 'Correction Requested',
            default                => 'Pending Review',
        };
    }

    public function kycStatusBadgeClass(): string
    {
        return match ($this->kyc_status ?? 'pending') {
            'approved'             => 'bg-emerald-100 text-emerald-800',
            'correction_requested' => 'bg-amber-100 text-amber-800',
            default                => 'bg-slate-100 text-slate-700',
        };
    }

    public function needsWorkModePrompt(): bool
    {
        return $this->work_mode === null;
    }

    public function workModeLabel(): string
    {
        return match ($this->work_mode) {
            'onsite' => 'Onsite',
            'hybrid' => 'Hybrid',
            'remote' => 'Fully Remote',
            default  => 'Not set',
        };
    }

    /**
     * Whether this staff member is expected at the office today, given their
     * declared work mode. Drives whether attendance clock-in enforces the
     * office geofence: onsite (or undeclared) staff are always held to it,
     * fully remote staff never are, and hybrid staff only on their declared
     * onsite days.
     */
    public function isExpectedOnsite(?\Carbon\Carbon $when = null): bool
    {
        return match ($this->work_mode) {
            'remote' => false,
            'hybrid' => in_array(($when ?? now())->format('D'), $this->onsite_days ?? [], true),
            default  => true, // 'onsite', or not yet declared — safest default
        };
    }

    public function onsiteDaysLabel(): string
    {
        if ($this->work_mode !== 'hybrid' || blank($this->onsite_days)) {
            return '—';
        }

        return implode(', ', $this->onsite_days);
    }

    public function completionPercentage(): int
    {
        $fields = [
            'other_names', 'designation', 'date_of_employment', 'sex',
            'marital_status', 'state_of_origin', 'local_govt_area', 'present_address',
            'home_telephone', 'next_of_kin_name', 'next_of_kin_relationship',
            'next_of_kin_home_address', 'bank_name', 'bank_account_number',
        ];

        $filled = collect($fields)->filter(fn ($f) => filled($this->{$f}))->count();

        // Fields on the users table
        if (filled($this->user?->date_of_birth)) {
            $filled++;
        }
        if (filled($this->user?->photo)) {
            $filled++;
        }

        $total = \count($fields) + 2;

        return (int) round(($filled / $total) * 100);
    }
}
