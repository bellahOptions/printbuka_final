<div wire:poll.10s>
    <div class="flex items-center justify-between gap-4">
        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Live dashboard updated {{ $lastUpdated }}</p>
        <p class="text-xs font-black uppercase tracking-wide text-cyan-700">{{ $activityCountToday }} staff actions today</p>
    </div>

    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $card)
            <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-black uppercase tracking-wide {{ $card['tone'] }}">{{ $card['label'] }}</p>
                <p class="mt-3 text-5xl font-black text-slate-950">{{ number_format($card['value']) }}</p>
            </div>
        @endforeach
    </div>

    @if ($canViewFinance)
        <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Live Finance</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                @foreach ($financeCards as $card)
                    <div class="rounded-md border border-slate-200 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                        @if (($card['suffix'] ?? null) === '%')
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format($card['value'], 1) }}%</p>
                        @else
                            <p class="mt-2 text-2xl font-black text-slate-950">NGN {{ number_format($card['value'], 2) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Live Job Status</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($jobStatusCounts as $status => $count)
                <div class="rounded-md border border-slate-200 p-4">
                    <p class="text-sm font-black text-slate-800">{{ $status }}</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($count) }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
