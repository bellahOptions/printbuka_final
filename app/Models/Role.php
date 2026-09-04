<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    /**
     * Slugs that core application code branches on directly (middleware,
     * pending-staff detection, the "who can assign roles" check) rather than
     * looking up permissions — these can never be deleted or renamed, no
     * matter what is_system says.
     */
    public const PROTECTED_SLUGS = ['super_admin', 'staff_pending'];

    protected $fillable = [
        'slug',
        'label',
        'priority',
        'permissions',
        'dashboard_menu',
        'is_system',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'dashboard_menu' => 'array',
            'priority' => 'integer',
            'is_system' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function isProtected(): bool
    {
        return in_array($this->slug, self::PROTECTED_SLUGS, true);
    }

    public function hasWildcard(): bool
    {
        return in_array('*', (array) $this->permissions, true);
    }
}
