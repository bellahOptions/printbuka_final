@extends('layouts.admin')

@section('title', 'Today\'s Tasks | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <section class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Today's Task</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-950">Your tasks for today</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Review the tasks assigned to you and mark them as done when ready. Once a task is marked done, it is locked and can only receive a one-time manager review.</p>
                </div>
                <div class="space-y-2 text-right">
                    <p class="text-sm font-black text-slate-500">Due today</p>
                    <p class="text-4xl font-black text-pink-700">{{ number_format($todayTasks->count()) }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <section class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Your tasks</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950">Tasks due today</h2>
                        </div>
                        <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-black text-slate-800 hover:border-pink-300 hover:text-pink-700">Refresh</a>
                    </div>

                    @if ($todayTasks->isEmpty())
                        <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm font-semibold text-slate-600">
                            No tasks scheduled for today yet. Check back once your manager assigns work or use the task panel to request an assignment.
                        </div>
                    @else
                        <div class="mt-6 space-y-4">
                            @foreach ($todayTasks as $todo)
                                <article class="rounded-3xl border border-slate-200 p-5 shadow-sm">
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
                                                <span class="font-black uppercase tracking-[0.2em] text-cyan-700">{{ $statusLabel }}</span>
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Due {{ $todo->due_date->format('M j, Y') }}</span>
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
                                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-600 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-700">Working on it</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.tasks.mark-done', $todo) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Mark as done</button>
                                                    </form>
                                                </div>
                                            @elseif ($todo->status === 'working_on_it')
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <span class="rounded-full bg-cyan-100 px-4 py-2 text-sm font-black text-cyan-800">Working on it</span>
                                                    <form method="POST" action="{{ route('admin.tasks.mark-done', $todo) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Mark as done</button>
                                                    </form>
                                                </div>
                                            @elseif (in_array($todo->status, ['completed', 'review_requested'], true))
                                                <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-black text-amber-800">Awaiting review</span>
                                            @elseif ($todo->status === 'reviewed')
                                                <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-800">Reviewed{{ $todo->review_rating ? ' · '.$todo->review_rating.'/5' : '' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                @if ($canReview)
                    <section class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Review queue</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950">Tasks awaiting approval</h2>
                            </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-pink-50 px-4 py-2 text-sm font-black text-pink-700">{{ number_format($reviewTasks->count()) }} pending</span>
                            @if ($workingOnCount > 0)
                                <span class="rounded-full bg-cyan-50 px-4 py-2 text-sm font-black text-cyan-700">{{ number_format($workingOnCount) }} working</span>
                            @endif
                        </div>

                        @if ($reviewTasks->isEmpty())
                            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm font-semibold text-slate-600">
                                No tasks are waiting for review right now.
                            </div>
                        @else
                            <div class="mt-6 space-y-4">
                                @foreach ($reviewTasks as $todo)
                                    <article class="rounded-3xl border border-slate-200 p-5 shadow-sm">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="space-y-3">
                                                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                                                    <span class="font-black uppercase tracking-[0.2em] text-cyan-700">Completed</span>
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Assigned to {{ $todo->assignee?->displayName() ?? 'Staff' }}</span>
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
                                                    <label class="block text-xs font-black uppercase tracking-wide text-slate-600">Rating</label>
                                                    <select name="review_rating" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                                        <option value="">Select rating</option>
                                                        <option value="5">5 - Outstanding</option>
                                                        <option value="4">4 - Very Good</option>
                                                        <option value="3">3 - Good</option>
                                                        <option value="2">2 - Needs improvement</option>
                                                        <option value="1">1 - Warning</option>
                                                    </select>
                                                    <textarea name="review_comments" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Optional manager comment"></textarea>
                                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">Finalize review</button>
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

            <aside class="space-y-6">
                <section class="rounded-2xl border border-slate-200/60 bg-slate-50 p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-slate-500">How it works</p>
                    <div class="mt-4 space-y-4 text-sm leading-6 text-slate-600">
                        <p><strong class="text-slate-900">1.</strong> Staff members receive daily tasks and complete them in the dashboard.</p>
                        <p><strong class="text-slate-900">2.</strong> Once a staff member marks a task done, the task becomes locked.</p>
                        <p><strong class="text-slate-900">3.</strong> Operations Managers and Super Admin submit a one-time review rating.</p>
                        <p><strong class="text-slate-900">4.</strong> Ratings of 4-5 send appraisal emails; rating 1 sends a warning email.</p>
                    </div>
                </section>

                @if ($canAssign)
                    <section class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Assign new task</p>
                        <form method="POST" action="{{ route('admin.tasks.store') }}" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-black text-slate-800">Staff assignees</label>
                                <select name="user_ids[]" multiple required size="6" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                    @foreach ($assignableStaff as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->displayName() }} · {{ $staff->role }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs font-semibold text-slate-500">Hold Ctrl/Cmd to select multiple staff members.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-800">Task</label>
                                <input name="task" required type="text" maxlength="500" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-800">Due date</label>
                                <input name="due_date" type="date" value="{{ today()->toDateString() }}" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100" />
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-800">Order reference (optional)</label>
                                <input name="order_id" type="text" pattern="\d*" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Order ID" />
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-800">Notes</label>
                                <textarea name="notes" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-pink-500 focus:ring-4 focus:ring-pink-100" placeholder="Optional task details"></textarea>
                            </div>
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Assign task</button>
                        </form>
                    </section>
                @endif
            </aside>
        </section>
    </div>
@endsection
