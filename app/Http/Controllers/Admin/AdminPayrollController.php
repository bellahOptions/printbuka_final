<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PayrollRunMail;
use App\Mail\PayslipMail;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\SalaryStructure;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminPayrollController extends Controller
{
    public function index(): View
    {
        abort_unless(request()->user()?->canAdmin('payroll.view') || request()->user()?->canAdmin('*'), 403);

        $allRuns = PayrollRun::query();

        return view('admin.payroll.index', [
            'runs'       => PayrollRun::query()->with('createdBy')->orderByDesc('payroll_year')->orderByDesc('payroll_month')->paginate(12),
            'totalRuns'  => (clone $allRuns)->count(),
            'paidRuns'   => (clone $allRuns)->where('status', 'paid')->count(),
            'draftRuns'  => (clone $allRuns)->whereIn('status', ['draft', 'finalized'])->count(),
        ]);
    }

    // --- Salary Structures ---

    public function salaryIndex(): View
    {
        abort_unless(request()->user()?->canAdmin('payroll.view') || request()->user()?->canAdmin('*'), 403);

        return view('admin.payroll.salary-structures', [
            'staffList'  => User::query()->where('role', '!=', 'customer')->where('is_active', true)->orderBy('last_name')->get(),
            'structures' => SalaryStructure::query()->with('staff')->where('is_active', true)->orderBy('effective_date', 'desc')->get(),
        ]);
    }

    public function salaryStore(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'staff_id'            => ['required', 'exists:users,id'],
            'effective_date'      => ['required', 'date'],
            'basic_salary'        => ['required', 'numeric', 'min:0'],
            'housing_allowance'   => ['nullable', 'numeric', 'min:0'],
            'transport_allowance' => ['nullable', 'numeric', 'min:0'],
            'medical_allowance'   => ['nullable', 'numeric', 'min:0'],
            'other_allowances'    => ['nullable', 'numeric', 'min:0'],
            'pension_deduction'   => ['nullable', 'numeric', 'min:0'],
            'tax_deduction'       => ['nullable', 'numeric', 'min:0'],
            'other_deductions'    => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);

        $staffUser = User::findOrFail($validated['staff_id']);
        abort_if($staffUser->role === 'customer', 422, 'Cannot set salary for a customer account.');

        // Deactivate previous active structures for this staff
        SalaryStructure::query()->where('staff_id', $staffUser->id)->where('is_active', true)->update(['is_active' => false]);

        SalaryStructure::query()->create(array_merge($validated, [
            'is_active'           => true,
            'created_by_id'       => $request->user()->id,
            'housing_allowance'   => (float) ($validated['housing_allowance'] ?? 0),
            'transport_allowance' => (float) ($validated['transport_allowance'] ?? 0),
            'medical_allowance'   => (float) ($validated['medical_allowance'] ?? 0),
            'other_allowances'    => (float) ($validated['other_allowances'] ?? 0),
            'pension_deduction'   => (float) ($validated['pension_deduction'] ?? 0),
            'tax_deduction'       => (float) ($validated['tax_deduction'] ?? 0),
            'other_deductions'    => (float) ($validated['other_deductions'] ?? 0),
        ]));

        return back()->with('status', 'Salary structure saved for '.$staffUser->displayName().'.');
    }

    // --- Payroll Runs ---

    public function createRun(): View
    {
        abort_unless(request()->user()?->canAdmin('payroll.manage') || request()->user()?->canAdmin('*'), 403);

        return view('admin.payroll.create-run', [
            'activeStaffCount' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->whereHas('salaryStructures', fn ($q) => $q->where('is_active', true))
                ->count(),
        ]);
    }

    public function storeRun(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'payroll_month' => ['required', 'integer', 'between:1,12'],
            'payroll_year'  => ['required', 'integer', 'min:2020', 'max:2100'],
            'payment_date'  => ['nullable', 'date'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $exists = PayrollRun::query()
            ->where('payroll_month', $validated['payroll_month'])
            ->where('payroll_year', $validated['payroll_year'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'payroll_month' => 'A payroll run for this month already exists.',
            ]);
        }

        $run = DB::transaction(function () use ($validated, $request): PayrollRun {
            $run = PayrollRun::query()->create([
                ...$validated,
                'status'        => 'draft',
                'created_by_id' => $request->user()->id,
            ]);

            // Auto-generate entries for all active staff with salary structures
            $staffWithSalary = User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->with(['salaryStructures' => fn ($q) => $q->where('is_active', true)->latest('effective_date')])
                ->get()
                ->filter(fn (User $u) => $u->salaryStructures->isNotEmpty());

            foreach ($staffWithSalary as $staff) {
                $salary = $staff->salaryStructures->first();
                $gross = $salary->grossSalary();
                $deductions = $salary->totalDeductions();

                PayrollEntry::query()->create([
                    'payroll_run_id'      => $run->id,
                    'staff_id'            => $staff->id,
                    'basic_salary'        => $salary->basic_salary,
                    'housing_allowance'   => $salary->housing_allowance,
                    'transport_allowance' => $salary->transport_allowance,
                    'medical_allowance'   => $salary->medical_allowance,
                    'other_allowances'    => $salary->other_allowances,
                    'gross_salary'        => $gross,
                    'pension_deduction'   => $salary->pension_deduction,
                    'tax_deduction'       => $salary->tax_deduction,
                    'other_deductions'    => $salary->other_deductions,
                    'total_deductions'    => $deductions,
                    'net_salary'          => max(0, $gross - $deductions),
                    'payment_status'      => 'pending',
                ]);
            }

            return $run;
        });

        return redirect()
            ->route('admin.payroll.run', $run)
            ->with('status', 'Payroll run for '.$run->periodLabel().' created with '.$run->entries()->count().' entries.');
    }

    public function showRun(PayrollRun $run): View
    {
        abort_unless(request()->user()?->canAdmin('payroll.view') || request()->user()?->canAdmin('*'), 403);

        return view('admin.payroll.run', [
            'run' => $run->load(['entries.staff', 'createdBy', 'finalizedBy']),
        ]);
    }

    public function updateEntry(Request $request, PayrollEntry $entry): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);
        $run = $entry->payrollRun;
        abort_if($run && ($run->status === 'finalized' || $run->status === 'paid'), 422, 'Finalized payrolls cannot be edited.');

        $validated = $request->validate([
            'basic_salary'        => ['required', 'numeric', 'min:0'],
            'housing_allowance'   => ['nullable', 'numeric', 'min:0'],
            'transport_allowance' => ['nullable', 'numeric', 'min:0'],
            'medical_allowance'   => ['nullable', 'numeric', 'min:0'],
            'other_allowances'    => ['nullable', 'numeric', 'min:0'],
            'pension_deduction'   => ['nullable', 'numeric', 'min:0'],
            'tax_deduction'       => ['nullable', 'numeric', 'min:0'],
            'other_deductions'    => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string', 'max:500'],
        ]);

        $gross = array_sum([
            (float) $validated['basic_salary'],
            (float) ($validated['housing_allowance'] ?? 0),
            (float) ($validated['transport_allowance'] ?? 0),
            (float) ($validated['medical_allowance'] ?? 0),
            (float) ($validated['other_allowances'] ?? 0),
        ]);
        $deductions = array_sum([
            (float) ($validated['pension_deduction'] ?? 0),
            (float) ($validated['tax_deduction'] ?? 0),
            (float) ($validated['other_deductions'] ?? 0),
        ]);

        $entry->update(array_merge($validated, [
            'housing_allowance'   => (float) ($validated['housing_allowance'] ?? 0),
            'transport_allowance' => (float) ($validated['transport_allowance'] ?? 0),
            'medical_allowance'   => (float) ($validated['medical_allowance'] ?? 0),
            'other_allowances'    => (float) ($validated['other_allowances'] ?? 0),
            'pension_deduction'   => (float) ($validated['pension_deduction'] ?? 0),
            'tax_deduction'       => (float) ($validated['tax_deduction'] ?? 0),
            'other_deductions'    => (float) ($validated['other_deductions'] ?? 0),
            'gross_salary'        => $gross,
            'total_deductions'    => $deductions,
            'net_salary'          => max(0, $gross - $deductions),
        ]));

        return back()->with('status', 'Entry updated for '.$entry->staff?->displayName().'.');
    }

    public function finalizeRun(Request $request, PayrollRun $run): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);
        abort_if($run->status !== 'draft', 422, 'Only draft payroll runs can be finalized.');

        $run->forceFill([
            'status'          => 'finalized',
            'finalized_by_id' => $request->user()->id,
            'finalized_at'    => now(),
        ])->save();

        return back()->with('status', 'Payroll for '.$run->periodLabel().' finalized.');
    }

    public function sendPayslips(Request $request, PayrollRun $run): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);
        abort_if($run->status === 'draft', 422, 'Finalize the payroll before sending payslips.');

        $sent = 0;
        $failed = 0;

        foreach ($run->entries()->with('staff')->get() as $entry) {
            $email = $entry->staff?->email ?? '';
            if (! filled($email)) { $failed++; continue; }

            try {
                Mail::to($email)->send(new PayslipMail($entry->load('payrollRun.createdBy')));
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Payslip email failed.', ['entry_id' => $entry->id, 'message' => $e->getMessage()]);
                $failed++;
            }
        }

        $run->forceFill(['status' => 'paid'])->save();

        return back()->with('status', "Payslips sent: {$sent} delivered, {$failed} failed. Run marked as paid.");
    }

    public function downloadPayslip(PayrollEntry $entry)
    {
        abort_unless(
            request()->user()?->canAdmin('payroll.view')
            || request()->user()?->canAdmin('*')
            || request()->user()?->id === $entry->staff_id,
            403
        );

        $pdf = Pdf::loadView('admin.payroll.payslip-pdf', [
            'entry' => $entry->load('staff', 'payrollRun'),
        ]);

        return $pdf->download('Payslip-'.$entry->payrollRun?->periodLabel().'-'.$entry->staff?->displayName().'.pdf');
    }

    public function downloadRunPdf(PayrollRun $run)
    {
        abort_unless(request()->user()?->canAdmin('payroll.view') || request()->user()?->canAdmin('*'), 403);

        $entries         = $run->entries()->with('staff')->get();
        $totalGross      = $entries->sum('gross_salary');
        $totalDeductions = $entries->sum('total_deductions');
        $totalNet        = $entries->sum('net_salary');

        $pdf = Pdf::loadView('admin.payroll.run-pdf', [
            'run'             => $run->load('createdBy', 'finalizedBy'),
            'entries'         => $entries,
            'totalGross'      => $totalGross,
            'totalDeductions' => $totalDeductions,
            'totalNet'        => $totalNet,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Payroll-'.$run->periodLabel().'.pdf');
    }

    public function emailToCeo(Request $request, PayrollRun $run): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('payroll.manage') || $request->user()?->canAdmin('*'), 403);

        $ceos = \App\Models\User::query()
            ->whereIn('role', ['managing_director', 'ceo'])
            ->where('is_active', true)
            ->get();

        if ($ceos->isEmpty()) {
            $ceos = \App\Models\User::query()
                ->whereIn('role', ['super_admin', 'admin'])
                ->where('is_active', true)
                ->get();
        }

        if ($ceos->isEmpty()) {
            return back()->with('error', 'No Managing Director or admin account found to send the report to.');
        }

        $sent = 0;
        $sentByName = $request->user()->displayName();

        foreach ($ceos as $ceo) {
            try {
                Mail::to($ceo->email)->send(new PayrollRunMail($run->load('entries.staff', 'createdBy', 'finalizedBy'), $sentByName));
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Payroll run email to CEO failed.', ['user_id' => $ceo->id, 'message' => $e->getMessage()]);
            }
        }

        return back()->with('status', "Payroll report for {$run->periodLabel()} sent to {$sent} recipient(s).");
    }
}
