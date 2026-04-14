@extends('layouts.theme')

@section('title', 'Admin Dashboard | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Printbuka Admin</p>
                <h1 class="mt-2 text-4xl">{{ $adminRoleLabel }} dashboard.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Your menu and job actions are limited by your assigned staff role and department approval.</p>
            </div>

            <livewire:admin.notifications />
            <livewire:admin.realtime-stats />

            @if (auth()->user()->canAdmin('*') || auth()->user()->canAdmin('staff.view'))
                <livewire:admin.staff-activity-feed />
            @endif

            @if (auth()->user()->canAdmin('*') || auth()->user()->canAdmin('finance.view'))
                <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Weekly Profit Snapshot</p>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-black uppercase tracking-wide text-slate-500">
                                    <th class="py-3">Week</th>
                                    <th class="py-3">Revenue</th>
                                    <th class="py-3">Expenses</th>
                                    <th class="py-3">Profit</th>
                                    <th class="py-3">Margin</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($weeklyProfitSnapshot as $snapshot)
                                    <tr>
                                        <td class="py-4 font-black">{{ $snapshot['week'] }}</td>
                                        <td class="py-4">NGN {{ number_format($snapshot['revenue'], 2) }}</td>
                                        <td class="py-4">NGN {{ number_format($snapshot['expenses'], 2) }}</td>
                                        <td class="py-4">NGN {{ number_format($snapshot['profit'], 2) }}</td>
                                        <td class="py-4">{{ number_format($snapshot['margin'], 1) }}%</td>
                                        <td class="py-4">{{ $snapshot['status'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Your Menu</p>
                        <h2 class="mt-2 text-3xl text-slate-950">{{ $adminRoleLabel }}</h2>
                    </div>
                    @if (auth()->user()->canAdmin('staff.view'))
                        <a href="{{ route('admin.staff.index') }}" class="rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Staff Access {{ $pendingStaffCount ? '('.$pendingStaffCount.')' : '' }}</a>
                    @endif
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($dashboardMenus as $menu)
                        <div class="rounded-md border border-slate-200 p-4 font-black text-slate-800">{{ $menu }}</div>
                    @endforeach
                </div>
            </section>

            @if (auth()->user()->canAdmin('*'))
                <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Admin Modules</p>
                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <a href="{{ route('admin.orders.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Job Management</a>
                        <a href="{{ route('admin.blog.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Blog Management</a>
                        <a href="{{ route('admin.invoices.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Invoice Management</a>
                        <a href="{{ route('admin.finance.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Finance</a>
                        <a href="{{ route('admin.products.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Product Management</a>
                        <a href="{{ route('admin.product-categories.index') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Product Category Management</a>
                        <a href="{{ route('admin.settings.edit') }}" class="rounded-md border border-slate-200 p-4 font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Site Settings</a>
                    </div>
                </section>
            @endif

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Workbook Workflow</p>
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($workflowPhases as $phase)
                        <div class="rounded-md border border-slate-200 p-4">
                            <p class="font-black text-slate-950">{{ $phase['phase'] }}</p>
                            <p class="mt-2 text-sm font-bold text-slate-600">{{ $phase['responsible'] }}</p>
                            <p class="mt-2 text-xs font-black uppercase tracking-wide text-pink-700">{{ $phase['status'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Recent Workflow Jobs</p>
                        <h2 class="mt-2 text-3xl text-slate-950">Frontend orders needing action.</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if (auth()->user()->canAdmin('orders.create'))
                            <a href="{{ route('admin.orders.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Create Job</a>
                        @endif
                        <a href="{{ route('admin.orders.index') }}" class="rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Open Job Tracker</a>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-black uppercase tracking-wide text-slate-500">
                                <th class="py-3">Job</th>
                                <th class="py-3">Client</th>
                                <th class="py-3">Product</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Invoice</th>
                                <th class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="py-4 font-black">{{ $order->job_order_number ?? $order->displayNumber() }}</td>
                                    <td class="py-4">{{ $order->customer_name }}</td>
                                    <td class="py-4">{{ $order->product?->name ?? 'Custom order' }}</td>
                                    <td class="py-4">{{ $order->status }}</td>
                                    <td class="py-4">{{ $order->invoice?->invoice_number ?? 'Pending' }}</td>
                                    <td class="py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="font-black text-pink-700 hover:text-pink-800">Open</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </main>
@endsection
