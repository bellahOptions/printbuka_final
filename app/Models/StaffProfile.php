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
    ];

    protected function casts(): array
    {
        return [
            'date_of_employment' => 'date',
            'kyc_completed_at'   => 'datetime',
            'kyc_reviewed_at'    => 'datetime',
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
