@extends('layouts.theme')

@section('title', 'Admin Dashboard | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-cyan-300">Printbuka Admin</p>
                <h1 class="mt-2 text-4xl">Production workflow command center.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Frontend orders flow into this dashboard as production jobs. Staff permissions follow the SOP workbook phases: intake, design, production, QC, delivery, review and management verification.</p>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Orders</p>
                    <p class="mt-3 text-5xl font-black">{{ $orderCount }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Active Jobs</p>
                    <p class="mt-3 text-5xl font-black">{{ $activeJobs }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Delivered</p>
                    <p class="mt-3 text-5xl font-black">{{ $deliveredJobs }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-black uppercase tracking-wide text-amber-700">Staff</p>
                    <p class="mt-3 text-5xl font-black">{{ $staffCount }}</p>
                </div>
            </div>

            <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Recent Workflow Jobs</p>
                        <h2 class="mt-2 text-3xl text-slate-950">Frontend orders needing action.</h2>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Open Job Tracker</a>
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
