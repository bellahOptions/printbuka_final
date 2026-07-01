<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StaffKycReviewMail;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'post_held'                 => ['nullable', 'string', 'max:255'],
            'post_telephone'            => ['nullable', 'string', 'max:30'],
            'post_email'                => ['nullable', 'email', 'max:255'],
            'bank_name'                 => ['nullable', 'string', 'max:255'],
            'bank_account_number'       => ['nullable', 'string', 'max:30'],
            'pension_pin'               => ['nullable', 'string', 'max:50'],
            'tax_id'                    => ['nullable', 'string', 'max:50'],
            'declared_salary'           => ['nullable', 'integer', 'min:0'],
            'emergency_contact_notes'   => ['nullable', 'string', 'max:1000'],
            'mark_kyc_complete'         => ['nullable', 'boolean'],
        ]);

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
}
