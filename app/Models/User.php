<?php

namespace App\Models;

use App\Models\Concerns\RoutesByUuid;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Support\MediaUrl;
use App\Support\RoleRegistry;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\PushSubscription;

#[Fillable(['first_name', 'last_name', 'phone', 'companyName', 'email', 'password', 'google_id', 'avatar', 'email_verified_at', 'role', 'secondary_role', 'department', 'requested_role', 'other_role', 'address', 'date_of_birth', 'photo', 'approved_by_id', 'approved_at', 'is_active', 'employment_status', 'employment_status_reason', 'employment_status_changed_at', 'employment_status_changed_by_id', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'access_restricted', 'access_restricted_reason', 'access_restricted_by_id', 'access_restricted_at', 'permission_overrides'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, RoutesByUuid;

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
            'permission_overrides'           => 'array',
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

    public function twoFactorTrustedDevices(): HasMany
    {
        return $this->hasMany(TwoFactorTrustedDevice::class);
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
        $permissions = RoleRegistry::permissionsFor($this->role);

        if (in_array('*', $permissions, true) || in_array($permission, $permissions, true)) {
            return true;
        }

        if ($this->secondary_role) {
            $secondaryPermissions = RoleRegistry::permissionsFor($this->secondary_role);

            if (in_array('*', $secondaryPermissions, true) || in_array($permission, $secondaryPermissions, true)) {
                return true;
            }
        }

        return in_array($permission, $this->extraPermissions(), true);
    }

    /**
     * Permission strings granted to this individual staff member on top of
     * their role — set by Super Admin from the staff profile page.
     *
     * @return list<string>
     */
    public function extraPermissions(): array
    {
        return array_values((array) ($this->permission_overrides ?? []));
    }

    public function rolePriority(): int
    {
        return max(
            RoleRegistry::priorityFor($this->role),
            $this->secondary_role ? RoleRegistry::priorityFor($this->secondary_role) : 0,
        );
    }

    public function hasSecondaryRole(): bool
    {
        return filled($this->secondary_role);
    }

    public function secondaryRoleLabel(): ?string
    {
        if (! $this->secondary_role) {
            return null;
        }

        return (string) (config('printbuka_admin.role_labels.'.$this->secondary_role) ?? ucwords(str_replace('_', ' ', $this->secondary_role)));
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
     * Browser Web Push subscriptions (one per subscribed browser/tab).
     *
     * Deliberately not named pushSubscriptions() — the webpush package's own
     * HasPushSubscriptions trait defines a method of that name, which would
     * silently collide with it.
     */
    public function webPushSubscriptions(): MorphMany
    {
        return $this->morphMany(config('webpush.model'), 'subscribable');
    }

    /**
     * WebPush channel resolves every browser subscription for this user.
     */
    public function routeNotificationForWebPush(): EloquentCollection
    {
        return $this->webPushSubscriptions;
    }

    public function updateWebPushSubscription(string $endpoint, ?string $key, ?string $token, ?string $contentEncoding): PushSubscription
    {
        /** @var PushSubscription|null $subscription */
        $subscription = PushSubscription::query()->where('endpoint', $endpoint)->first();

        if ($subscription && (string) $subscription->subscribable_id === (string) $this->getKey() && $subscription->subscribable_type === $this->getMorphClass()) {
            $subscription->forceFill([
                'public_key' => $key,
                'auth_token' => $token,
                'content_encoding' => $contentEncoding,
            ])->save();

            return $subscription;
        }

        $subscription?->delete();

        return $this->webPushSubscriptions()->create([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
            'content_encoding' => $contentEncoding,
        ]);
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
