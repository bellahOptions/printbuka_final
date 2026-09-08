@extends('layouts.new-app')

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
        <livewire:support.ticket-list />
    </div>
</main>
@endsection