@extends('layouts.admin')

@section('title', 'Newsletter Campaigns | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="pb-card p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">Customer Marketing</p>
                    <h1 class="pb-page-title mt-1">Newsletter Campaigns</h1>
                    <p class="pb-page-subtitle">
                        Send marketing emails to verified and active registered customers.
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="pb-stat-card text-right">
                        <p class="pb-stat-label">Current Audience</p>
                        <p class="pb-stat-value">{{ number_format($audienceCount) }}</p>
                    </div>
                    <a href="{{ route('admin.newsletters.create') }}" class="pb-btn pb-btn-lg pb-btn-primary">
                        + New Newsletter
                    </a>
                </div>
            </div>

            @if (session('status'))
                <div class="pb-alert pb-alert-success mt-5">
                    {{ session('status') }}
                </div>
            @endif
        </section>

        <section class="pb-card p-6">
            <h2 class="pb-section-title">Recent Campaigns</h2>
            <div class="mt-5">
                <livewire:admin.newsletter-campaigns-table />
            </div>
        </section>
    </div>
@endsection
