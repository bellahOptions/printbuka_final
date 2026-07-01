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
        'post_held', 'post_telephone', 'post_email',
        'bank_name', 'bank_account_number', 'pension_pin', 'tax_id',
        'emergency_contact_notes', 'kyc_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_employment' => 'date',
            'kyc_completed_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return $this->kyc_completed_at !== null;
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

        return (int) round(($filled / count($fields)) * 100);
    }
}
