@extends('layouts.admin')

@section('title', 'Manage Roles | Printbuka')

@section('content')
<div class="mx-auto max-w-[1200px] space-y-6">

    {{-- ════════ HERO ════════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-violet-600 via-violet-500 to-purple-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <span class="pb-badge pb-badge-purple">ERM — Role Management</span>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Manage <span class="text-violet-700">staff roles</span>
                </h1>
                <p class="text-sm text-slate-500 max-w-lg">
                    Create new roles with a custom permission set, or adjust what an existing role can access.
                    Changes apply to every staff member holding that role immediately.
                </p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="pb-btn pb-btn-md pb-btn-outline self-start text-sm">
                ← Staff
            </a>
        </div>

        @if(session('status'))
            <div class="pb-alert pb-alert-success mx-6 mb-6">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="pb-alert pb-alert-error mx-6 mb-6">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    {{-- ════════ CREATE ROLE ════════ --}}
    <section class="animate-fade-in-up delay-100 pb-card">
        <div class="pb-card-header border-b border-slate-100">
            <h3 class="pb-card-title">Create a new role</h3>
            <p class="pb-card-description">Pick a unique slug and the permissions this role should have.</p>
        </div>
        <form action="{{ route('admin.staff.roles.store') }}" method="POST" class="pb-card-content space-y-5 pt-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="pb-field">
                    <label class="pb-label" for="new-slug">Slug (unique, no spaces)</label>
                    <input id="new-slug" name="slug" value="{{ old('slug') }}" required
                           placeholder="e.g. video_editor" class="pb-input" pattern="[A-Za-z0-9_\-]+">
                </div>
                <div class="pb-field">
                    <label class="pb-label" for="new-label">Display label</label>
                    <input id="new-label" name="label" value="{{ old('label') }}" required
                           placeholder="e.g. Video Editor" class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label" for="new-priority">Priority (0–100)</label>
                    <input id="new-priority" type="number" min="0" max="100" name="priority" value="{{ old('priority', 20) }}" required class="pb-input">
                    <p class="text-xs text-slate-400 mt-1">Higher priority can act on lower-priority staff (e.g. evaluations, todo review).</p>
                </div>
            </div>

            <div class="pb-field">
                <label class="pb-label" for="new-dashboard-menu">Dashboard menu items (optional, comma-separated)</label>
                <input id="new-dashboard-menu" name="dashboard_menu" value="{{ old('dashboard_menu') }}"
                       placeholder="e.g. Job Briefs, Uploads, Today's Tasks" class="pb-input">
            </div>

            <div>
                <p class="pb-label mb-2">Permissions</p>
                @include('admin.staff._permission-checkboxes', ['selected' => old('permissions', [])])
            </div>

            <button type="submit" class="pb-btn pb-btn-md pb-btn-primary text-sm">Create role</button>
        </form>
    </section>

    {{-- ════════ EXISTING ROLES ════════ --}}
    <section class="animate-fade-in-up delay-200 space-y-4">
        @foreach($roles as $role)
            <div class="pb-card">
                <div class="pb-card-header border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="pb-card-title">{{ $role->label }}</h3>
                            <span class="pb-badge pb-badge-secondary text-[10px]">{{ $role->slug }}</span>
                            @if($role->is_system)
                                <span class="pb-badge pb-badge-warning text-[10px]">Built-in</span>
                            @endif
                            @if($role->hasWildcard())
                                <span class="pb-badge pb-badge-danger text-[10px]">Full access (*)</span>
                            @endif
                        </div>
                        <p class="pb-card-description">
                            {{ number_format($userCountsByRole[$role->slug] ?? 0) }} staff member(s) currently hold this role.
                        </p>
                    </div>
                    @if(! $role->is_system && ! $role->isProtected() && ($userCountsByRole[$role->slug] ?? 0) === 0)
                        <form action="{{ route('admin.staff.roles.destroy', $role) }}" method="POST"
                              onsubmit="return confirm('Delete the &quot;{{ $role->label }}&quot; role? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="pb-btn pb-btn-sm text-xs bg-red-600 text-white hover:bg-red-700">
                                Delete role
                            </button>
                        </form>
                    @endif
                </div>

                <form action="{{ route('admin.staff.roles.update', $role) }}" method="POST" class="pb-card-content space-y-5 pt-4">
                    @csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="pb-field">
                            <label class="pb-label">Display label</label>
                            <input name="label" value="{{ $role->label }}" required class="pb-input">
                        </div>
                        <div class="pb-field">
                            <label class="pb-label">Priority (0–100)</label>
                            <input type="number" min="0" max="100" name="priority" value="{{ $role->priority }}" required class="pb-input">
                        </div>
                    </div>

                    <div class="pb-field">
                        <label class="pb-label">Dashboard menu items (comma-separated)</label>
                        <input name="dashboard_menu" value="{{ implode(', ', $role->dashboard_menu ?? []) }}" class="pb-input">
                    </div>

                    @if($role->slug === 'super_admin')
                        <p class="text-xs text-slate-400">Process & Technology Manager always keeps full access (*) — permissions can't be edited for this role.</p>
                    @else
                        <div>
                            <p class="pb-label mb-2">Permissions</p>
                            @include('admin.staff._permission-checkboxes', ['selected' => $role->permissions ?? [], 'suffix' => $role->id])
                        </div>
                    @endif

                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-primary text-xs">Save changes</button>
                </form>
            </div>
        @endforeach
    </section>

</div>
@endsection
