@extends('layouts.admin')

@section('title', 'Staff Management (ERM) | Printbuka')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6">

    {{-- ════════ HERO ════════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-violet-600 via-violet-500 to-purple-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="pb-badge pb-badge-purple">ERM — People Management</span>
                    <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="pb-status-dot pb-status-pending"><span></span><span></span></span>
                        {{ number_format($staffStats['pending']) }} awaiting approval
                    </span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Staff <span class="text-violet-700">access & departments</span>
                </h1>
                <p class="text-sm text-slate-500 max-w-lg">
                    Approve registrations, manage roles and departments, track employment status, and monitor team performance.
                </p>
            </div>
            <div class="flex flex-col gap-2 self-start sm:flex-row">
                @if($canAssignRoles)
                    <a href="{{ route('admin.staff.roles.index') }}" class="pb-btn pb-btn-md pb-btn-primary text-sm">
                        Manage Roles
                    </a>
                @endif
                <a href="{{ route('admin.dashboard') }}" class="pb-btn pb-btn-md pb-btn-outline text-sm">
                    ← Dashboard
                </a>
            </div>
        </div>

        @if(session('status'))
            <div class="pb-alert pb-alert-success mx-6 mb-6">
                <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif
        @if($errors->has('photo'))
            <div class="pb-alert pb-alert-error mx-6 mb-6">{{ $errors->first('photo') }}</div>
        @endif
    </section>

    {{-- ════════ ERM KPI CARDS ════════ --}}
    <div class="animate-fade-in-up delay-100 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $kpis = [
                ['label'=>'Total Staff',  'value'=>$staffStats['total'],    'badge'=>'pb-badge-secondary', 'icon'=>'M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6', 'color'=>'text-slate-700', 'bar'=>'bg-slate-400'],
                ['label'=>'Active',       'value'=>$staffStats['active'],   'badge'=>'pb-badge-success',   'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                     'color'=>'text-emerald-700','bar'=>'bg-emerald-500'],
                ['label'=>'Pending',      'value'=>$staffStats['pending'],  'badge'=>'pb-badge-warning',   'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                       'color'=>'text-amber-700',  'bar'=>'bg-amber-500'],
                ['label'=>'Inactive',     'value'=>$staffStats['inactive'], 'badge'=>'pb-badge-danger',    'icon'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',                                                                                                                                                    'color'=>'text-red-700',    'bar'=>'bg-red-500'],
            ];
        @endphp
        @foreach($kpis as $kpi)
            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar {{ $kpi['bar'] }}"></div>
                <div class="flex items-start justify-between gap-3 mt-1">
                    <div>
                        <p class="pb-stat-label">{{ $kpi['label'] }}</p>
                        <p class="pb-stat-value {{ $kpi['color'] }} mt-2">{{ number_format($kpi['value']) }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $kpi['icon'] }}"/>
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    {{-- ════════ ROLE & DEPARTMENT BREAKDOWN ════════ --}}
    <div class="animate-fade-in-up delay-200 grid gap-5 xl:grid-cols-2">
        {{-- Role distribution --}}
        <div class="pb-card">
            <div class="pb-card-header border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-1 rounded-full bg-cyan-500"></div>
                    <h3 class="pb-card-title">Role distribution</h3>
                </div>
                <p class="pb-card-description">Headcount by assigned role</p>
            </div>
            <div class="pb-card-content space-y-3 pt-4">
                @forelse($roleCounts as $rc)
                    @php($pct = $staffStats['total'] > 0 ? min(100, ($rc->total / $staffStats['total']) * 100) : 0)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-slate-700">{{ $roles[$rc->role] ?? $rc->role }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format($rc->total) }}</span>
                        </div>
                        <div class="pb-progress">
                            <div class="pb-progress-info" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="pb-empty"><p class="pb-empty-title">No roles yet</p></div>
                @endforelse
            </div>
        </div>

        {{-- Department distribution --}}
        <div class="pb-card">
            <div class="pb-card-header border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-1 rounded-full bg-emerald-500"></div>
                    <h3 class="pb-card-title">Department distribution</h3>
                </div>
                <p class="pb-card-description">Headcount by department</p>
            </div>
            <div class="pb-card-content space-y-3 pt-4">
                @forelse($departmentCounts as $dc)
                    @php($pct = $staffStats['total'] > 0 ? min(100, ($dc->total / $staffStats['total']) * 100) : 0)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-slate-700">{{ $dc->department ?? 'Unassigned' }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format($dc->total) }}</span>
                        </div>
                        <div class="pb-progress">
                            <div class="pb-progress-success" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="pb-empty"><p class="pb-empty-title">No departments yet</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ════════ PENDING APPROVALS ════════ --}}
    <section class="animate-fade-in-up delay-200 pb-card overflow-hidden">
        <div class="border-b border-slate-100 p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-4 w-1 rounded-full bg-amber-500"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Approval</p>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Review staff registrations</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Confirm roles, assign departments, and activate accounts. {{ number_format($staffStats['pending']) }} waiting.
                    </p>
                </div>
            </div>
        </div>

        <livewire:admin.pending-staff-list :can-assign-roles="$canAssignRoles" :roles="$roles" />
    </section>

    {{-- ════════ ACTIVE STAFF DIRECTORY ════════ --}}
    <section class="animate-fade-in-up delay-300 pb-card overflow-hidden">
        <div class="border-b border-slate-100 p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="h-4 w-1 rounded-full bg-emerald-500"></div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Staff Directory</p>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Approved staff</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ number_format($staffStats['active']) }} active employees — manage roles, photos, and employment status.
                    </p>
                </div>
                @if($canSendKycReminders)
                    <form action="{{ route('admin.staff.kyc-reminders') }}" method="POST"
                          onsubmit="return confirm('Send a KYC bio-data reminder email to all {{ $staffStats['kycPending'] }} staff whose KYC has not been submitted or approved yet?')">
                        @csrf
                        <button type="submit" class="pb-btn pb-btn-md pb-btn-outline text-sm" @disabled($staffStats['kycPending'] === 0)>
                            Send KYC Reminders
                            @if($staffStats['kycPending'] > 0)
                                <span class="pb-badge pb-badge-warning ml-1 text-[10px]">{{ number_format($staffStats['kycPending']) }}</span>
                            @endif
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <livewire:admin.staff-list
            :can-assign-roles="$canAssignRoles"
            :can-manage-employment="$canManageEmployment"
            :can-manage-kyc="$canManageKyc"
            :roles="$roles"
        />
    </section>

</div>
@endsection
