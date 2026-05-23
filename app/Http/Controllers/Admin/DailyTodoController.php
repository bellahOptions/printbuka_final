<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTodo;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->orderByRaw("FIELD(status, 'pending', 'working_on_it', 'review_requested', 'approved', 'rejected')")
            ->orderBy('due_date')
            ->get();

        $reviewTasks = collect();

        if ($this->canReview($user)) {
            $reviewTasks = DailyTodo::query()
                ->where('status', 'review_requested')
                ->with(['order', 'assignee', 'assigner'])
                ->latest()
                ->get();
        }

        $assignableStaff = User::query()
            ->where('role', '!=', 'customer')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.tasks.index', [
            'todayTasks' => $todayTasks,
            'reviewTasks' => $reviewTasks,
            'assignableStaff' => $assignableStaff,
            'canReview' => $this->canReview($user),
            'workingOnCount' => $this->canReview($user)
                ? DailyTodo::query()->where('status', 'working_on_it')->whereDate('due_date', today())->distinct('user_id')->count('user_id')
                : 0,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canReview($user), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'task' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'due_date' => ['required', 'date'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ]);

        DailyTodo::create([
            'user_id' => $validated['user_id'],
            'assigned_by_id' => $user->id,
            'order_id' => $validated['order_id'] ?? null,
            'task' => $validated['task'],
            'notes' => $validated['notes'] ?? null,
            'due_date' => $validated['due_date'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Task added to today’s list.');
    }

    public function markWorking(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($todo->user_id === $user->id, 403);
        abort_unless(in_array($todo->status, ['pending', 'rejected'], true), 403);

        $todo->update([
            'status' => 'working_on_it',
            'completed_at' => null,
            'reviewed_by_id' => null,
            'reviewed_at' => null,
            'review_comments' => null,
        ]);

        return back()->with('status', 'Task status updated to working on it.');
    }

    public function markDone(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($todo->user_id === $user->id, 403);
        abort_unless(in_array($todo->status, ['pending', 'working_on_it', 'rejected'], true), 403);

        $todo->update([
            'status' => 'review_requested',
            'completed_at' => now(),
        ]);

        return back()->with('status', 'Task marked complete and sent for review.');
    }

    public function approve(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canReview($user), 403);
        abort_unless($todo->status === 'review_requested', 403);

        $validated = $request->validate([
            'review_comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $todo->update([
            'status' => 'approved',
            'reviewed_by_id' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'] ?? null,
        ]);

        return back()->with('status', 'Task review approved.');
    }

    public function reject(Request $request, DailyTodo $todo): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->canReview($user), 403);
        abort_unless($todo->status === 'review_requested', 403);

        $validated = $request->validate([
            'review_comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $todo->update([
            'status' => 'rejected',
            'reviewed_by_id' => $user->id,
            'reviewed_at' => now(),
            'review_comments' => $validated['review_comments'] ?? null,
        ]);

        return back()->with('status', 'Task review rejected.');
    }

    private function canReview(?User $user): bool
    {
        return $user !== null && in_array($user->role, config('printbuka_admin.todo_review_roles', []), true);
    }
}
