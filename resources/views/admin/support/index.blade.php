@extends('layouts.admin')

@section('title', 'IT Support Tickets | Printbuka')

@section('content')
<main class="space-y-6">
    <div class="pb-page-header">
        <div>
            <h1 class="pb-page-title">IT Support Tickets</h1>
            <p class="pb-page-subtitle">Create and track internal tickets for Process & Technology Manager / IT resolution.</p>
        </div>
        <a href="{{ route('admin.support.create') }}" class="pb-btn pb-btn-primary">New Ticket</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="pb-stat-card">
            <p class="pb-stat-label">Total</p>
            <p class="pb-stat-value">{{ $stats['total'] }}</p>
        </div>
        <div class="pb-stat-card">
            <p class="pb-stat-label">Open</p>
            <p class="pb-stat-value text-amber-600">{{ $stats['open'] }}</p>
        </div>
        <div class="pb-stat-card">
            <p class="pb-stat-label">In Progress</p>
            <p class="pb-stat-value text-cyan-700">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="pb-stat-card">
            <p class="pb-stat-label">Resolved / Closed</p>
            <p class="pb-stat-value text-emerald-700">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <livewire:admin.support-tickets-table />
</main>
@endsection
