@extends('layouts.admin')

@section('title', 'Admin Job Tracker | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Production Job Tracker</p>
                <h1 class="mt-2 text-4xl text-slate-950">All customer orders.</h1>
            </div>
            @if (auth()->user()->canAdmin('orders.create'))
                <a href="{{ route('admin.orders.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                    Create Job
                </a>
            @endif
        </div>

        <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">Job Order / Invoice #</th>
                        <th class="px-5 py-4">Client</th>
                        <th class="px-5 py-4">Channel</th>
                        <th class="px-5 py-4">Priority</th>
                        <th class="px-5 py-4">Payment Status</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-5 py-4">
                                <span class="block font-black text-slate-900">{{ $order->job_order_number ?? $order->displayNumber() }}</span>
                                <span class="text-xs font-semibold text-slate-500">{{ $order->invoice?->invoice_number ?? 'Invoice Pending' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="block font-bold">{{ $order->customer_name }}</span>
                                <span class="text-xs text-slate-500">{{ $order->customer_phone }} · {{ $order->customer_email }}</span>
                            </td>
                            <td class="px-5 py-4">{{ $order->channel ?? 'Online' }}</td>
                            <td class="px-5 py-4">{{ $order->priority ?: '—' }}</td>
                            <td class="px-5 py-4">{{ $order->payment_status ?: 'Pending' }}</td>
                            <td class="px-5 py-4">{{ $order->status }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-black text-pink-700 hover:text-pink-800">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">No jobs have been logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
