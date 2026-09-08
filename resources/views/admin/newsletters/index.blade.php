@extends('layouts.admin')

@section('title', 'Newsletter Campaigns | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-pink-700">Customer Marketing</p>
                    <h1 class="mt-1 text-4xl font-black text-slate-950">Newsletter Campaigns</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-500">
                        Send marketing emails to verified and active registered customers.
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Current Audience</p>
                        <p class="text-2xl font-black text-slate-950">{{ number_format($audienceCount) }}</p>
                    </div>
                    <a href="{{ route('admin.newsletters.create') }}" class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-black text-white transition hover:bg-pink-700">
                        + New Newsletter
                    </a>
                </div>
            </div>

            @if (session('status'))
                <p class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </p>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-950">Recent Campaigns</h2>
            <div class="mt-5">
                <livewire:admin.newsletter-campaigns-table />
            </div>
        </section>
    </div>
@endsection
