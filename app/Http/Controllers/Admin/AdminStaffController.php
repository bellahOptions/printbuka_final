<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\LivewireSecureUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.index', [
            'pendingStaff' => User::query()
                ->where('role', 'staff_pending')
                ->orWhere(function ($query): void {
                    $query->where('is_active', false)->whereNotNull('requested_role');
                })
                ->latest()
                ->paginate(6, ['*'], 'pending_page'),
            'staff' => User::query()
                ->where('role', '!=', 'customer')
                ->where('role', '!=', 'staff_pending')
                ->latest()
                ->paginate(12, ['*'], 'staff_page'),
            'staffStats' => [
                'total' => User::query()->where('role', '!=', 'customer')->count(),
                'active' => User::query()->where('role', '!=', 'customer')->where('is_active', true)->count(),
                'pending' => User::query()->where('role', 'staff_pending')->orWhere(fn ($query) => $query->where('is_active', false)->whereNotNull('requested_role'))->count(),
                'inactive' => User::query()->where('role', '!=', 'customer')->where('is_active', false)->count(),
            ],
            'roleCounts' => User::query()
                ->where('role', '!=', 'customer')
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->orderByDesc('total')
                ->get(),
            'departmentCounts' => User::query()
                ->where('role', '!=', 'customer')
                ->select('department', DB::raw('count(*) as total'))
                ->groupBy('department')
                ->orderByDesc('total')
                ->get(),
            'roles' => config('printbuka_admin.role_labels'),
            'departments' => config('printbuka_admin.departments'),
            'canAssignRoles' => request()->user()?->role === 'super_admin',
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(($request->user()?->role ?? null) === 'super_admin', 403);

        $request->merge([
            'role' => $request->input('role') ?: null,
            'department' => $request->input('department') ?: null,
        ]);

        $validated = $request->validate([
            'role' => ['nullable', 'string', Rule::in(array_keys(config('printbuka_admin.roles', [])))],
            'department' => ['nullable', 'string', Rule::in(array_values(config('printbuka_admin.departments', [])))],
            'is_active' => ['nullable', 'boolean'],
            'photo_upload_path' => ['nullable', 'string', 'max:255'],
            'photo' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'dimensions:min_width=80,min_height=80,max_width=4000,max_height=4000',
            ],
        ]);

        $updates = [
            'role' => $validated['role'] ?? $user->role,
            'department' => $validated['department'] ?? $user->department,
            'is_active' => $request->boolean('is_active', $user->is_active),
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ];

        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')->store('staff-photos', 'public');

            if (filled($user->photo) && Str::startsWith($user->photo, 'staff-photos/')) {
                Storage::disk('public')->delete($user->photo);
            }

            $updates['photo'] = $newPhotoPath;
        } elseif (filled($validated['photo_upload_path'] ?? null)) {
            $livewirePhotoPath = LivewireSecureUploads::consumePath(
                $request,
                (string) $validated['photo_upload_path'],
                ['staff-photos']
            );

            if (! $livewirePhotoPath) {
                throw ValidationException::withMessages([
                    'photo' => 'The uploaded photo is invalid or expired. Please upload it again.',
                ]);
            }

            if (filled($user->photo) && Str::startsWith($user->photo, 'staff-photos/')) {
                Storage::disk('public')->delete($user->photo);
            }

            $updates['photo'] = $livewirePhotoPath;
        }

        $user->update($updates);

        return back()->with('status', 'Staff access updated.');
    }
}
