@extends('layouts.admin')

@section('title', 'Production Job Tracker (ORM) | Printbuka')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6">

    {{-- ════════ HERO ════════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="pb-badge pb-badge-cyan">ORM — Operations</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="pb-status-dot pb-status-online"><span></span><span></span></span>
                        Live production queue
                    </span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Job tracker
                </h1>
                <p class="text-sm text-slate-500 max-w-lg">
                    Monitor every order from intake through QC, production, and final delivery.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(auth()->user()->canAdmin('orders.create'))
                    <a href="{{ route('admin.orders.create') }}"
                       class="pb-btn pb-btn-md pb-btn-primary text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Job
                    </a>
                @endif
                @if($canSendTodoReminders)
                    <form method="POST" action="{{ route('admin.orders.todo-reminders.send') }}">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-outline text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Send Reminders
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    {{-- ════════ ORM PIPELINE — ATTENTION NEEDED ════════ --}}
    @if($staffTodos->isNotEmpty())
        <section class="animate-fade-in-up delay-100 pb-card overflow-hidden">
            <div class="border-b border-slate-100 p-6">
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-4 w-1 rounded-full bg-amber-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Needs Attention</p>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Jobs requiring immediate action</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Paid jobs currently stuck, awaiting staff action or approval.
                </p>
            </div>

            <div class="p-6 grid gap-4 lg:grid-cols-2">
                @foreach($staffTodos as $todoGroup)
                    <article class="rounded-xl border border-amber-200 bg-amber-50/50 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="pb-avatar pb-avatar-sm shrink-0">
                                    <div class="pb-avatar-fallback bg-amber-100 text-amber-800 text-xs font-bold">
                                        {{ strtoupper(substr($todoGroup['recipient']->displayName(), 0, 2)) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $todoGroup['recipient']->displayName() }}</p>
                                    <p class="text-xs text-slate-400">{{ $todoGroup['recipient']->email }}</p>
                                </div>
                            </div>
                            <span class="pb-badge pb-badge-warning text-[10px]">
                                {{ count($todoGroup['items']) }} task{{ count($todoGroup['items']) !== 1 ? 's' : '' }}
                            </span>
                        </div>
                        <ul class="space-y-2">
                            @foreach(array_slice($todoGroup['items'], 0, 5) as $item)
                                <li class="rounded-lg bg-white border border-amber-100 p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <a href="{{ route('admin.orders.show', $item['order']) }}"
                                           class="text-sm font-semibold text-brand-700 hover:text-brand-900 transition-colors">
                                            {{ $item['order']->job_order_number ?? $item['order']->displayNumber() }}
                                        </a>
                                        <span class="text-xs font-medium text-red-600 shrink-0">
                                            {{ $item['stuck_hours'] }}h stuck
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1">{{ $item['order']->customer_name }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="pb-badge pb-badge-secondary text-[9px]">{{ $item['phase'] }}</span>
                                        <span class="pb-badge {{ $item['payment_status'] === 'paid' ? 'pb-badge-success' : 'pb-badge-warning' }} text-[9px]">
                                            {{ $item['payment_status'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ════════ LIVE ORDERS TABLE (Livewire) ════════ --}}
    <section class="animate-fade-in-up delay-200">
        <div class="mb-3 flex items-center gap-2">
            <div class="h-4 w-1 rounded-full bg-cyan-500"></div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">All Jobs</p>
        </div>
        <livewire:admin.orders-table />
    </section>

</div>
@endsection
