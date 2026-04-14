<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['first_name', 'last_name', 'phone', 'companyName', 'email', 'password', 'google_id', 'avatar', 'email_verified_at', 'role', 'department', 'requested_role', 'other_role', 'address', 'date_of_birth', 'photo', 'approved_by_id', 'approved_at', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
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
        return $this->is_active && $this->role !== 'customer' && $this->canAdmin('admin.view');
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
}
