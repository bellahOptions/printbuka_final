@extends('layouts.theme')

@section('title', 'My Support Tickets | Printbuka')

@section('content')
<main class="min-h-screen bg-gradient-to-br from-slate-50 to-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                        <a href="{{ route('dashboard') }}" class="hover:text-pink-600 transition">Dashboard</a>
                        <span>/</span>
                        <span class="text-slate-700 font-medium">Support Tickets</span>
                    </div>
                    <h1 class="text-3xl font-bold text-slate-900">Support Tickets</h1>
                    <p class="mt-1 text-sm text-slate-500">Track and manage your support requests</p>
                </div>
                <a href="{{ route('support.create') }}" class="btn bg-pink-600 hover:bg-pink-700 border-0 text-white shadow-md shadow-pink-200">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Ticket
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-slate-400">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Total Tickets</div>
                <div class="stat-value text-2xl text-slate-900">{{ $stats['total'] }}</div>
            </div>

            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-amber-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Open</div>
                <div class="stat-value text-2xl text-slate-900">{{ $stats['open'] }}</div>
             </div>

            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-cyan-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">In Progress</div>
                <div class="stat-value text-2xl text-slate-900">{{ $stats['in_progress'] }}</div>
             </div>

            <div class="stat bg-white rounded-2xl shadow-md border border-slate-100 p-5">
                <div class="stat-figure text-emerald-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-title text-slate-500">Resolved/Closed</div>
                <div class="stat-value text-2xl text-slate-900">{{ $stats['resolved'] }}</div>
             </div>
        </div>

        {{-- Tickets Table --}}
        <div class="card bg-white rounded-2xl shadow-md border border-slate-100">
            <div class="card-body p-6">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-slate-600 font-semibold">Ticket #</th>
                                <th class="text-slate-600 font-semibold">Subject</th>
                                <th class="text-slate-600 font-semibold hidden sm:table-cell">Category</th>
                                <th class="text-slate-600 font-semibold">Priority</th>
                                <th class="text-slate-600 font-semibold">Status</th>
                                <th class="text-slate-600 font-semibold hidden md:table-cell">Created</th>
                                <th class="text-slate-600 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="font-mono text-sm font-semibold text-slate-800">{{ $ticket->ticket_number }}</td>
                                    <td>
                                        <p class="font-medium text-slate-800">{{ Str::limit($ticket->subject, 40) }}</p>
                                        <p class="text-xs text-slate-400 sm:hidden">{{ $ticket->created_at->format('M d, Y') }}</p>
                                    </td>
                                    <td class="hidden sm:table-cell">
                                        <span class="badge badge-sm bg-slate-100 text-slate-700 capitalize">{{ $ticket->category }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->getPriorityColor() }} badge-sm capitalize">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ticket->getStatusColor() }} badge-sm capitalize">
                                            {{ str_replace('_', ' ', $ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell text-sm text-slate-500">
                                        {{ $ticket->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('support.show', $ticket) }}" 
                                           class="btn btn-xs btn-ghost text-pink-600 hover:bg-pink-50"
                                           title="View Ticket">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </td>
                                 </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12">
                                        <div class="flex flex-col items-center">
                                            <div class="h-20 w-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197"/>
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium">No support tickets yet</p>
                                            <p class="text-sm text-slate-400 mt-1">Create your first support ticket</p>
                                            <a href="{{ route('support.create') }}" class="btn btn-sm btn-pink-600 text-white mt-4">
                                                New Ticket
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($tickets->hasPages())
                    <div class="mt-6">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection