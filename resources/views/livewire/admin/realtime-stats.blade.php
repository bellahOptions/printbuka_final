<section class="pb-card p-6" wire:poll.10s>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Live finance and job statistics</h2>
        </div>
        <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <span class="pb-status-dot pb-status-online"><span></span><span></span></span>
            Updated {{ $lastUpdated }}
        </p>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        @php
            $primaryIcons = [
                'Orders' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                'Active Jobs' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'Delivered' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'Staff Online' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V8a2 2 0 00-2-2h-3m-7 14H5a2 2 0 01-2-2V8a2 2 0 012-2h3m4 14v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4m6 0h2m-6 0H6m6-14V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2m6 0H6"/>',
                'Visitors Online' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                'Visitors Today' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
            ];
            $primaryBars = [
                'Orders' => 'bg-pink-500',
                'Active Jobs' => 'bg-cyan-500',
                'Delivered' => 'bg-emerald-500',
                'Staff Online' => 'bg-amber-500',
                'Visitors Online' => 'bg-indigo-500',
                'Visitors Today' => 'bg-violet-500',
            ];
            $primaryChips = [
                'Orders' => 'bg-pink-50 text-pink-600',
                'Active Jobs' => 'bg-cyan-50 text-cyan-600',
                'Delivered' => 'bg-emerald-50 text-emerald-600',
                'Staff Online' => 'bg-amber-50 text-amber-600',
                'Visitors Online' => 'bg-indigo-50 text-indigo-600',
                'Visitors Today' => 'bg-violet-50 text-violet-600',
            ];
        @endphp
        @foreach ($cards as $card)
            <article class="pb-kpi-card">
                <div class="pb-kpi-accent-bar {{ $primaryBars[$card['label']] ?? 'bg-slate-400' }}"></div>
                <div class="mt-1 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="pb-stat-label">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-bold leading-none {{ $card['tone'] ?? 'text-slate-900' }}">{{ number_format($card['value']) }}</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $primaryChips[$card['label']] ?? 'bg-slate-100 text-slate-500' }}">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $primaryIcons[$card['label']] ?? '' !!}
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    @if ($canViewFinance)
        <div class="mt-6 border-t border-slate-100 pt-6">
            <div class="mb-4 flex items-center gap-2">
                <div class="h-4 w-1 rounded-full bg-emerald-500"></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Finance graph / figures</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @foreach ($financeCards as $card)
                    <div class="pb-stat-card">
                        <p class="pb-stat-label truncate">{{ $card['label'] }}</p>
                        @if (($card['suffix'] ?? null) === '%')
                            <p class="mt-2 text-xl font-bold text-slate-900">{{ number_format($card['value'], 1) }}%</p>
                        @else
                            <p class="mt-2 truncate text-xl font-bold {{ $card['value'] < 0 ? 'text-red-600' : 'text-slate-900' }}" title="₦{{ number_format($card['value'], 2) }}">₦{{ number_format($card['value'], 2) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 border-t border-slate-100 pt-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <div class="h-4 w-1 rounded-full bg-cyan-500"></div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Job statistics</p>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Active jobs, pending payment, totals</h3>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $activityCountToday }} staff actions today</p>
        </div>

        @php
            $statusBars = [
                'Quote Requested' => 'bg-slate-300',
                'Analyzing Job Brief' => 'bg-amber-400',
                'Design / Artwork Preparation' => 'bg-violet-400',
                'In Production' => 'bg-blue-400',
                'Quality Check & Packaging' => 'bg-cyan-400',
                'Delivery In Progress' => 'bg-indigo-400',
                'Delivered' => 'bg-emerald-400',
                'On Hold' => 'bg-slate-300',
                'Cancelled' => 'bg-red-400',
            ];
        @endphp
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($jobStatusCounts as $status => $count)
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <span class="h-8 w-1 shrink-0 rounded-full {{ $statusBars[$status] ?? 'bg-slate-300' }}"></span>
                    <p class="flex-1 truncate text-sm font-semibold text-slate-700">{{ $status }}</p>
                    <p class="shrink-0 text-xl font-bold text-slate-900">{{ number_format($count) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
