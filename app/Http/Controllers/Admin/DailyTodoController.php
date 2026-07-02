<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TaskAssignedMail;
use App\Mail\TaskReviewOutcomeMail;
use App\Models\DailyTodo;
use App\Models\User;
use App\Notifications\StaffPushNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DailyTodoController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $todayTasks = DailyTodo::query()
    ->where('user_id', $user->id)
    ->whereDate('due_date', today())
    ->with(['order', 'assigner', 'reviewer'])
    ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
    ->orderByRaw("FIELD(status, 'pending', 'working_on_it', 'completed', 'review_requested', 'reviewed', 'approved', 'rejected')")
    ->orderBy('due_date')
    ->get();

        $reviewTasks = collect();

        if ($this->canReview($user)) {
            $reviewTasks = DailyTodo::query()
                ->whereIn('status', ['completed', 'review_requested'])
                ->whereNull('reviewed_at')
                ->with(['order', 'assignee', 'assigner'])
                ->latest('completed_at')
                ->latest('updated_at')
                ->get();
        }

        $assignableStaff = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $assignedTasks = collect();
        if ($this->canAssign($user)) {
            $assignedTasks = DailyTodo::query()
                ->where('assigned_by_id', $user->id)
                ->with(['assignee', 'order'])
                ->orderByDesc('due_date')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }

        return view('admin.tasks.index', [
            'todayTasks' => $todayTasks,
            'reviewTasks' => $reviewTasks,
            'assignableStaff' => $assignableStaff,
            'assignedTasks' => $assignedTasks,
            'canReview' => $this->canReview($user),
            'canAssign' => $this->canAssign($user),
            'workingOnCount' => $this->canReview($user)
                ? DailyTodo::query()->where('status', 'working_on_it')->whereDate('due_date', today())->distinct('user_id')->count('user_id')
                : 0,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canAssign($user), 403);
$validated = $request->validate([
    'user_ids' => ['required', 'array', 'min:1'],
    'user_ids.*' => ['required', 'integer', 'exists:users,id', 'distinct'],
    'task' => ['required', 'string', 'max:500'],
    'priority' => ['required', 'string', Rule::in(['high', 'medium', 'low'])],
    'notes' => ['nullable', 'string', 'max:3000'],
    'due_date' => ['required', 'date'],
    'order_id' => ['nullable', 'integer', 'exists:orders,id'],
]);

        $assignees = User::query()
            ->whereIn('id', $validated['user_ids'])
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $created = 0;

        foreach ($validated['user_ids'] as $assigneeId) {
            $assignee = $assignees->get((int) $assigneeId);
            if (! $assignee) {
                continue;
            }

            $todo = DailyTodo::create([
    'user_id' => $assignee->id,
    'assigned_by_id' => $user->id,
    'order_id' => $validated['order_id'] ?? null,
    'task' => $validated['task'],
    'priority' => $validated['priority'],
    'notes' => $validated['notes'] ?? null,
    'due_date' => $validated['due_date'],
    'status' => 'pending',
]);
            $created++;

            $this->sendAssignmentEmail($assignee, $todo, $user);
            $this->notifyAssignee($assignee, $todo, $user);
        }

        if ($created === 0) {
            return back()->with('warning', 'No valid active staff members were selected for assignment.');
        }

        return back()->with('status', 'Task assigned to '.$created.' staff member(s).');
    }

    public function markWorking(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($todo->user_id === $user->id, 403);
        abort_unless($todo->status === 'pending', 403);

        $todo->update([
            'status' => 'working_on_it',
        ]);

        return back()->with('status', 'Task status updated to working on it.');
    }

    public function markDone(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($todo->user_id === $user->id, 403);
        abort_unless(in_array($todo->status, ['pending', 'working_on_it'], true), 403);

        $todo->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('status', 'Task marked complete.');
    }

    public function approve(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canReview($user), 403);
        abort_unless(in_array($todo->status, ['completed', 'review_requested'], true), 403);
        abort_unless($todo->reviewed_at === null, 403);

        $validated = $request->validate([
            'review_rating' => ['required', 'integer', 'between:1,5'],
            'review_comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $rating = (int) $validated['review_rating'];

        $todo->update([
            'status' => 'reviewed',
            'reviewed_by_id' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'] ?? null,
            'review_rating' => $rating,
        ]);

        $todo->loadMissing(['assignee', 'reviewer', 'order', 'assigner']);
        $this->sendOutcomeEmailIfRequired($todo, $rating);
        $this->notifyReviewOutcome($todo, $rating, $user);

        return back()->with('status', 'Task review submitted successfully.');
    }

    public function reject(Request $request, DailyTodo $todo): RedirectResponse
    {
        return $this->approve($request, $todo);
    }

    private function canReview(?User $user): bool
    {
        return $user !== null && in_array($user->role, config('printbuka_admin.todo_review_roles', []), true);
    }

    private function canAssign(?User $user): bool
    {
        return $user !== null && in_array($user->role, ['operations_manager', 'super_admin', 'managing_director'], true);
    }

    private function notifyAssignee(User $assignee, DailyTodo $todo, User $assigner): void
    {
        try {
            $assignee->notify(new StaffPushNotification(
                title: 'New Task Assigned',
                body: $todo->task,
                type: 'task_assigned',
                data: [
                    'task_id'     => $todo->id,
                    'priority'    => $todo->priority ?? '',
                    'due_date'    => $todo->due_date?->toDateString() ?? '',
                    'assigned_by' => $assigner->displayName(),
                ],
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function notifyReviewOutcome(DailyTodo $todo, int $rating, User $reviewer): void
    {
        $assignee = $todo->assignee;

        if (! $assignee) {
            return;
        }

        $outcome = match (true) {
            $rating >= 4 => 'approved',
            $rating === 1 => 'rejected',
            default      => 'reviewed',
        };

        try {
            $assignee->notify(new StaffPushNotification(
                title: 'Task '.ucfirst($outcome),
                body: $todo->task,
                type: 'task_reviewed',
                data: [
                    'task_id'     => $todo->id,
                    'outcome'     => $outcome,
                    'rating'      => $rating,
                    'reviewed_by' => $reviewer->displayName(),
                    'comments'    => $todo->review_comments ?? '',
                ],
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function sendAssignmentEmail(User $assignee, DailyTodo $todo, User $assigner): void
    {
        if (! filled($assignee->email)) {
            return;
        }

        try {
            Mail::to((string) $assignee->email)->send(new TaskAssignedMail($assignee, $todo->loadMissing('order'), $assigner));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function sendOutcomeEmailIfRequired(DailyTodo $todo, int $rating): void
    {
        if (! in_array($rating, [1, 4, 5], true)) {
            return;
        }

        $assignee = $todo->assignee;
        if (! $assignee || ! filled($assignee->email)) {
            return;
        }

        try {
            Mail::to((string) $assignee->email)->send(new TaskReviewOutcomeMail($assignee, $todo, $rating));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
