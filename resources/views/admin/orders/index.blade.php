@extends('layouts.theme')

@section('title', 'Admin Job Tracker | Printbuka')

@section('content')
    <main class="bg-slate-50 py-12 text-slate-900">
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Production Job Tracker</p>
                    <h1 class="mt-2 text-4xl text-slate-950">All frontend orders.</h1>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Admin Dashboard</a>
            </div>

            <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-4">Job Order</th>
                            <th class="px-5 py-4">Invoice</th>
                            <th class="px-5 py-4">Client</th>
                            <th class="px-5 py-4">Job Type</th>
                            <th class="px-5 py-4">Priority</th>
                            <th class="px-5 py-4">Payment</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-5 py-4 font-black">{{ $order->job_order_number ?? $order->displayNumber() }}</td>
                                <td class="px-5 py-4">{{ $order->invoice?->invoice_number ?? 'Pending' }}</td>
                                <td class="px-5 py-4">
                                    <span class="block font-bold">{{ $order->customer_name }}</span>
                                    <span class="text-xs text-slate-500">{{ $order->customer_email }}</span>
                                </td>
                                <td class="px-5 py-4">{{ $order->product?->name ?? ucfirst($order->service_type) }}</td>
                                <td class="px-5 py-4">{{ $order->priority }}</td>
                                <td class="px-5 py-4">{{ $order->payment_status }}</td>
                                <td class="px-5 py-4">{{ $order->status }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-black text-pink-700 hover:text-pink-800">Manage</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-slate-500">No jobs have been logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </section>
    </main>
@endsection
