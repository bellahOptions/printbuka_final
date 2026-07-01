<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffEvaluation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStaffEvaluationController extends Controller
{
    public function index(): View
    {
        abort_unless(
            request()->user()?->canAdmin('staff.evaluations')
            || request()->user()?->canAdmin('evaluations.view')
            || request()->user()?->canAdmin('*'),
            403
        );

        $query = StaffEvaluation::query()->with(['staff', 'evaluatedBy']);

        if ($staffId = request('staff_id')) {
            $query->where('staff_id', $staffId);
        }
        if ($month = request('month')) {
            $query->where('period_month', (int) $month);
        }
        if ($year = request('year')) {
            $query->where('period_year', (int) $year);
        }

        return view('admin.evaluations.index', [
            'evaluations' => $query->orderByDesc('period_year')->orderByDesc('period_month')->paginate(24),
            'staffList'   => User::query()->where('role', '!=', 'customer')->where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->canAdmin('staff.evaluations') || $request->user()?->canAdmin('*'), 403);

        $staffId = $request->query('staff_id');
        $month   = $request->query('month', now()->month);
        $year    = $request->query('year', now()->year);

        $existing = null;
        if ($staffId) {
            $existing = StaffEvaluation::query()
                ->where('staff_id', $staffId)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->first();
        }

        return view('admin.evaluations.create', [
            'staffList' => User::query()
                ->where('role', '!=', 'customer')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get(),
            'existing'   => $existing,
            'preselected'=> $staffId,
            'month'      => (int) $month,
            'year'       => (int) $year,
            'months'     => array_combine(range(1, 12), array_map(fn ($m) => date('F', mktime(0, 0, 0, $m, 1)), range(1, 12))),
        ]);
    }

    public function show(StaffEvaluation $evaluation): View
    {
        abort_unless(
            request()->user()?->canAdmin('staff.evaluations')
            || request()->user()?->canAdmin('*')
            || request()->user()?->id === $evaluation->staff_id,
            403
        );

        return view('admin.evaluations.show', [
            'evaluation' => $evaluation->load('staff', 'evaluatedBy'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAdmin('staff.evaluations') || $request->user()?->canAdmin('*'), 403);

        $validated = $request->validate([
            'staff_id'                  => ['required', 'exists:users,id'],
            'period_month'              => ['required', 'integer', 'between:1,12'],
            'period_year'               => ['required', 'integer', 'min:2020', 'max:2100'],
            'overall_rating'            => ['required', 'integer', 'between:1,5'],
            'punctuality_rating'        => ['nullable', 'integer', 'between:1,5'],
            'quality_of_work_rating'    => ['nullable', 'integer', 'between:1,5'],
            'teamwork_rating'           => ['nullable', 'integer', 'between:1,5'],
            'communication_rating'      => ['nullable', 'integer', 'between:1,5'],
            'initiative_rating'         => ['nullable', 'integer', 'between:1,5'],
            'strengths'                 => ['nullable', 'string', 'max:2000'],
            'areas_for_improvement'     => ['nullable', 'string', 'max:2000'],
            'comments'                  => ['nullable', 'string', 'max:2000'],
        ]);

        $eval = StaffEvaluation::updateOrCreate(
            [
                'staff_id'     => $validated['staff_id'],
                'period_month' => $validated['period_month'],
                'period_year'  => $validated['period_year'],
            ],
            array_merge($validated, [
                'evaluated_by_id' => $request->user()->id,
                'status'          => 'submitted',
            ])
        );

        return redirect()
            ->route('admin.staff.profile.show', $validated['staff_id'])
            ->with('status', 'Evaluation for '.\Carbon\Carbon::createFromDate($validated['period_year'], $validated['period_month'], 1)->format('F Y').' saved.');
    }

    public function acknowledge(Request $request, StaffEvaluation $evaluation): RedirectResponse
    {
        abort_unless($request->user()?->id === $evaluation->staff_id, 403);
        abort_if($evaluation->staff_acknowledged, 422, 'Already acknowledged.');

        $evaluation->forceFill([
            'staff_acknowledged'    => true,
            'staff_acknowledged_at' => now(),
            'status'                => 'acknowledged',
        ])->save();

        return back()->with('status', 'Evaluation acknowledged.');
    }
}
