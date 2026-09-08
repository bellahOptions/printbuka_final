@extends('layouts.admin')

@section('title', 'Customer Management (CRM) | Printbuka')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6">

    {{-- ════════ HERO ════════ --}}
    <section class="animate-fade-in-up pb-card overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-400"></div>
        <div class="flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="pb-badge pb-badge-info">CRM — Customer Relations</span>
                    <span class="text-xs text-slate-500">Manage accounts, lifecycle, and direct outreach</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Customer database</h1>
                <p class="text-sm text-slate-500 max-w-lg">
                    Track customer lifecycle, order history, spend, and communicate directly from this workspace.
                </p>
            </div>
        </div>

        @if(session('status'))
            <div class="pb-alert pb-alert-success mx-6 mb-6">
                <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif
    </section>

    {{-- ════════ SEARCH / FILTER ════════ --}}
    <form method="GET" action="{{ route('admin.customers.index') }}"
          class="animate-fade-in-up delay-100 pb-card p-5">
        <p class="pb-label">Search customers</p>
        <div class="flex flex-col gap-2 sm:flex-row mt-1">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Name, email, phone, company…"
                   class="pb-input flex-1">
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="pb-btn pb-btn-md pb-btn-ink text-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                <a href="{{ route('admin.customers.index') }}"
                   class="pb-btn pb-btn-md pb-btn-outline text-sm">Reset</a>
            </div>
        </div>
    </form>

    {{-- ════════ CUSTOMER TABLE — CRM VIEW ════════ --}}
    <section class="animate-fade-in-up delay-200 pb-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-4 w-1 rounded-full bg-blue-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Customer List</p>
                </div>
                <h2 class="text-xl font-bold text-slate-900">All customers</h2>
            </div>
        </div>

        <livewire:admin.customers-table :filters="$filters" />
    </section>

    {{-- ════════ CRM LIFECYCLE LEGEND ════════ --}}
    <div class="animate-fade-in-up delay-300 pb-card p-5">
        <p class="pb-stat-label mb-3">CRM Lifecycle Legend</p>
        <div class="flex flex-wrap gap-3">
            @foreach([
                ['label'=>'Lead',       'badge'=>'pb-badge-secondary', 'desc'=>'0 orders, 0 spend'],
                ['label'=>'Prospect',   'badge'=>'pb-badge-info',      'desc'=>'1+ orders, unpaid'],
                ['label'=>'New Client', 'badge'=>'pb-badge-primary',   'desc'=>'1–2 paid orders'],
                ['label'=>'Regular',    'badge'=>'pb-badge-success',   'desc'=>'3+ orders, &lt;₦100k spend'],
                ['label'=>'VIP Client', 'badge'=>'pb-badge-warning',   'desc'=>'3+ orders, ₦100k+ spend'],
            ] as $stage)
                <div class="flex items-center gap-2">
                    <span class="pb-badge {{ $stage['badge'] }} text-[10px]">{{ $stage['label'] }}</span>
                    <span class="text-xs text-slate-400">{!! $stage['desc'] !!}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
