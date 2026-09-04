<?php

namespace App\Support;

use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Roles used to live purely in config/printbuka_admin.php. They're now
 * backed by the `roles` table so Super Admin can add/edit roles through the
 * admin UI, but every existing call site still reads plain config() arrays
 * (config('printbuka_admin.roles'), .role_labels, .role_priority,
 * .staff_dashboard_menus). Rather than touch every one of those sites,
 * applyOverlay() rewrites those config values from the DB once per request
 * (cached) — DB rows become the live source of truth everywhere for free.
 */
class RoleRegistry
{
    private const CACHE_KEY = 'admin_roles:all:v1';

    private const CACHE_TTL_MINUTES = 5;

    public static function all(): Collection
    {
        return SafeCache::remember(
            self::CACHE_KEY,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            function (): Collection {
                try {
                    if (! Schema::hasTable('roles')) {
                        return collect();
                    }

                    return Role::query()->orderByDesc('priority')->get();
                } catch (\Throwable) {
                    return collect();
                }
            }
        );
    }

    /**
     * Overlay DB-defined roles onto the printbuka_admin config at runtime.
     * Safe to call unconditionally (e.g. from a service provider boot()) —
     * it's a no-op before the roles table exists or is seeded.
     */
    public static function applyOverlay(): void
    {
        $roles = self::all();

        if ($roles->isEmpty()) {
            return;
        }

        config([
            'printbuka_admin.roles' => $roles->pluck('permissions', 'slug')->all(),
            'printbuka_admin.role_priority' => $roles->pluck('priority', 'slug')->all() + ['customer' => 0],
            'printbuka_admin.role_labels' => $roles->pluck('label', 'slug')->all(),
            'printbuka_admin.staff_dashboard_menus' => $roles->pluck('dashboard_menu', 'slug')->filter()->all(),
        ]);
    }

    /**
     * @return list<string>
     */
    public static function permissionsFor(string $slug): array
    {
        $roles = self::all();

        if ($roles->isEmpty()) {
            // Table not migrated/seeded yet — fall back to the static config
            // so permission checks keep working during the migration window.
            return (array) config('printbuka_admin.roles.'.$slug, []);
        }

        return (array) ($roles->firstWhere('slug', $slug)?->permissions ?? []);
    }

    public static function priorityFor(string $slug): int
    {
        if ($slug === 'customer') {
            return 0;
        }

        $roles = self::all();

        if ($roles->isEmpty()) {
            return (int) config('printbuka_admin.role_priority.'.$slug, 0);
        }

        return (int) ($roles->firstWhere('slug', $slug)?->priority ?? 0);
    }

    public static function clearCache(): void
    {
        SafeCache::forget(self::CACHE_KEY);
    }
}
