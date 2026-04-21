@extends('layouts.admin')

@section('title', 'IT Support Tickets | Printbuka')

@section('content')
<main class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-950">IT Support Tickets</h1>
            <p class="text-sm font-semibold text-slate-500">Create and track internal tickets for Super Admin / IT resolution.</p>
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

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="font-black text-slate-500">Ticket #</th>
                        <th class="font-black text-slate-500">Subject</th>
                        <th class="font-black text-slate-500">Priority</th>
                        <th class="font-black text-slate-500">Status</th>
                        <th class="font-black text-slate-500">Assigned To</th>
                        <th class="font-black text-slate-500">Updated</th>
                        <th class="font-black text-slate-500 text-right">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="font-mono text-xs font-black text-slate-700">{{ $ticket->ticket_number }}</td>
                            <td>
                                <p class="font-bold text-slate-900">{{ Str::limit($ticket->subject, 50) }}</p>
                                <p class="text-xs font-semibold text-slate-400">{{ ucfirst($ticket->category) }}</p>
                            </td>
                            <td>
                                <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-sm capitalize">{{ $ticket->priority }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $ticket->getStatusColor() }} badge-sm capitalize">{{ str_replace('_', ' ', $ticket->status) }}</span>
                            </td>
                            <td>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $ticket->assignedStaff?->displayName() ?? 'Unassigned' }}
                                </p>
                            </td>
                            <td class="text-sm font-semibold text-slate-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-xs btn-outline border-slate-200 hover:border-pink-400 hover:text-pink-700 font-black">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm font-semibold text-slate-500">No tickets yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
