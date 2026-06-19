@extends('layouts.admin')

@section('title', 'Dashboard | Printbuka Admin')

@section('content')
@php
    $admin = auth()->user();
@endphp
<div class="mx-auto max-w-[1440px] space-y-6">

    {{-- ════════════════════════════════════════════════
         HERO: Welcome + Quick Actions
    ════════════════════════════════════════════════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        {{-- Accent stripe --}}
        <div class="h-1 w-full bg-gradient-to-r from-brand-600 via-brand-500 to-pink-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="pb-badge pb-badge-primary">{{ $adminRoleLabel }}</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="pb-status-dot pb-status-online"><span></span><span></span></span>
                        Live workspace
                    </span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                    <span class="text-brand-700">{{ $admin->first_name ?? $admin->displayName() }}</span>
                </h1>
                <p class="text-sm text-slate-500 max-w-lg">
                    Here's what's happening across production, sales, and your team today.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($admin->canAdmin('orders.create'))
                    <a href="{{ route('admin.orders.create') }}"
                       class="pb-btn pb-btn-md pb-btn-primary text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Job
                    </a>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="pb-btn pb-btn-md pb-btn-outline text-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Job Tracker
                </a>
            </div>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════
         ORM KPIs — Order / Operations Resource Metrics
    ════════════════════════════════════════════════ --}}
    <div class="animate-fade-in-up delay-100">
        <div class="mb-3 flex items-center gap-2">
            <div class="h-4 w-1 rounded-full bg-brand-600"></div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Operations (ORM)</p>
        </div>
        <livewire:admin.realtime-stats />
    </div>

    {{-- ════════════════════════════════════════════════
         SHOP ORDERS — stats (visible to shop-orders.view)
    ════════════════════════════════════════════════ --}}
    @if(!is_null($shopOrderStats))
    <section class="animate-fade-in-up delay-100">
        <div class="mb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-4 w-1 rounded-full bg-pink-500"></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Shop Orders</p>
            </div>
            <a href="{{ route('admin.shop-orders.index') }}" class="pb-btn pb-btn-sm pb-btn-outline text-xs">
                View All Orders
            </a>
        </div>

        {{-- KPI row --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-3">
            <div class="pb-card p-4">
                <p class="text-xs font-bold uppercase text-slate-400 mb-1">Total Orders</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($shopOrderStats['total']) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ number_format($shopOrderStats['this_month']) }} this month</p>
            </div>
            <div class="pb-card p-4 border-l-2 border-l-sky-400">
                <p class="text-xs font-bold uppercase text-sky-600 mb-1">Received</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($shopOrderStats['order_received']) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Awaiting processing</p>
            </div>
            <div class="pb-card p-4 border-l-2 border-l-amber-400">
                <p class="text-xs font-bold uppercase text-amber-600 mb-1">Processing</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($shopOrderStats['processing']) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Being prepared</p>
            </div>
            <div class="pb-card p-4 border-l-2 border-l-violet-400">
                <p class="text-xs font-bold uppercase text-violet-600 mb-1">Dispatched</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($shopOrderStats['dispatched']) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">En route to customer</p>
            </div>
            <div class="pb-card p-4 border-l-2 border-l-emerald-400">
                <p class="text-xs font-bold uppercase text-emerald-600 mb-1">Delivered</p>
                <p class="text-2xl font-black text-slate-900">{{ number_format($shopOrderStats['delivered']) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Completed</p>
            </div>
        </div>

        {{-- Revenue row --}}
        <div class="grid sm:grid-cols-3 gap-3">
            <div class="pb-card p-4 bg-gradient-to-br from-pink-50 to-white">
                <p class="text-xs font-bold uppercase text-pink-600 mb-1">Shop Revenue (Month)</p>
                <p class="text-xl font-black text-slate-900">₦{{ number_format($shopOrderStats['revenue_month'], 0) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Paid orders this month</p>
            </div>
            <div class="pb-card p-4">
                <p class="text-xs font-bold uppercase text-slate-400 mb-1">Total Shop Revenue</p>
                <p class="text-xl font-black text-slate-900">₦{{ number_format($shopOrderStats['revenue_total'], 0) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">All time</p>
            </div>
            <div class="pb-card p-4 {{ $shopOrderStats['pending_dispatch'] > 0 ? 'border border-amber-200 bg-amber-50/50' : '' }}">
                <p class="text-xs font-bold uppercase {{ $shopOrderStats['pending_dispatch'] > 0 ? 'text-amber-600' : 'text-slate-400' }} mb-1">Needs Action</p>
                <p class="text-xl font-black {{ $shopOrderStats['pending_dispatch'] > 0 ? 'text-amber-700' : 'text-slate-900' }}">
                    {{ number_format($shopOrderStats['pending_dispatch']) }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">Paid but not yet dispatched</p>
            </div>
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════════
         TODAY'S TASKS
    ════════════════════════════════════════════════ --}}
    <section class="animate-fade-in-up delay-200 pb-card">
        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-4 w-1 rounded-full bg-amber-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Task Management</p>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Your tasks today</h2>
                <p class="mt-1 text-sm text-slate-500">Assigned tasks due for completion today.</p>
            </div>
            <a href="{{ route('admin.tasks.index') }}"
               class="pb-btn pb-btn-sm pb-btn-outline self-start text-xs">
                View All Tasks
            </a>
        </div>

        @if($todayTasks->isNotEmpty())
            @php
                $taskStatusColors = ['pending'=>'pb-badge-warning','working_on_it'=>'pb-badge-info','completed'=>'pb-badge-success','review_requested'=>'pb-badge-success','reviewed'=>'pb-badge-secondary','approved'=>'pb-badge-success','rejected'=>'pb-badge-danger'];
                $taskStatusLabels = ['pending'=>'Open','working_on_it'=>'In Progress','completed'=>'Done','review_requested'=>'Review','reviewed'=>'Reviewed','approved'=>'Approved','rejected'=>'Rejected'];
            @endphp
            <div class="grid gap-3 px-6 pb-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($todayTasks->take(3) as $todo)
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-slate-300 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-1.5">
                                <span class="pb-badge {{ $taskStatusColors[$todo->status] ?? 'pb-badge-secondary' }} text-[10px]">
                                    {{ $taskStatusLabels[$todo->status] ?? ucfirst(str_replace('_',' ',$todo->status)) }}
                                </span>
                            </div>
                            <span class="pb-badge {{ $todo->priority === 'high' ? 'pb-badge-danger' : ($todo->priority === 'medium' ? 'pb-badge-warning' : 'pb-badge-info') }} text-[10px]">
                                {{ ucfirst($todo->priority) }}
                            </span>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-slate-900 leading-snug">{{ $todo->task }}</h3>
                        <div class="mt-2.5 flex items-center justify-between gap-2">
                            <p class="text-xs text-slate-500">By {{ $todo->assigner?->displayName() ?? 'System' }}</p>
                            <span class="text-xs font-medium text-slate-600">Due {{ $todo->due_date->format('M j') }}</span>
                        </div>
                        @if($todo->order)
                            <p class="mt-2 text-xs font-medium text-brand-700">
                                Job: {{ $todo->order->job_order_number ?? $todo->order->displayNumber() }}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="pb-empty mx-6 mb-6">
                <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="pb-empty-title">All clear — no tasks today</p>
                <p class="pb-empty-body">Tasks assigned to you will appear here once created.</p>
            </div>
        @endif
    </section>

    {{-- ════════════════════════════════════════════════
         ORM — LIVE PRODUCTION QUEUE
    ════════════════════════════════════════════════ --}}
    <section class="animate-fade-in-up delay-200 pb-card">
        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-4 w-1 rounded-full bg-cyan-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ORM — Live Queue</p>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Production pipeline</h2>
                <p class="mt-1 text-sm text-slate-500">Active jobs moving through the production workbook.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="pb-btn pb-btn-sm pb-btn-outline self-start text-xs">
                View All Jobs
            </a>
        </div>

        <div class="table-scroll-container overflow-x-auto border-t border-slate-100">
            <table class="pb-table w-full min-w-[720px]">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Client</th>
                        <th class="hidden sm:table-cell">Product</th>
                        <th>Status</th>
                        <th class="hidden md:table-cell">Invoice</th>
                        <th class="hidden lg:table-cell">Phase</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900 text-sm">{{ $order->job_order_number ?? $order->displayNumber() }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('M j, Y') }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-slate-800 text-sm">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">{{ $order->customer_email }}</p>
                            </td>
                            <td class="hidden sm:table-cell">
                                <span class="text-sm text-slate-600">{{ $order->product?->name ?? 'Custom' }}</span>
                            </td>
                            <td>
                                <span class="pb-badge pb-badge-cyan text-[10px]">{{ $order->status }}</span>
                            </td>
                            <td class="hidden md:table-cell">
                                <span class="text-sm {{ $order->invoice ? 'text-slate-700 font-medium' : 'text-slate-400' }}">
                                    {{ $order->invoice?->invoice_number ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="hidden lg:table-cell">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    {{ $order->phase_approval_status ?? 'Awaiting approval' }}
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-xs font-semibold text-brand-700 hover:text-brand-900 transition-colors">
                                    Open →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="pb-empty border-0 bg-transparent py-6">
                                    <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="pb-empty-title">No active jobs</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════
         CRM — CUSTOMER & PRODUCT ANALYTICS
    ════════════════════════════════════════════════ --}}
    <section class="animate-fade-in-up delay-300 pb-card">
        <div class="p-6 pb-0">
            <div class="flex items-center gap-2 mb-1">
                <div class="h-4 w-1 rounded-full bg-violet-500"></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">CRM — Product Intelligence</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-bold text-slate-900">Product analytics</h2>
                <a href="{{ route('admin.products.index') }}" class="pb-btn pb-btn-sm pb-btn-outline text-xs self-start">
                    Manage Products
                </a>
            </div>
        </div>

        <div class="p-6 grid gap-5 lg:grid-cols-2">
            {{-- Views bar chart --}}
            @php $maxV = $productChartData->max('views') ?: 1; @endphp
            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                <p class="pb-stat-label mb-4">Top 10 products by views</p>
                <div class="space-y-3">
                    @forelse($productChartData as $item)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700 truncate max-w-[200px]">{{ $item['name'] }}</span>
                                <span class="font-bold text-brand-700 shrink-0 ml-2">{{ number_format($item['views']) }}</span>
                            </div>
                            <div class="pb-progress">
                                <div class="pb-progress-primary" style="width:{{ min(100, max(2, ($item['views'] / $maxV) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">No product views yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Category breakdown --}}
            @php $maxC = $productCategoryBreakdown->max('count') ?: 1; @endphp
            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                <p class="pb-stat-label mb-4">Products by category</p>
                <div class="space-y-3">
                    @forelse($productCategoryBreakdown as $cat)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700">{{ $cat['name'] }}</span>
                                <span class="font-bold text-cyan-700 shrink-0 ml-2">{{ $cat['count'] }}</span>
                            </div>
                            <div class="pb-progress">
                                <div class="pb-progress-info" style="width:{{ min(100, max(2, ($cat['count'] / $maxC) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">No categories yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Most viewed table --}}
        <div class="border-t border-slate-100">
            <table class="pb-table w-full min-w-[460px]">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Views</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mostViewedProducts as $product)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $product->name }}</td>
                            <td class="text-slate-500">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                            <td class="font-bold text-brand-700">{{ number_format((int)$product->view_count) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-sm text-slate-400">No views yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════
         ERM — STAFF PERFORMANCE
    ════════════════════════════════════════════════ --}}
    @if($admin->canAdmin('*') || $admin->canAdmin('staff.view'))
        <section class="animate-fade-in-up delay-300 pb-card">
            <div class="p-6 pb-0">
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-4 w-1 rounded-full bg-violet-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">ERM — People Intelligence</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Staff performance</h2>
                    <a href="{{ route('admin.staff.index') }}" class="pb-btn pb-btn-sm pb-btn-outline text-xs self-start">
                        Manage Staff
                    </a>
                </div>
            </div>

            <div class="p-6 grid gap-5 lg:grid-cols-2">
                {{-- Activity bar --}}
                @php $maxA = $staffChartData->max('activities') ?: 1; @endphp
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                    <p class="pb-stat-label mb-4">Staff activity — last 7 days</p>
                    <div class="space-y-3">
                        @forelse($staffChartData as $staff)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium text-slate-700 truncate max-w-[160px]">{{ $staff['name'] }}</span>
                                    <div class="flex items-center gap-2 shrink-0 ml-2">
                                        <span class="text-slate-400 text-[10px]">{{ $staff['role'] }}</span>
                                        <span class="font-bold text-violet-700">{{ $staff['activities'] }}</span>
                                    </div>
                                </div>
                                <div class="pb-progress">
                                    <div class="pb-progress-purple" style="width:{{ min(100, max(2, ($staff['activities'] / $maxA) * 100)) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-4">No activity data yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Weekly trend bars --}}
                @php
                    $maxD      = $weeklyStaffActivity->max('total') ?: 1;
                    $dayLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                @endphp
                <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                    <p class="pb-stat-label mb-4">Daily activity trend (7 days)</p>
                    <div class="flex items-end justify-between gap-1.5" style="min-height:100px">
                        @forelse($weeklyStaffActivity as $day)
                            <div class="flex flex-col items-center gap-1 flex-1">
                                <span class="text-[9px] font-bold text-violet-600">{{ $day['total'] }}</span>
                                <div class="w-full rounded-t overflow-hidden bg-violet-100" style="height:{{ max(8, ($day['total'] / $maxD) * 90) }}px">
                                    <div class="h-full w-full bg-gradient-to-t from-violet-600 to-violet-400"></div>
                                </div>
                                <span class="text-[9px] text-slate-400">{{ $dayLabels[\Carbon\Carbon::parse($day['date'])->dayOfWeekIso - 1] ?? '' }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 w-full text-center">No data this week.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Top staff table --}}
            <div class="border-t border-slate-100">
                <table class="pb-table w-full min-w-[460px]">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Activities (7 days)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topStaff as $s)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $s->displayName() }}</td>
                                <td class="text-slate-500">{{ config('printbuka_admin.role_labels.'.$s->role, $s->role) }}</td>
                                <td class="font-bold text-violet-700">{{ number_format((int)$s->staff_activities_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-400">No staff data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- ════════════════════════════════════════════════
         BOTTOM GRID: Role Menu + Workflow Phases
    ════════════════════════════════════════════════ --}}
    <div class="animate-fade-in-up delay-400 grid gap-5 xl:grid-cols-2">

        {{-- Admin menu card --}}
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-brand-600 to-pink-400"></div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="pb-section-title text-base">{{ $adminRoleLabel }}</p>
                </div>
                <p class="pb-section-subtitle mb-4">Your module access in this workspace.</p>
                <ul class="divide-y divide-slate-100 border-y border-slate-100">
                    @foreach($dashboardMenus as $menu)
                        <li class="py-2.5 text-sm font-medium text-slate-700">{{ $menu }}</li>
                    @endforeach
                </ul>
                @if($admin->canAdmin('staff.view'))
                    <div class="mt-4">
                        <a href="{{ route('admin.staff.index') }}"
                           class="pb-btn pb-btn-md pb-btn-outline text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Staff Access
                            @if(isset($pendingStaffCount) && $pendingStaffCount)
                                <span class="pb-badge pb-badge-danger ml-1 text-[10px]">{{ $pendingStaffCount }}</span>
                            @endif
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Workflow phases --}}
        <div class="pb-card overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-cyan-500 to-blue-500"></div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="pb-section-title text-base">Workbook phases</p>
                </div>
                <p class="pb-section-subtitle mb-4">Production workflow status overview.</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($workflowPhases as $phase)
                        <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 hover:border-slate-300 transition-colors">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <p class="text-sm font-semibold text-slate-900 leading-snug">{{ $phase['phase'] }}</p>
                                <span class="pb-badge {{ str_contains(strtolower((string)$phase['status']), 'complete') ? 'pb-badge-success' : 'pb-badge-warning' }} text-[10px] shrink-0">
                                    {{ $phase['status'] }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mb-3">{{ $phase['responsible'] }}</p>
                            <div class="pb-progress">
                                <div class="pb-progress-{{ str_contains(strtolower((string)$phase['status']), 'complete') ? 'success' : (str_contains(strtolower((string)$phase['status']), 'progress') ? 'primary' : 'warning') }}"
                                     style="width:{{ str_contains(strtolower((string)$phase['status']), 'complete') ? 100 : (str_contains(strtolower((string)$phase['status']), 'progress') ? 65 : 20) }}%"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
