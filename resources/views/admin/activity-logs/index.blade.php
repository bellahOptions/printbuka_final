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

        <div class="mt-8">
            <livewire:admin.activity-logs-table :filters="['search' => $search, 'role' => $role, 'route_name' => $routeName]" />
        </div>
    </div>
@endsection
