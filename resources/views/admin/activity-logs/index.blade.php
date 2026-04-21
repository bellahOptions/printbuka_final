@extends('layouts.admin')

@section('title', 'Admin Audit Logs | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Central Logging</p>
                <h1 class="mt-2 text-4xl text-slate-950">Admin audit logs.</h1>
                <p class="mt-2 text-sm font-semibold text-slate-500">Investigative timeline for all admin actions across the panel.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mt-6 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="lg:col-span-2">
                    <span class="text-xs font-black uppercase tracking-wide text-slate-500">Search</span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Action, route, URL, user..."
                        class="mt-1 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold"
                    >
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-wide text-slate-500">Role</span>
                    <select name="role" class="mt-1 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        <option value="">All roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption }}" @selected($role === $roleOption)>
                                {{ config('printbuka_admin.role_labels.'.$roleOption, $roleOption) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span class="text-xs font-black uppercase tracking-wide text-slate-500">Route</span>
                    <select name="route_name" class="mt-1 h-11 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                        <option value="">All routes</option>
                        @foreach ($routeNames as $routeOption)
                            <option value="{{ $routeOption }}" @selected($routeName === $routeOption)>
                                {{ $routeOption }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="mt-3 flex gap-2">
                <button class="h-10 rounded-md bg-slate-900 px-4 text-sm font-black text-white transition hover:bg-pink-700">Filter</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-black text-slate-700 transition hover:border-pink-300 hover:text-pink-700">Reset</a>
            </div>
        </form>

        <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[1220px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">When</th>
                        <th class="px-5 py-4">Admin</th>
                        <th class="px-5 py-4">Action</th>
                        <th class="px-5 py-4">Route</th>
                        <th class="px-5 py-4">Method</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Subject</th>
                        <th class="px-5 py-4">IP</th>
                        <th class="px-5 py-4">Context</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-900">{{ $log->created_at->format('M j, Y') }}</p>
                                <p class="text-xs font-semibold text-slate-500">{{ $log->created_at->format('H:i:s') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-900">{{ $log->user?->displayName() ?? 'Unknown' }}</p>
                                <p class="text-xs font-semibold text-slate-500">{{ $log->user?->email ?? 'No email' }}</p>
                                <p class="text-xs font-semibold text-slate-500">{{ config('printbuka_admin.role_labels.'.$log->role, $log->role) }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $log->action }}</td>
                            <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ $log->route_name ?? 'n/a' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-black {{ $log->method === 'DELETE' ? 'bg-red-100 text-red-700' : ($log->method === 'POST' || $log->method === 'PUT' || $log->method === 'PATCH' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-black">{{ $log->status_code ?? '—' }}</td>
                            <td class="px-5 py-4 text-xs font-semibold text-slate-600">
                                {{ $log->subject_type ?? 'n/a' }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}
                            </td>
                            <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ $log->ip_address ?? 'n/a' }}</td>
                            <td class="px-5 py-4">
                                <details>
                                    <summary class="cursor-pointer text-xs font-black text-pink-700 hover:text-pink-900">View</summary>
                                    <pre class="mt-2 max-h-48 overflow-auto rounded-md bg-slate-900 p-3 text-[11px] font-semibold text-slate-100">{{ json_encode($log->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-500">No logs found for this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $logs->links() }}</div>
    </div>
@endsection
