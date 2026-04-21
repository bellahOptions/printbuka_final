@extends('layouts.admin')

@section('title', 'Customer Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-pink-700">Customer Management</p>
                <h1 class="mt-2 text-4xl text-slate-950">Customers.</h1>
                <p class="mt-2 text-sm font-semibold text-slate-500">Manage customer records, account status, and direct communication.</p>
            </div>
        </div>

        @if (session('status'))
            <p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>
        @endif

        <form method="GET" action="{{ route('admin.customers.index') }}" class="mt-6 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <label class="text-xs font-black uppercase tracking-wide text-slate-500">Search customers</label>
            <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, email, phone, company..."
                    class="h-11 w-full rounded-md border border-slate-200 px-4 text-sm font-semibold"
                >
                <div class="flex gap-2">
                    <button class="h-11 rounded-md bg-slate-900 px-5 text-sm font-black text-white transition hover:bg-pink-700">Search</button>
                    <a href="{{ route('admin.customers.index') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-slate-200 px-4 text-sm font-black text-slate-700 transition hover:border-pink-300 hover:text-pink-700">Reset</a>
                </div>
            </div>
        </form>

        <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Company</th>
                        <th class="px-5 py-4">Orders</th>
                        <th class="px-5 py-4">Invoices</th>
                        <th class="px-5 py-4">Total Paid</th>
                        <th class="px-5 py-4">Joined</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-black text-slate-950">{{ $customer->displayName() }}</p>
                                <p class="text-xs font-semibold text-slate-500">{{ $customer->email }}</p>
                                <p class="text-xs font-semibold text-slate-500">{{ $customer->phone ?: 'No phone' }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $customer->companyName ?: '—' }}</td>
                            <td class="px-5 py-4 font-black">{{ number_format((int) $customer->orders_count) }}</td>
                            <td class="px-5 py-4 font-black">{{ number_format((int) $customer->invoices_count) }}</td>
                            <td class="px-5 py-4 font-black text-slate-900">NGN {{ number_format((float) ($customer->total_paid ?? 0), 2) }}</td>
                            <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ $customer->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-black {{ $customer->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <form action="{{ route('admin.customers.update-status', $customer) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $customer->is_active ? 0 : 1 }}">
                                        <button class="font-black {{ $customer->is_active ? 'text-amber-700 hover:text-amber-900' : 'text-emerald-700 hover:text-emerald-900' }}">
                                            {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <details class="inline-block text-left">
                                        <summary class="cursor-pointer font-black text-pink-700 hover:text-pink-900">Message</summary>
                                        <div class="absolute right-8 z-20 mt-2 w-80 rounded-md border border-slate-200 bg-white p-4 shadow-2xl">
                                            <form action="{{ route('admin.customers.send-message', $customer) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <label class="block text-xs font-black uppercase tracking-wide text-slate-500">
                                                    Subject
                                                    <input type="text" name="subject" required class="mt-1 h-10 w-full rounded-md border border-slate-200 px-3 text-sm font-semibold">
                                                </label>
                                                <label class="block text-xs font-black uppercase tracking-wide text-slate-500">
                                                    Message
                                                    <textarea name="message" rows="4" required class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold"></textarea>
                                                </label>
                                                <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-black text-white transition hover:bg-pink-700">
                                                    Send Email
                                                </button>
                                            </form>
                                        </div>
                                    </details>

                                    @if (auth()->user()?->role === 'super_admin')
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="font-black text-slate-500 hover:text-red-700"
                                                onclick="return confirm('Delete this customer data? This cannot be undone.')"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-500">No customers matched your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $customers->links() }}</div>
    </div>
@endsection
