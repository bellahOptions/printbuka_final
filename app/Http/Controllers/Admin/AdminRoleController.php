<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\PermissionCatalog;
use App\Support\RoleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.roles', [
            'roles' => Role::query()->orderByDesc('priority')->get(),
            'permissionGroups' => PermissionCatalog::grouped(),
            'userCountsByRole' => User::query()
                ->select('role')
                ->selectRaw('count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRole($request, isNew: true);

        $role = Role::create([
            'slug' => $validated['slug'],
            'label' => $validated['label'],
            'priority' => $validated['priority'],
            'permissions' => $validated['permissions'],
            'dashboard_menu' => $this->parseDashboardMenu($request->input('dashboard_menu')),
            'is_system' => false,
            'created_by_id' => $request->user()->id,
        ]);

        RoleRegistry::clearCache();

        return back()->with('status', 'Role "'.$role->label.'" created — it can now be assigned to staff.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $this->validateRole($request, isNew: false, role: $role);

        // super_admin must always keep full access — never let this form
        // strip the wildcard and accidentally lock every admin out.
        $permissions = $role->slug === 'super_admin' ? ['*'] : $validated['permissions'];

        $role->update([
            'label' => $validated['label'],
            'priority' => $validated['priority'],
            'permissions' => $permissions,
            'dashboard_menu' => $this->parseDashboardMenu($request->input('dashboard_menu')),
        ]);

        RoleRegistry::clearCache();

        return back()->with('status', 'Role "'.$role->label.'" updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system || $role->isProtected()) {
            return back()->with('status', 'Built-in roles cannot be deleted.');
        }

        $staffCount = User::query()->where('role', $role->slug)->count();

        if ($staffCount > 0) {
            return back()->with('status', "Cannot delete \"{$role->label}\" — {$staffCount} staff member(s) still hold this role. Reassign them first.");
        }

        $role->delete();

        RoleRegistry::clearCache();

        return back()->with('status', 'Role deleted.');
    }

    /**
     * @return array{slug?: string, label: string, priority: int, permissions: list<string>}
     */
    private function validateRole(Request $request, bool $isNew, ?Role $role = null): array
    {
        $catalog = PermissionCatalog::all();

        $rules = [
            'label' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(array_merge($catalog, ['*']))],
        ];

        if ($isNew) {
            $rules['slug'] = [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::notIn(array_merge(Role::PROTECTED_SLUGS, ['customer'])),
                Rule::unique('roles', 'slug'),
            ];
        }

        $validated = $request->validate($rules);

        if ($isNew) {
            $validated['slug'] = Str::slug($validated['slug'], '_');
        }

        $validated['permissions'] = array_values($validated['permissions'] ?? []);

        return $validated;
    }

    /**
     * @return list<string>
     */
    private function parseDashboardMenu(?string $raw): array
    {
        if (! filled($raw)) {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
