@extends('layouts.admin')

@section('title', 'Admin Audit Logs | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="pb-page-header">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Central Logging</p>
                <h1 class="pb-page-title">Admin audit logs</h1>
                <p class="pb-page-subtitle">Investigative timeline for all admin actions across the panel.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="pb-card p-4">
            <div class="grid gap-3 lg:grid-cols-4">
                <div class="pb-field lg:col-span-2">
                    <label class="pb-label">Search</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Action, route, URL, user..."
                        class="pb-input w-full"
                    >
                </div>
                <div class="pb-field">
                    <label class="pb-label">Role</label>
                    <select name="role" class="pb-select w-full">
                        <option value="">All roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption }}" @selected($role === $roleOption)>
                                {{ config('printbuka_admin.role_labels.'.$roleOption, $roleOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pb-field">
                    <label class="pb-label">Route</label>
                    <select name="route_name" class="pb-select w-full">
                        <option value="">All routes</option>
                        @foreach ($routeNames as $routeOption)
                            <option value="{{ $routeOption }}" @selected($routeName === $routeOption)>
                                {{ $routeOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <button class="pb-btn pb-btn-md pb-btn-primary">Filter</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="pb-btn pb-btn-md pb-btn-outline">Reset</a>
            </div>
        </form>

        <div class="mt-8">
            <livewire:admin.activity-logs-table :filters="['search' => $search, 'role' => $role, 'route_name' => $routeName]" />
        </div>
    </div>
@endsection
