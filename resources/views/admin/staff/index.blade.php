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
            <a href="{{ route('admin.dashboard') }}" class="pb-btn pb-btn-md pb-btn-outline self-start text-sm">
                ← Dashboard
            </a>
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

        <div class="divide-y divide-slate-100">
            @forelse($pendingStaff as $person)
                @if($canAssignRoles)
                    <form action="{{ route('admin.staff.update', $person) }}" method="POST"
                          enctype="multipart/form-data"
                          class="p-6 grid gap-5 lg:grid-cols-[1fr_200px_200px_auto] lg:items-end">
                        @csrf @method('PUT')
                @else
                    <div class="p-6 grid gap-5 lg:grid-cols-[1fr_200px_200px_auto] lg:items-end">
                @endif

                    {{-- Staff info + photo upload --}}
                    <div>
                        <div class="flex items-start gap-4">
                            @if($person->profilePhotoUrl())
                                <img src="{{ $person->profilePhotoUrl() }}" alt="{{ $person->displayName() }}"
                                     class="h-14 w-14 rounded-full border-2 border-slate-200 object-cover shrink-0">
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full
                                            border-2 border-slate-200 bg-violet-100 text-sm font-bold text-violet-800">
                                    {{ $person->profileInitials() }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-slate-900">{{ $person->displayName() }}</p>
                                <p class="text-sm text-slate-500">{{ $person->email }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $person->phone }}</p>
                                <span class="pb-badge pb-badge-warning mt-2 text-[10px]">
                                    Requested: {{ $roles[$person->requested_role] ?? $person->requested_role ?? 'N/A' }}
                                    {{ $person->other_role ? '· '.$person->other_role : '' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="pb-label">Profile photo (optional)</p>
                            <livewire:uploads.secure-image-upload
                                :key="'pending-photo-'.$person->id"
                                input-name="photo_upload_path"
                                directory="staff-photos"
                                :max-size-kb="2048"
                                :multiple="false"
                            />
                        </div>
                    </div>

                    {{-- Role select --}}
                    <div class="pb-field">
                        <label class="pb-label" for="role-{{ $person->id }}">Final role</label>
                        <select id="role-{{ $person->id }}" name="role" @disabled(!$canAssignRoles)
                            class="pb-select @disabled(!$canAssignRoles) disabled:opacity-60">
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $person->requested_role) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2">
                        @if($canAssignRoles)
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 cursor-pointer hover:border-emerald-300 transition">
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Approve
                            </label>
                            <button type="submit" class="pb-btn pb-btn-md pb-btn-primary text-sm">
                                Save & Activate
                            </button>
                        @else
                            <span class="pb-badge pb-badge-secondary">Super Admin assigns role</span>
                        @endif
                    </div>

                @if($canAssignRoles)
                    </form>
                @else
                    </div>
                @endif
            @empty
                <div class="pb-empty m-6">
                    <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="pb-empty-title">No pending registrations</p>
                    <p class="pb-empty-body">All staff registrations have been reviewed.</p>
                </div>
            @endforelse
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $pendingStaff->links() }}</div>
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
            </div>
        </div>

        <div class="table-scroll-container overflow-x-auto">
            <table class="pb-table w-full min-w-[1200px]">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Employment</th>
                        <th>Approved</th>
                        <th>Profile Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $person)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($person->profilePhotoUrl())
                                        <img src="{{ $person->profilePhotoUrl() }}" alt="{{ $person->displayName() }}"
                                             class="h-10 w-10 rounded-full border border-slate-200 object-cover shrink-0">
                                    @else
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                                                    bg-violet-100 text-xs font-bold text-violet-800">
                                            {{ $person->profileInitials() }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900 text-sm">{{ $person->displayName() }}</p>
                                        <p class="text-xs text-slate-400">{{ $person->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm font-medium text-slate-700">{{ $roles[$person->role] ?? $person->role }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-slate-600">{{ $person->department ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="pb-badge {{ $person->is_active ? 'pb-badge-success' : 'pb-badge-danger' }} text-[10px]">
                                    {{ $person->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($person->access_restricted)
                                    <span class="pb-badge pb-badge-danger text-[10px] mt-1 block">Access Restricted</span>
                                @endif
                            </td>
                            <td>
                                <span class="pb-badge {{ match($person->employment_status ?? 'active') { 'active'=>'pb-badge-success','suspended'=>'pb-badge-warning','terminated'=>'pb-badge-danger', default=>'pb-badge-secondary' } }} text-[10px]">{{ $person->employmentStatusLabel() }}</span>
                                @if($person->employment_status_changed_at)
                                    <p class="text-[10px] text-slate-400 mt-1">{{ $person->employment_status_changed_at->format('M j, Y') }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs text-slate-500">{{ $person->approved_at?->format('M j, Y') ?? '—' }}</span>
                            </td>
                            <td>
                                @if($canAssignRoles)
                                    <form action="{{ route('admin.staff.update', $person) }}" method="POST"
                                          enctype="multipart/form-data"
                                          class="flex items-start gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="role" value="{{ $person->role }}">
                                        <input type="hidden" name="department" value="{{ $person->department }}">
                                        <input type="hidden" name="is_active" value="{{ $person->is_active ? 1 : 0 }}">
                                        <div class="min-w-[180px]">
                                            <livewire:uploads.secure-image-upload
                                                :key="'staff-photo-'.$person->id"
                                                input-name="photo_upload_path"
                                                directory="staff-photos"
                                                :max-size-kb="2048"
                                                :multiple="false"
                                            />
                                        </div>
                                        <button type="submit" class="pb-btn pb-btn-sm pb-btn-primary text-xs shrink-0">
                                            Upload
                                        </button>
                                    </form>
                                @else
                                    <span class="pb-badge pb-badge-secondary text-[10px]">Super Admin only</span>
                                @endif
                            </td>
                            <td>
                                <div class="space-y-2 min-w-[200px]">
                                    @if($canManageEmployment)
                                        <form action="{{ route('admin.staff.employment-status', $person) }}" method="POST"
                                              class="space-y-2">
                                            @csrf @method('PATCH')
                                            <select name="employment_status" class="pb-select text-xs h-9 py-0">
                                                <option value="active"     @selected(($person->employment_status ?? 'active') === 'active')>Onboard / Active</option>
                                                <option value="suspended"  @selected(($person->employment_status ?? '') === 'suspended')>Suspend</option>
                                                <option value="terminated" @selected(($person->employment_status ?? '') === 'terminated')>Terminate</option>
                                            </select>
                                            <input name="employment_status_reason" value="{{ $person->employment_status_reason }}"
                                                   class="pb-input text-xs h-9 py-0" placeholder="Reason (optional)">
                                            <button type="submit" class="pb-btn pb-btn-sm pb-btn-ink text-xs w-full">Apply</button>
                                        </form>
                                    @else
                                        <span class="pb-badge pb-badge-secondary text-[10px]">HR / Super Admin</span>
                                    @endif

                                    @if(auth()->user()?->role === 'super_admin' && $person->role !== 'super_admin')
                                        <form action="{{ route('admin.staff.access-restriction', $person) }}" method="POST"
                                              onsubmit="return confirm('{{ $person->access_restricted ? 'Restore access for '.$person->displayName().'?' : 'Restrict access for '.$person->displayName().'? They will be logged out immediately.' }}')">
                                            @csrf @method('PATCH')
                                            @if (!$person->access_restricted)
                                                <input name="reason" class="pb-input text-xs h-9 py-0 w-full" placeholder="Reason (optional)">
                                            @endif
                                            <button type="submit"
                                                class="pb-btn pb-btn-sm text-xs w-full mt-1 {{ $person->access_restricted ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                                                {{ $person->access_restricted ? '✓ Restore Access' : '⊘ Restrict Access' }}
                                            </button>
                                        </form>
                                        @if ($person->access_restricted)
                                            <p class="text-[10px] text-red-600 font-semibold">
                                                Restricted {{ $person->access_restricted_at?->diffForHumans() }}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="pb-empty border-0 bg-transparent">
                                    <p class="pb-empty-title">No approved staff yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">{{ $staff->links() }}</div>
    </section>

</div>
@endsection
