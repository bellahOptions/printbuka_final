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
            <p class="text-xs text-slate-400 shrink-0">
                {{ $customers->total() }} total · showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }}
            </p>
        </div>

        <div class="table-scroll-container overflow-x-auto">
            <table class="pb-table w-full min-w-[1100px]">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Company</th>
                        <th>
                            <abbr title="Total orders placed" class="no-underline">Orders</abbr>
                        </th>
                        <th>Invoices</th>
                        <th>Lifetime Value</th>
                        <th>Lifecycle</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                @php
                    $lifecycleFn = static function(int $orders, float $ltv): array {
                        return match(true) {
                            $orders === 0 && $ltv == 0                        => ['label' => 'Lead',       'badge' => 'pb-badge-secondary'],
                            $orders >= 1  && $ltv == 0                        => ['label' => 'Prospect',   'badge' => 'pb-badge-info'],
                            $orders >= 1  && $ltv > 0 && $orders < 3         => ['label' => 'New Client', 'badge' => 'pb-badge-primary'],
                            $orders >= 3  && $ltv < 100000                    => ['label' => 'Regular',    'badge' => 'pb-badge-success'],
                            $orders >= 3  && $ltv >= 100000                   => ['label' => 'VIP Client', 'badge' => 'pb-badge-warning'],
                            default                                            => ['label' => 'Client',     'badge' => 'pb-badge-secondary'],
                        };
                    };
                @endphp
                <tbody>
                    @forelse($customers as $customer)
                        @php($lifecycle = $lifecycleFn((int)($customer->orders_count ?? 0), (float)($customer->total_paid ?? 0)))
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="pb-avatar pb-avatar-md shrink-0">
                                        <div class="pb-avatar-fallback bg-blue-100 text-blue-800 font-semibold text-xs">
                                            {{ strtoupper(substr($customer->displayName(), 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900 text-sm">{{ $customer->displayName() }}</p>
                                        <p class="text-xs text-slate-400">{{ $customer->email }}</p>
                                        <p class="text-xs text-slate-400">{{ $customer->phone ?: '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-slate-600">{{ $customer->companyName ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-slate-900">{{ number_format((int)($customer->orders_count ?? 0)) }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-slate-700">{{ number_format((int)($customer->invoices_count ?? 0)) }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-slate-900">
                                    ₦{{ number_format((float)($customer->total_paid ?? 0), 0) }}
                                </span>
                            </td>
                            <td>
                                <span class="pb-badge {{ $lifecycle['badge'] }} text-[10px]">
                                    {{ $lifecycle['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-500">{{ $customer->created_at->format('M j, Y') }}</span>
                            </td>
                            <td>
                                <span class="pb-badge {{ $customer->is_active ? 'pb-badge-success' : 'pb-badge-warning' }} text-[10px]">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end items-center gap-2">
                                    {{-- Toggle status --}}
                                    <form action="{{ route('admin.customers.update-status', $customer) }}"
                                          method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $customer->is_active ? 0 : 1 }}">
                                        <button type="submit"
                                            class="pb-btn pb-btn-sm {{ $customer->is_active ? 'pb-btn-outline text-amber-700 border-amber-200 hover:bg-amber-50 hover:border-amber-400' : 'pb-btn-success' }} text-xs">
                                            {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    {{-- Message popover --}}
                                    <div class="relative pb-popover-wrapper">
                                        <button type="button"
                                            data-popover-btn
                                            class="pb-btn pb-btn-sm pb-btn-outline text-xs">
                                            Message
                                        </button>
                                        <div data-popover-panel
                                             class="absolute right-0 z-30 mt-2 w-80 pb-card shadow-xl p-4 hidden">
                                            <h4 class="text-sm font-semibold text-slate-900 mb-3">
                                                Send to {{ $customer->displayName() }}
                                            </h4>
                                            <form action="{{ route('admin.customers.send-message', $customer) }}"
                                                  method="POST" class="space-y-3">
                                                @csrf
                                                <div class="pb-field">
                                                    <label class="pb-label">Subject</label>
                                                    <input type="text" name="subject" required class="pb-input text-sm">
                                                </div>
                                                <div class="pb-field">
                                                    <label class="pb-label">Message</label>
                                                    <textarea name="message" rows="3" required
                                                              class="pb-textarea text-sm"></textarea>
                                                </div>
                                                <button type="submit" class="pb-btn pb-btn-md pb-btn-primary text-sm w-full">
                                                    Send Email
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    @if(auth()->user()?->role === 'super_admin')
                                        <form action="{{ route('admin.customers.destroy', $customer) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Permanently delete this customer? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="pb-btn pb-btn-sm pb-btn-ghost text-red-600 hover:bg-red-50 text-xs">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center">
                                <div class="pb-empty border-0 bg-transparent">
                                    <svg class="pb-empty-icon h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6"/>
                                    </svg>
                                    <p class="pb-empty-title">No customers found</p>
                                    <p class="pb-empty-body">Try adjusting your search filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $customers->links() }}
        </div>
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
