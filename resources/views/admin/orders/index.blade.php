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

        <div class="mt-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Automatic Staff Todo</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950">Paid pending jobs by staff</h2>
                </div>
                @if ($canSendTodoReminders)
                    <form method="POST" action="{{ route('admin.orders.todo-reminders.send') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-cyan-700 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-800">Send reminders</button>
                    </form>
                @endif
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse ($staffTodos as $todoGroup)
                    <article class="rounded-md border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-black text-slate-950">{{ $todoGroup['recipient']->displayName() }}</h3>
                                <p class="text-xs font-semibold text-slate-500">{{ $todoGroup['recipient']->email }}</p>
                            </div>
                            <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-black text-cyan-800">{{ count($todoGroup['items']) }} task(s)</span>
                        </div>
                        <ul class="mt-4 space-y-3">
                            @foreach (array_slice($todoGroup['items'], 0, 5) as $item)
                                <li class="rounded-md bg-white p-3 text-sm">
                                    <a href="{{ route('admin.orders.show', $item['order']) }}" class="font-black text-pink-700 hover:text-pink-800">
                                        {{ $item['order']->job_order_number ?? $item['order']->displayNumber() }}
                                    </a>
                                    <span class="font-semibold text-slate-700">- {{ $item['order']->customer_name }}</span>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $item['phase'] }} · {{ $item['payment_status'] }} · {{ $item['stuck_hours'] }} hour(s)</p>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @empty
                    <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-500 lg:col-span-2">No paid pending staff tasks right now.</p>
                @endforelse
            </div>
        </div>

        <livewire:admin.orders-table />
    </div>
@endsection
