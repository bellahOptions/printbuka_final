@extends('layouts.admin')

@section('title', 'IT Support Tickets | Printbuka')

@section('content')
<main class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">IT Support Tickets</h1>
            <p class="text-sm font-semibold text-slate-500">Create and track internal tickets for Process & Technology Manager / IT resolution.</p>
        </div>
        <a href="{{ route('admin.support.create') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">New Ticket</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Total</p>
            <p class="mt-2 text-2xl font-black text-slate-950">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Open</p>
            <p class="mt-2 text-2xl font-black text-amber-600">{{ $stats['open'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">In Progress</p>
            <p class="mt-2 text-2xl font-black text-cyan-700">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Resolved / Closed</p>
            <p class="mt-2 text-2xl font-black text-emerald-700">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <livewire:admin.support-tickets-table />
</main>
@endsection
