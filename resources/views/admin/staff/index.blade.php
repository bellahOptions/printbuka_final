@extends('layouts.theme')

@section('title', 'Staff Access | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-black text-cyan-300 hover:text-cyan-200">Admin Dashboard</a>
                <h1 class="mt-3 text-4xl">Staff access.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Approve staff registrations, assign departments, and control dashboard access.</p>
            </div>

            @if (session('status'))
                <p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
            @endif

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Pending Approval</p>
                <div class="mt-6 space-y-5">
                    @forelse ($pendingStaff as $person)
                        <form action="{{ route('admin.staff.update', $person) }}" method="POST" class="grid gap-4 rounded-md border border-slate-200 p-4 lg:grid-cols-[1fr_0.8fr_0.8fr_auto] lg:items-end">
                            @csrf
                            @method('PUT')
                            <div>
                                <p class="font-black text-slate-950">{{ $person->displayName() }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-500">{{ $person->email }} · {{ $person->phone }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-500">Requested: {{ $roles[$person->requested_role] ?? $person->requested_role ?? 'Pending' }} {{ $person->other_role ? '· '.$person->other_role : '' }}</p>
                            </div>
                            <label class="text-sm font-black">Final Role<select name="role" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($roles as $value => $label)<option value="{{ $value }}" @selected(old('role', $person->requested_role) === $value)>{{ $label }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Department<select name="department" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($departments as $value => $label)<option value="{{ $label }}" @selected(old('department', $person->department) === $label)>{{ $label }}</option>@endforeach</select></label>
                            <label class="flex min-h-12 items-center gap-3 rounded-md border border-slate-200 px-4 text-sm font-black">
                                <input type="checkbox" name="is_active" value="1" checked class="h-5 w-5 rounded border-slate-300 text-pink-600">
                                Approve
                            </label>
                            <button class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700 lg:col-start-4">Save Access</button>
                        </form>
                    @empty
                        <p class="text-sm font-semibold text-slate-500">No staff registrations are pending.</p>
                    @endforelse
                </div>
            </section>

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Active Staff</p>
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-black uppercase tracking-wide text-slate-500">
                                <th class="py-3">Staff</th>
                                <th class="py-3">Role</th>
                                <th class="py-3">Department</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($staff as $person)
                                <tr>
                                    <td class="py-4">
                                        <span class="block font-black">{{ $person->displayName() }}</span>
                                        <span class="text-xs font-semibold text-slate-500">{{ $person->email }}</span>
                                    </td>
                                    <td class="py-4">{{ $roles[$person->role] ?? $person->role }}</td>
                                    <td class="py-4">{{ $person->department ?? 'Pending' }}</td>
                                    <td class="py-4">{{ $person->is_active ? 'Active' : 'Inactive' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </main>
@endsection
