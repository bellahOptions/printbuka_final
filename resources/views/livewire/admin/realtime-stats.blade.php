<section class="rounded-md border border-slate-200 bg-white p-6" wire:poll.10s>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Statistics</p>
            <h2 class="mt-2 text-3xl leading-tight text-slate-950">Live finance and job statistics</h2>
        </div>
        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Updated {{ $lastUpdated }}</p>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <article class="rounded-md border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 text-4xl font-black leading-none text-slate-950">{{ number_format($card['value']) }}</p>
            </article>
        @endforeach
    </div>

    @if ($canViewFinance)
        <div class="mt-6 border-t border-slate-200 pt-6">
            <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Finance Graph / Figures</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                @foreach ($financeCards as $card)
                    <article class="rounded-md border border-slate-200 p-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                        @if (($card['suffix'] ?? null) === '%')
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format($card['value'], 1) }}%</p>
                        @else
                            <p class="mt-2 text-2xl font-black text-slate-950">NGN {{ number_format($card['value'], 2) }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 border-t border-slate-200 pt-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Job Statistics</p>
                <h3 class="mt-2 text-2xl font-black text-slate-950">Active jobs, pending payment, totals</h3>
            </div>
            <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $activityCountToday }} staff actions today</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($jobStatusCounts as $status => $count)
                <div class="flex items-center justify-between gap-4 rounded-md border border-slate-200 px-4 py-3">
                    <p class="text-sm font-black text-slate-800">{{ $status }}</p>
                    <p class="text-2xl font-black text-slate-950">{{ number_format($count) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
