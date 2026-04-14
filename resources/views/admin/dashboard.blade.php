@extends('layouts.admin')

@section('title', 'Admin Dashboard | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <!-- Hero Section -->
        <section class="fade-in-up rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white via-white to-pink-50/30 p-8 shadow-sm backdrop-blur-sm">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-pink-100 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-pink-700">
                            {{ $adminRoleLabel }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Live
                        </span>
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-950 lg:text-6xl">
                        {{ $adminRoleLabel }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-700 to-pink-500">Dashboard</span>
                    </h1>
                    <p class="max-w-3xl text-base leading-relaxed text-slate-600">
                        Finance, live job statistics, ongoing production jobs, staff performance, and minimal live activity in one clean workspace.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if (auth()->user()->canAdmin('orders.create'))
                        <a href="{{ route('admin.orders.create') }}" class="btn-primary group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30 hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Job
                        </a>
                    @endif
                    <a href="{{ route('admin.orders.index') }}" class="group relative inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 bg-white/80 backdrop-blur-sm px-6 py-3.5 text-sm font-black text-slate-800 transition-all duration-300 hover:border-pink-300 hover:text-pink-700 hover:shadow-lg">
                        <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        Open Job Tracker
                    </a>
                </div>
            </div>
        </section>

        <!-- Real-time Stats -->
        <div class="fade-in-up section-delay-1">
            <livewire:admin.realtime-stats />
        </div>

        <!-- Ongoing Jobs Table -->
        <section class="fade-in-up section-delay-2 card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-50 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-cyan-700">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                            </span>
                            Live Queue
                        </span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-950">Live production queue</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Detailed and extensive view of active work moving through the workbook process.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-3 text-sm font-black text-slate-800 transition-all duration-300 hover:border-pink-300 hover:text-pink-700 hover:bg-pink-50/50">
                    View All Jobs
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <div class="mt-6 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/50">
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Job</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Client</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Product</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Invoice</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Phase Approval</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentOrders as $order)
                            <tr class="table-row-hover group">
                                <td class="px-5 py-4">
                                    <p class="font-black text-slate-950">{{ $order->job_order_number ?? $order->displayNumber() }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $order->created_at->format('M j, Y') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-black text-slate-900">{{ $order->customer_name }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $order->customer_email }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-700">{{ $order->product?->name ?? 'Custom order' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="status-badge bg-cyan-50 text-cyan-800">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-700">{{ $order->invoice?->invoice_number ?? 'Pending' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        {{ $order->phase_approval_status ?? 'Pending Operations Approval' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="group inline-flex items-center gap-1 font-black text-pink-700 transition-all duration-300 hover:text-pink-800">
                                        Open
                                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="text-slate-500 font-semibold">No active jobs yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Staff Activity Feed -->
        @if (auth()->user()->canAdmin('*') || auth()->user()->canAdmin('staff.view'))
            <div class="fade-in-up section-delay-3">
                <livewire:admin.staff-activity-feed />
            </div>
        @endif

        <!-- Bottom Grid Sections -->
        <section class="fade-in-up section-delay-4 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <!-- Admin Menu Card -->
            <div class="card-hover rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-pink-50/20 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="text-sm font-black uppercase tracking-wider text-pink-700">Admin Related Menu</p>
                </div>
                <h2 class="text-3xl font-black text-slate-950 mb-5">{{ $adminRoleLabel }}</h2>
                <div class="divide-y divide-slate-100 border-y border-slate-100">
                    @foreach ($dashboardMenus as $menu)
                        <p class="py-3.5 text-sm font-black text-slate-800 hover:text-pink-700 transition-colors cursor-default">{{ $menu }}</p>
                    @endforeach
                </div>
                @if (auth()->user()->canAdmin('staff.view'))
                    <a href="{{ route('admin.staff.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-800 transition-all duration-300 hover:border-pink-300 hover:text-pink-700 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Staff Access
                        @if($pendingStaffCount)
                            <span class="ml-2 rounded-full bg-pink-100 px-2 py-0.5 text-xs font-black text-pink-700">{{ $pendingStaffCount }}</span>
                        @endif
                    </a>
                @endif
            </div>

            <!-- Workflow Phases Card -->
            <div class="card-hover rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white to-cyan-50/20 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Workflow</p>
                </div>
                <h2 class="text-3xl font-black text-slate-950 mb-5">Workbook phases</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($workflowPhases as $phase)
                        <article class="group rounded-xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:shadow-md hover:border-cyan-200">
                            <div class="flex items-start justify-between mb-3">
                                <p class="font-black text-slate-950 group-hover:text-cyan-700 transition-colors">{{ $phase['phase'] }}</p>
                                <span class="rounded-full bg-gradient-to-r {{ str_contains(strtolower($phase['status']), 'complete') ? 'from-emerald-100 to-emerald-200 text-emerald-800' : 'from-amber-100 to-amber-200 text-amber-800' }} px-2.5 py-1 text-[0.6rem] font-black uppercase tracking-wider">
                                    {{ $phase['status'] }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-slate-600 mb-2">{{ $phase['responsible'] }}</p>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-full transition-all duration-500"
                                     style="width: {{ str_contains(strtolower($phase['status']), 'complete') ? '100%' : (str_contains(strtolower($phase['status']), 'progress') ? '65%' : '25%') }}"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
