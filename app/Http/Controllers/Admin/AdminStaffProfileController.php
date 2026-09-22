<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffKycReviewMail;
use App\Models\StaffProfile;
use App\Models\User;
use App\Notifications\StaffPushNotification;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use App\Support\LivewireSecureUploads;
use App\Support\PermissionCatalog;
use App\Support\RoleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminStaffProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_if($user->role === 'customer', 404);
        abort_unless(
            request()->user()?->canAdmin('staff.kyc')
            || request()->user()?->canAdmin('*')
            || request()->user()?->id === $user->id,
            403
        );

        $profile = $user->staffProfile ?? new StaffProfile(['user_id' => $user->id]);

        return view('admin.staff.profile', [
            'staffMember' => $user->load('staffProfile', 'salaryStructures', 'staffQueries', 'staffEvaluations'),
            'profile'     => $profile,
            'queries'     => $user->staffQueries()->with('issuedBy')->latest()->get(),
            'evaluations' => $user->staffEvaluations()->with('evaluatedBy')->latest('period_year')->latest('period_month')->get(),
            'latestSalary' => $user->salaryStructures()->where('is_active', true)->latest('effective_date')->first(),
            'payslips'    => $user->payrollEntries()->with('payrollRun')->latest()->take(12)->get(),
            'permissionGroups' => PermissionCatalog::grouped(),
            'rolePermissions' => RoleRegistry::permissionsFor($user->role),
            'assignableSecondaryRoles' => collect(config('printbuka_admin.roles', []))
                ->keys()
                ->reject(fn (string $slug): bool => in_array($slug, ['customer', 'staff_pending', 'super_admin', $user->role], true))
                ->mapWithKeys(fn (string $slug): array => [$slug => config('printbuka_admin.role_labels.'.$slug) ?? ucwords(str_replace('_', ' ', $slug))]),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'customer', 404);

        $actor      = $request->user();
        $isHrOrAdmin = $actor?->canAdmin('staff.kyc') || $actor?->canAdmin('*');
        $isSelf     = $actor?->id === $user->id;

        abort_unless($isHrOrAdmin || $isSelf, 403);

        // Once KYC is approved, only HR/admin can still edit
        $profile = $user->staffProfile;
        if (($profile?->kyc_status === 'approved') && ! $isHrOrAdmin) {
            return back()->with('status_error', 'Your KYC has been approved and is now locked. Contact HR if a correction is needed.');
        }

        $validated = $request->validate([
            'date_of_birth'             => ['nullable', 'date', 'before:today'],
            'photo_upload_path'         => ['nullable', 'string', 'max:500'],
            'other_names'               => ['nullable', 'string', 'max:255'],
            'designation'               => ['nullable', 'string', 'max:255'],
            'date_of_employment'        => ['nullable', 'date'],
            'sex'                       => ['nullable', 'string', 'in:Male,Female'],
            'marital_status'            => ['nullable', 'string', 'in:Single,Married,Separated,Divorced'],
            'state_of_origin'           => ['nullable', 'string', 'max:100'],
            'local_govt_area'           => ['nullable', 'string', 'max:100'],
            'present_address'           => ['nullable', 'string', 'max:500'],
            'home_telephone'            => ['nullable', 'string', 'max:30'],
            'next_of_kin_name'          => ['nullable', 'string', 'max:255'],
            'next_of_kin_relationship'  => ['nullable', 'string', 'max:100'],
            'next_of_kin_home_address'  => ['nullable', 'string', 'max:500'],
            'next_of_kin_office_address'=> ['nullable', 'string', 'max:500'],
            'bank_name'                 => ['nullable', 'string', 'max:255'],
            'bank_account_number'       => ['nullable', 'string', 'max:30'],
            'emergency_contact_notes'   => ['nullable', 'string', 'max:1000'],
            'mark_kyc_complete'         => ['nullable', 'boolean'],
        ]);

        // Save date_of_birth to users table
        $dateOfBirth = $validated['date_of_birth'] ?? null;
        unset($validated['date_of_birth']);
        if ($dateOfBirth !== null || $request->has('date_of_birth')) {
            $user->forceFill(['date_of_birth' => $dateOfBirth])->save();
        }

        // Handle photo/selfie upload
        $photoUploadPath = $validated['photo_upload_path'] ?? null;
        unset($validated['photo_upload_path']);
        if (filled($photoUploadPath)) {
            $livewirePhotoPath = LivewireSecureUploads::consumePath($request, $photoUploadPath, ['staff-photos']);

            if (! $livewirePhotoPath) {
                throw ValidationException::withMessages([
                    'photo_upload_path' => 'The uploaded photo is invalid or has expired. Please upload it again.',
                ]);
            }

            $this->deleteStoredPhoto($user->photo);

            if (CloudinaryUrl::isConfigured()) {
                $fullPath = Storage::disk('public')->path($livewirePhotoPath);
                $result = app(CloudinaryUploadService::class)->upload($fullPath, ['folder' => 'staff-photos']);
                $user->forceFill(['photo' => $result['public_id'] ?? $livewirePhotoPath])->save();
            } else {
                $user->forceFill(['photo' => $livewirePhotoPath])->save();
            }
        }

        $profile = StaffProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge(
                $validated,
                $request->boolean('mark_kyc_complete') && ! $user->staffProfile?->kyc_completed_at
                    ? ['kyc_completed_at' => now()]
                    : []
            )
        );

        // Sync address back to users table when staff edits own profile
        if ($request->user()?->id === $user->id && ! empty($validated['present_address'])) {
            $user->forceFill(['address' => $validated['present_address']])->save();
        }

        return back()->with('status', 'Bio-data profile saved.');
    }

    /**
     * Self-service: a staff member declares their own work arrangement once
     * — onsite, hybrid, or fully remote. Drives whether attendance clock-in
     * enforces the office geofence for them. Locked after the first save —
     * only Super Admin/IT can change it afterwards, via overrideWorkMode().
     */
    public function updateWorkMode(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || $user->role === 'customer', 404);

        if ($user->staffProfile?->work_mode_set_at !== null) {
            return back()->with('status_error', 'Your work arrangement is already set and locked. Contact the Process & Technology Manager or IT to change it.');
        }

        $validated = $request->validate([
            'work_mode'      => ['required', 'string', 'in:'.implode(',', StaffProfile::WORK_MODES)],
            'onsite_days'    => ['required_if:work_mode,hybrid', 'array', 'min:1'],
            'onsite_days.*'  => ['string', 'in:'.implode(',', StaffProfile::WEEKDAYS)],
        ]);

        StaffProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'work_mode'       => $validated['work_mode'],
                'onsite_days'     => $validated['work_mode'] === 'hybrid' ? array_values($validated['onsite_days']) : null,
                'work_mode_set_at' => now(),
            ]
        );

        return back()->with('status', 'Work arrangement saved.');
    }

    /**
     * Super Admin/IT override: change a staff member's work arrangement
     * after it has been locked by their own first save.
     */
    public function overrideWorkMode(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'customer', 404);

        $actor = $request->user();
        abort_unless($actor?->role === 'super_admin' || $actor?->canAdmin('*'), 403);

        $validated = $request->validate([
            'work_mode'      => ['required', 'string', 'in:'.implode(',', StaffProfile::WORK_MODES)],
            'onsite_days'    => ['required_if:work_mode,hybrid', 'array', 'min:1'],
            'onsite_days.*'  => ['string', 'in:'.implode(',', StaffProfile::WEEKDAYS)],
        ]);

        StaffProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'work_mode'       => $validated['work_mode'],
                'onsite_days'     => $validated['work_mode'] === 'hybrid' ? array_values($validated['onsite_days']) : null,
                'work_mode_set_at' => now(),
            ]
        );

        return back()->with('status', 'Work arrangement updated for '.$user->displayName().'.');
    }

    /**
     * Super Admin only: grant this specific staff member extra permission
     * strings on top of whatever their role already gives them, without
     * changing the role's permissions for everyone else who shares it.
     */
    public function updatePermissionOverrides(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'customer', 404);

        $actor = $request->user();
        abort_unless($actor?->role === 'super_admin' || $actor?->canAdmin('*'), 403);

        $validated = $request->validate([
            'permission_overrides'   => ['nullable', 'array'],
            'permission_overrides.*' => [Rule::in(PermissionCatalog::all())],
        ]);

        $user->forceFill([
            'permission_overrides' => array_values($validated['permission_overrides'] ?? []),
        ])->save();

        return back()->with('status', 'Extra permissions updated for '.$user->displayName().'.');
    }

    /**
     * Super Admin only: elevate a staff member by granting them a second,
     * additional role on top of their primary one (e.g. a Designer who is
     * also made a Production Manager) — without changing their primary role
     * or affecting anyone else who holds either role. The staff member is
     * notified either way (elevated or the grant removed).
     */
    public function updateSecondaryRole(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'customer', 404);

        $actor = $request->user();
        abort_unless($actor?->role === 'super_admin', 403);

        $assignableRoles = array_values(array_diff(
            array_keys(config('printbuka_admin.roles', [])),
            ['customer', 'staff_pending', 'super_admin', $user->role]
        ));

        $validated = $request->validate([
            'secondary_role' => ['nullable', 'string', Rule::in($assignableRoles)],
        ]);

        $newSecondaryRole = $validated['secondary_role'] ?? null;
        $previousSecondaryRole = $user->secondary_role;

        if ($newSecondaryRole === $previousSecondaryRole) {
            return back()->with('status', 'No change — that is already this staff member\'s secondary role.');
        }

        $user->forceFill(['secondary_role' => $newSecondaryRole])->save();

        $label = fn (?string $slug): ?string => $slug
            ? (string) (config('printbuka_admin.role_labels.'.$slug) ?? ucwords(str_replace('_', ' ', $slug)))
            : null;

        if ($newSecondaryRole) {
            $title = 'You were elevated to '.$label($newSecondaryRole);
            $body = ($actor->displayName()).' granted you additional '.$label($newSecondaryRole).
                ' access, on top of your '.$label($user->role).' role.';
            $type = 'role_elevated';
        } else {
            $title = 'Additional role removed';
            $body = ($actor->displayName()).' removed your '.$label($previousSecondaryRole).
                ' access. You now hold your '.$label($user->role).' role only.';
            $type = 'role_elevation_removed';
        }

        try {
            $user->notify(new StaffPushNotification(
                title: $title,
                body: $body,
                type: $type,
                data: [
                    'secondary_role' => $newSecondaryRole,
                    'previous_secondary_role' => $previousSecondaryRole,
                    'changed_by' => $actor->displayName(),
                    'action_url' => route('admin.staff.profile.show', $user),
                ],
            ));
        } catch (\Throwable $e) {
            Log::error('Role elevation push notification failed.', ['user_id' => $user->id, 'message' => $e->getMessage()]);
        }

        return back()->with('status', $newSecondaryRole
            ? $user->displayName().' has been elevated to also hold the '.$label($newSecondaryRole).' role. Staff has been notified.'
            : 'Secondary role removed for '.$user->displayName().'. Staff has been notified.');
    }

    public function markKycComplete(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.kyc') || $request->user()?->canAdmin('*'), 403);

        $profile = StaffProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->forceFill(['kyc_completed_at' => now()])->save();

        return back()->with('status', 'KYC marked as complete for '.$user->displayName().'.');
    }

    public function reviewKyc(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.kyc') || $request->user()?->canAdmin('*'), 403);
        abort_if($user->role === 'customer', 404);

        $validated = $request->validate([
            'kyc_action' => ['required', 'in:approve,request_correction'],
            'kyc_notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $status = $validated['kyc_action'] === 'approve' ? 'approved' : 'correction_requested';

        $profile = StaffProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->forceFill([
            'kyc_status'         => $status,
            'kyc_review_notes'   => $validated['kyc_notes'] ?? null,
            'kyc_reviewed_by_id' => $request->user()->id,
            'kyc_reviewed_at'    => now(),
            'kyc_completed_at'   => $status === 'approved'
                ? ($profile->kyc_completed_at ?? now())
                : null,
        ])->save();

        try {
            Mail::to($user->email)->send(new StaffKycReviewMail(
                staff:        $user,
                status:       $status,
                notes:        $validated['kyc_notes'] ?? null,
                reviewerName: $request->user()->displayName(),
            ));
        } catch (\Throwable $e) {
            Log::error('KYC review mail failed.', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        $label = $status === 'approved' ? 'approved' : 'returned for correction';

        return back()->with('status', "KYC for {$user->displayName()} has been {$label}. Staff has been notified by email.");
    }

    private function deleteStoredPhoto(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        if (CloudinaryUrl::isCloudinaryResource($path)) {
            try {
                app(CloudinaryUploadService::class)->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Storage::disk('public')->delete($path);
    }
}
