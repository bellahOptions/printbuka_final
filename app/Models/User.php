<?php

namespace App\Models;

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

#[Fillable(['first_name', 'last_name', 'phone', 'companyName', 'email', 'password', 'google_id', 'avatar', 'email_verified_at', 'role', 'department', 'requested_role', 'other_role', 'address', 'date_of_birth', 'photo', 'approved_by_id', 'approved_at', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function hasAdminAccess(): bool
    {
        return $this->is_active && $this->hasVerifiedEmail() && $this->role !== 'customer' && $this->canAdmin('admin.view');
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

    public function deliveryAddresses(): HasMany
    {
        return $this->hasMany(DeliveryAddress::class)->orderByDesc('is_default')->latest();
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
