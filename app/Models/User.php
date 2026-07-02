<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Support\MediaUrl;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['first_name', 'last_name', 'phone', 'companyName', 'email', 'password', 'google_id', 'avatar', 'email_verified_at', 'role', 'department', 'requested_role', 'other_role', 'address', 'date_of_birth', 'photo', 'approved_by_id', 'approved_at', 'is_active', 'employment_status', 'employment_status_reason', 'employment_status_changed_at', 'employment_status_changed_by_id', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'access_restricted', 'access_restricted_reason', 'access_restricted_by_id', 'access_restricted_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'              => 'datetime',
            'password'                       => 'hashed',
            'is_active'                      => 'boolean',
            'access_restricted'              => 'boolean',
            'access_restricted_at'           => 'datetime',
            'date_of_birth'                  => 'date',
            'approved_at'                    => 'datetime',
            'employment_status_changed_at'   => 'datetime',
            'two_factor_confirmed_at'        => 'datetime',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    public function hasAdminAccess(): bool
    {
        return $this->is_active
            && ! $this->access_restricted
            && $this->hasVerifiedEmail()
            && $this->role !== 'customer'
            && $this->canAdmin('admin.view');
    }

    public function isAccessRestricted(): bool
    {
        return (bool) $this->access_restricted;
    }

    public function canAdmin(string $permission): bool
    {
        $permissions = (array) config('printbuka_admin.roles.'.$this->role, []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public function rolePriority(): int
    {
        return (int) config('printbuka_admin.role_priority.'.$this->role, 0);
    }

    public function displayName(): string
    {
        return trim($this->first_name.' '.$this->last_name) ?: $this->email;
    }

    public function isPendingStaff(): bool
    {
        return $this->role === 'staff_pending' || (! $this->is_active && $this->requested_role !== null);
    }

    public function employmentStatusLabel(): string
    {
        return match ((string) ($this->employment_status ?? 'active')) {
            'suspended' => 'Suspended',
            'terminated' => 'Terminated',
            'pending' => 'Pending Onboarding',
            default => 'Active',
        };
    }

    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class)->orderByDesc('is_default')->latest();
    }

    public function staffActivities(): HasMany
    {
        return $this->hasMany(StaffActivity::class);
    }

    public function assignedTodos(): HasMany
    {
        return $this->hasMany(DailyTodo::class, 'user_id');
    }

    public function assignedByTodos(): HasMany
    {
        return $this->hasMany(DailyTodo::class, 'assigned_by_id');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(StaffPushSubscription::class);
    }

    public function staffProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function accessRestrictedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'access_restricted_by_id');
    }

    public function salaryStructures(): HasMany
    {
        return $this->hasMany(SalaryStructure::class, 'staff_id');
    }

    public function activeSalaryStructure(): ?\Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SalaryStructure::class, 'staff_id')
            ->where('is_active', true)
            ->latestOfMany('effective_date');
    }

    public function staffQueries(): HasMany
    {
        return $this->hasMany(StaffQuery::class, 'staff_id');
    }

    public function staffEvaluations(): HasMany
    {
        return $this->hasMany(StaffEvaluation::class, 'staff_id');
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class, 'staff_id');
    }

    public function kycCompleted(): bool
    {
        return $this->staffProfile?->kyc_completed_at !== null;
    }

    /**
     * FCM channel resolves all registered device tokens for this user.
     * Returns an array so the package fans out to every device the staff member owns.
     */
    public function routeNotificationForFcm(): array
    {
        return $this->pushSubscriptions()->pluck('device_token')->toArray();
    }

    public function profilePhotoUrl(): ?string
    {
        $photoUrl = $this->resolvedPhotoUrl($this->photo);

        if ($photoUrl !== null) {
            return $photoUrl;
        }

        if (filled($this->avatar) && filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return $this->generatedAvatarDataUrl();
    }

    public function profileInitials(): string
    {
        $initials = Str::of($this->displayName())
            ->replaceMatches('/[^A-Za-z0-9 ]+/', '')
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'PB';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profilePhotoUrl() ?? asset('favicon.png');
    }

    private function resolvedPhotoUrl(?string $photo): ?string
    {
        return MediaUrl::resolve($photo);
    }

    private function generatedAvatarDataUrl(): string
    {
        $palette = [
            '#0f766e',
            '#7c3aed',
            '#db2777',
            '#ea580c',
            '#2563eb',
            '#334155',
        ];

        $seed = (string) ($this->id ?? '').'|'.(string) ($this->email ?? '').'|'.(string) ($this->first_name ?? '');
        $index = (int) (abs((int) crc32($seed)) % count($palette));
        $bg = $palette[$index];
        $initials = htmlspecialchars($this->profileInitials(), ENT_QUOTES | ENT_XML1, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160" role="img" aria-label="{$initials}">
  <rect width="160" height="160" fill="{$bg}" />
  <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="56" font-weight="700">{$initials}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
