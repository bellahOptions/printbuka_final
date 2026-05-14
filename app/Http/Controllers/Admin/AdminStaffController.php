<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffEmploymentStatusMail;
use App\Models\User;
use App\Services\ImportantActionNotifier;
use App\Support\LivewireSecureUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'canManageEmployment' => in_array(request()->user()?->role, ['super_admin', 'hr'], true),
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
            'employment_status' => $request->boolean('is_active', $user->is_active) ? 'active' : ($user->employment_status ?? 'pending'),
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

        $wasActive = (bool) $user->is_active;
        $previousEmploymentStatus = (string) ($user->employment_status ?? 'pending');

        $user->update($updates);

        if (! $wasActive || $previousEmploymentStatus !== 'active') {
            $this->notifyStaffEmploymentStatus($user->fresh(), 'active', null);
            app(ImportantActionNotifier::class)->notify(
                'Staff onboarding',
                $user->displayName().' was onboarded or updated by '.$request->user()->displayName().'.'
            );
        }

        return back()->with('status', 'Staff access updated.');
    }

    public function updateEmploymentStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['super_admin', 'hr'], true), 403);
        abort_if($user->id === $request->user()?->id, 422, 'You cannot suspend or terminate your own account.');
        abort_if($user->role === 'customer', 404);

        $validated = $request->validate([
            'employment_status' => ['required', 'string', Rule::in(['active', 'suspended', 'terminated'])],
            'employment_status_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = (string) $validated['employment_status'];
        $reason = $validated['employment_status_reason'] ?? null;

        $user->forceFill([
            'employment_status' => $status,
            'employment_status_reason' => $reason,
            'employment_status_changed_at' => now(),
            'employment_status_changed_by_id' => $request->user()->id,
            'is_active' => $status === 'active',
        ])->save();

        if ($status !== 'active') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $this->notifyStaffEmploymentStatus($user->fresh(), $status, $reason);
        app(ImportantActionNotifier::class)->notify(
            'Staff '.$user->employmentStatusLabel(),
            $user->displayName().' was marked as '.$user->employmentStatusLabel().' by '.$request->user()->displayName().'.'
        );

        return back()->with('status', 'Staff employment status updated.');
    }

    private function notifyStaffEmploymentStatus(User $staff, string $status, ?string $reason): void
    {
        if (! filled($staff->email)) {
            return;
        }

        try {
            Mail::to((string) $staff->email)->send(new StaffEmploymentStatusMail($staff, $status, $reason));
        } catch (\Throwable $exception) {
            Log::error('Staff employment status email failed.', [
                'staff_id' => $staff->id,
                'staff_email' => $staff->email,
                'status' => $status,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
