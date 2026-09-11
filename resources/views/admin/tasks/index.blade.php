@extends('layouts.admin')

@section('title', 'Today\'s Tasks | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <section class="pb-card p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-gray-700">Today's Task</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">Your tasks for today</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Review the tasks assigned to you and mark them as done when ready. Once a task is marked done, it is locked and can only receive a one-time manager review.</p>
                </div>
                <div class="space-y-2 text-right bg-pink-50 px-4 py-3 rounded-xl justify-self-start">
                    <p class="text-sm font-black text-slate-500">Due today</p>
                    <p class="text-4xl font-black text-pink-700">{{ number_format($todayTasks->count()) }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <section class="pb-card p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Your tasks</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">Tasks due today</h2>
                        </div>
                        <a href="{{ route('admin.tasks.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Refresh</a>
                    </div>

                    @if ($todayTasks->isEmpty())
                        <div class="pb-empty mt-6">
                            <p class="pb-empty-title">No tasks scheduled for today yet. Check back once your HOD assigns work to you.</p>
                        </div>
                    @else
                        <div class="mt-6 space-y-4">
                            @foreach ($todayTasks as $todo)
                                <article class="pb-card p-5">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="space-y-3">
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                                @php
                                                    $statusLabel = match ($todo->status) {
                                                        'pending' => 'Pending',
                                                        'working_on_it' => 'Working on it',
                                                        'completed', 'review_requested' => 'Completed',
                                                        'reviewed', 'approved', 'rejected' => 'Reviewed',
                                                        default => ucfirst(str_replace('_', ' ', (string) $todo->status)),
                                                    };
                                                @endphp
                                                <span class="font-black uppercase tracking-[0.2em] text-gray-700">{{ $statusLabel }}</span>
                                                <span class="pb-badge pb-badge-secondary">Due {{ $todo->due_date->format('M j, Y') }}</span>
                                            </div>
                                            <h3 class="text-xl font-black text-slate-950">{{ $todo->task }}</h3>
                                            <p class="text-sm leading-6 text-slate-600">Assigned by {{ $todo->assigner?->displayName() ?? 'System' }}</p>
                                            @if ($todo->order)
                                                <p class="text-sm text-slate-500">Order: <a href="{{ route('admin.orders.show', $todo->order) }}" class="font-semibold text-pink-700 hover:text-pink-800">{{ $todo->order->job_order_number ?? $todo->order->displayNumber() }}</a></p>
                                            @endif
                                            @if ($todo->notes)
                                                <p class="rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">{{ $todo->notes }}</p>
                                            @endif
                                            @if ($todo->status === 'reviewed' && $todo->review_comments)
                                                <p class="rounded-2xl bg-slate-100 p-4 text-sm leading-6 text-slate-700">Manager note: {{ $todo->review_comments }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-start gap-3 sm:items-end">
                                            @if ($todo->status === 'pending')
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <form method="POST" action="{{ route('admin.tasks.mark-working', $todo) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="pb-btn pb-btn-md pb-btn-secondary">Working on it</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.tasks.mark-done', $todo) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Mark as done</button>
                                                    </form>
                                                </div>
                                            @elseif ($todo->status === 'working_on_it')
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <span class="pb-badge pb-badge-secondary">Working on it</span>
                                                    <form method="POST" action="{{ route('admin.tasks.mark-done', $todo) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="pb-btn pb-btn-md pb-btn-primary">Mark as done</button>
                                                    </form>
                                                </div>
                                            @elseif (in_array($todo->status, ['completed', 'review_requested'], true))
                                                <span class="pb-badge pb-badge-warning">Awaiting review</span>
                                            @elseif ($todo->status === 'reviewed')
                                                <span class="pb-badge pb-badge-success">Reviewed{{ $todo->review_rating ? ' · '.$todo->review_rating.'/5' : '' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                @if ($canReview)
                    <section class="pb-card p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-gray-700">Review queue</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950">Tasks awaiting approval</h2>
                            </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="pb-badge pb-badge-primary">{{ number_format($reviewTasks->count()) }} pending</span>
                            @if ($workingOnCount > 0)
                                <span class="pb-badge pb-badge-secondary">{{ number_format($workingOnCount) }} working</span>
                            @endif
                        </div>
                    </div>
                        @if ($reviewTasks->isEmpty())
                            <div class="pb-empty mt-6">
                                <p class="pb-empty-title">No tasks are waiting for review right now.</p>
                            </div>
                        @else
                            <div class="mt-6 space-y-4">
                                @foreach ($reviewTasks as $todo)
                                    <article class="pb-card p-5">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="space-y-3">
                                                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                                    <span class="font-black uppercase tracking-[0.2em] text-gray-700">Completed</span>
                                                    <span class="pb-badge pb-badge-secondary">Assigned to {{ $todo->assignee?->displayName() ?? 'Staff' }}</span>
                                                </div>
                                                <h3 class="text-xl font-black text-slate-950">{{ $todo->task }}</h3>
                                                <p class="text-sm leading-6 text-slate-600">Assigned by {{ $todo->assigner?->displayName() ?? 'System' }}</p>
                                                @if ($todo->notes)
                                                    <p class="rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">{{ $todo->notes }}</p>
                                                @endif
                                                @if ($todo->order)
                                                    <p class="text-sm text-slate-500">Order: <a href="{{ route('admin.orders.show', $todo->order) }}" class="font-semibold text-pink-700 hover:text-pink-800">{{ $todo->order->job_order_number ?? $todo->order->displayNumber() }}</a></p>
                                                @endif
                                            </div>
                                            <div class="flex flex-col gap-3 sm:min-w-[260px]">
                                                <form method="POST" action="{{ route('admin.tasks.approve', $todo) }}" class="space-y-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="pb-label">Rating</label>
                                                    <select name="review_rating" required class="pb-select w-full">
                                                        <option value="">Select rating</option>
                                                        <option value="5">5 - Outstanding</option>
                                                        <option value="4">4 - Very Good</option>
                                                        <option value="3">3 - Good</option>
                                                        <option value="2">2 - Needs improvement</option>
                                                        <option value="1">1 - Warning</option>
                                                    </select>
                                                    <textarea name="review_comments" rows="3" class="pb-textarea w-full" placeholder="Optional manager comment"></textarea>
                                                    <button type="submit" class="pb-btn pb-btn-md pb-btn-success w-full">Finalize review</button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif
            </div>

                @if ($canAssign)
                    <section class="pb-card p-6">
                        <p class="text-sm font-black uppercase tracking-wide text-gray-700">Assign new task</p>
                        <form method="POST" action="{{ route('admin.tasks.store') }}" class="mt-5 space-y-4">
                            @csrf
                            <div class="pb-field">
                                <label class="pb-label">Staff assignees</label>
                                <select name="user_ids[]" multiple required size="6" class="pb-select w-full">
                                    @foreach ($assignableStaff as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->displayName() }} · {{ $staff->role }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs font-semibold text-slate-500">Hold Ctrl/Cmd to select multiple staff members.</p>
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Task</label>
                                <input name="task" required type="text" maxlength="500" class="pb-input w-full" />
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Priority</label>
                                <select name="priority" required class="pb-select w-full">
                                    <option value="medium" selected>Normal</option>
                                    <option value="high">Urgent</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Due date</label>
                                <input name="due_date" type="date" value="{{ today()->toDateString() }}" required class="pb-input w-full" />
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Order reference (optional)</label>
                                <input name="order_id" type="text" pattern="\d*" class="pb-input w-full" placeholder="Order ID" />
                            </div>
                            <div class="pb-field">
                                <label class="pb-label">Notes</label>
                                <textarea name="notes" rows="3" data-rich-editor class="pb-textarea w-full" placeholder="Optional task details"></textarea>
                            </div>
                            <button type="submit" class="pb-btn pb-btn-md pb-btn-primary w-full">Assign task</button>
                        </form>
                    </section>
                @endif
            </aside>
        </section>

        @if ($canAssign && $assignedTasks->isNotEmpty())
            <section class="pb-card p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-gray-700">Assigned by you</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Tasks you've assigned</h2>
                    </div>
                    <span class="pb-badge pb-badge-secondary">{{ number_format($assignedTasks->count()) }} total</span>
                </div>
                <div class="mt-6 pb-table-wrapper">
                    <table class="pb-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Assigned to</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedTasks as $todo)
                                @php
                                    $statusBadge = match ($todo->status) {
                                        'pending'         => 'pb-badge-secondary',
                                        'working_on_it'   => 'pb-badge-info',
                                        'completed', 'review_requested' => 'pb-badge-warning',
                                        'reviewed'        => 'pb-badge-success',
                                        default           => 'pb-badge-secondary',
                                    };
                                    $statusLabel = match ($todo->status) {
                                        'pending'         => 'Pending',
                                        'working_on_it'   => 'Working on it',
                                        'completed', 'review_requested' => 'Awaiting review',
                                        'reviewed'        => 'Reviewed'.($todo->review_rating ? ' ('.$todo->review_rating.'/5)' : ''),
                                        default           => ucfirst(str_replace('_', ' ', (string) $todo->status)),
                                    };
                                    $isOverdue = $todo->due_date?->isPast() && ! in_array($todo->status, ['reviewed'], true);
                                @endphp
                                <tr class="group">
                                    <td class="max-w-xs">
                                        <span class="line-clamp-2 font-semibold text-slate-900">{{ $todo->task }}</span>
                                        @if ($todo->notes)
                                            <span class="block text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $todo->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $todo->assignee?->displayName() ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $todo->due_date?->format('M j, Y') ?? '—' }}
                                        @if ($isOverdue)
                                            <span class="ml-1 text-xs">overdue</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <span class="pb-badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        @if ($todo->order)
                                            <a href="{{ route('admin.orders.show', $todo->order) }}" class="font-semibold text-pink-700 hover:underline">
                                                {{ $todo->order->job_order_number ?? $todo->order->displayNumber() }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
@endsection
