@extends('layouts.admin')

@section('title', 'Staff Access | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="fade-in-up rounded-2xl border border-slate-200/60 bg-gradient-to-br from-white via-white to-pink-50/30 p-8 shadow-sm backdrop-blur-sm">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-pink-100 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-pink-700">
                            Staff Management
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ number_format($staffStats['pending']) }} pending approval
                        </span>
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-950 lg:text-6xl">
                        Staff access <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-700 to-pink-500">and departments</span>
                    </h1>
                    <p class="max-w-3xl text-base leading-relaxed text-slate-600">
                        Approve registrations, assign departments, and keep the active team distribution visible from the admin workspace.
                    </p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 bg-white/80 px-6 py-3.5 text-sm font-black text-slate-800 shadow-sm transition-all duration-300 hover:border-pink-300 hover:text-pink-700 hover:shadow-lg">
                    Admin Dashboard
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            @if (session('status'))
                <p class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
            @endif

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([['label' => 'Total Staff', 'value' => $staffStats['total'], 'valueClass' => 'text-slate-950', 'dotClass' => 'bg-slate-400'], ['label' => 'Active', 'value' => $staffStats['active'], 'valueClass' => 'text-emerald-800', 'dotClass' => 'bg-emerald-500'], ['label' => 'Pending', 'value' => $staffStats['pending'], 'valueClass' => 'text-amber-800', 'dotClass' => 'bg-amber-500'], ['label' => 'Inactive', 'value' => $staffStats['inactive'], 'valueClass' => 'text-pink-800', 'dotClass' => 'bg-pink-500']] as $card)
                    <article class="card-hover rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ $card['label'] }}</p>
                            <span class="h-2 w-2 rounded-full {{ $card['dotClass'] }}"></span>
                        </div>
                        <p class="mt-4 text-4xl font-black leading-none {{ $card['valueClass'] }}">{{ number_format($card['value']) }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="fade-in-up section-delay-1 grid gap-6 xl:grid-cols-2">
            <div class="card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    <p class="text-sm font-black uppercase tracking-wider text-cyan-700">Role Chart</p>
                </div>
                <div class="space-y-4">
                    @forelse ($roleCounts as $roleCount)
                        @php($percentage = $staffStats['total'] > 0 ? min(100, ($roleCount->total / $staffStats['total']) * 100) : 0)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex justify-between gap-4 text-sm font-black">
                                <span class="text-slate-800">{{ $roles[$roleCount->role] ?? $roleCount->role }}</span>
                                <span class="text-slate-950">{{ number_format($roleCount->total) }}</span>
                            </div>
                            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-cyan-600" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500">No staff roles available.</p>
                    @endforelse
                </div>
            </div>

            <div class="card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-sm font-black uppercase tracking-wider text-emerald-700">Department Chart</p>
                </div>
                <div class="space-y-4">
                    @forelse ($departmentCounts as $departmentCount)
                        @php($percentage = $staffStats['total'] > 0 ? min(100, ($departmentCount->total / $staffStats['total']) * 100) : 0)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex justify-between gap-4 text-sm font-black">
                                <span class="text-slate-800">{{ $departmentCount->department ?? 'Unassigned' }}</span>
                                <span class="text-slate-950">{{ number_format($departmentCount->total) }}</span>
                            </div>
                            <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-white">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500">No departments assigned yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="fade-in-up section-delay-2 card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-amber-700">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            Pending Approval
                        </span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-950">Review staff registrations</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Confirm final roles, choose departments, and activate approved staff accounts.</p>
                </div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ number_format($staffStats['pending']) }} waiting</p>
            </div>

            <div class="mt-6 space-y-5">
                @forelse ($pendingStaff as $person)
                    <form action="{{ route('admin.staff.update', $person) }}" method="POST" class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50/70 p-5 xl:grid-cols-[1fr_0.8fr_0.8fr_auto] xl:items-end">
                        @csrf
                        @method('PUT')
                        <div>
                            <p class="font-black text-slate-950">{{ $person->displayName() }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $person->email }} · {{ $person->phone }}</p>
                            <p class="mt-2 text-xs font-black uppercase tracking-wider text-amber-700">
                                Requested: {{ $roles[$person->requested_role] ?? $person->requested_role ?? 'Pending' }} {{ $person->other_role ? '· '.$person->other_role : '' }}
                            </p>
                        </div>
                        <label class="text-sm font-black text-slate-800">
                            Final Role
                            <select name="role" class="mt-2 min-h-12 w-full rounded-xl border border-slate-200 bg-white px-4 font-semibold text-slate-950 outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role', $person->requested_role) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-black text-slate-800">
                            Department
                            <select name="department" class="mt-2 min-h-12 w-full rounded-xl border border-slate-200 bg-white px-4 font-semibold text-slate-950 outline-none transition focus:border-pink-500 focus:ring-4 focus:ring-pink-100">
                                @foreach ($departments as $value => $label)
                                    <option value="{{ $label }}" @selected(old('department', $person->department) === $label)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-800">
                                <input type="checkbox" name="is_active" value="1" checked class="h-5 w-5 rounded border-slate-300 text-pink-600">
                                Approve
                            </label>
                            <button class="btn-primary rounded-xl bg-gradient-to-r from-pink-600 to-pink-700 px-5 py-3 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition-all duration-300 hover:shadow-xl hover:shadow-pink-600/30">Save</button>
                        </div>
                    </form>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-6 text-sm font-semibold text-slate-500">No staff registrations are pending.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $pendingStaff->links() }}</div>
        </section>

        <section class="fade-in-up section-delay-3 card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-50 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-cyan-700">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Active Staff
                        </span>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-950">Approved staff directory</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Current staff access status, role assignments, and approval dates.</p>
                </div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ number_format($staffStats['active']) }} active</p>
            </div>

            <div class="mt-6 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/50">
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Staff</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Role</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Department</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">Approved</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($staff as $person)
                            <tr class="table-row-hover">
                                <td class="px-5 py-4">
                                    <span class="block font-black text-slate-950">{{ $person->displayName() }}</span>
                                    <span class="text-xs font-semibold text-slate-500">{{ $person->email }}</span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-700">{{ $roles[$person->role] ?? $person->role }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-700">{{ $person->department ?? 'Pending' }}</td>
                                <td class="px-5 py-4">
                                    <span class="status-badge {{ $person->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-pink-50 text-pink-700' }}">
                                        {{ $person->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-500">{{ $person->approved_at?->format('M j, Y') ?? 'Pending' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <p class="text-sm font-semibold text-slate-500">No approved staff records yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $staff->links() }}</div>
        </section>
    </div>
@endsection
