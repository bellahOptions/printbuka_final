<section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm" wire:poll.10s>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-amber-700">Live Staff Activity</p>
            <h2 class="mt-2 text-3xl text-slate-950">{{ number_format($activitiesToday) }} actions today</h2>
        </div>
        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Updated {{ $lastUpdated }}</p>
    </div>

    <div class="mt-5 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
        <div class="space-y-3">
            @foreach ($roleCounts as $roleCount)
                <div class="flex items-center justify-between rounded-md border border-slate-200 p-4">
                    <span class="text-sm font-black text-slate-800">{{ config('printbuka_admin.role_labels.'.$roleCount->role, $roleCount->role) }}</span>
                    <span class="text-2xl font-black text-slate-950">{{ number_format($roleCount->total) }}</span>
                </div>
            @endforeach
        </div>

        <div class="space-y-3">
            @forelse ($activities as $activity)
                <article class="rounded-md border border-slate-200 p-4">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <p class="font-black text-slate-950">{{ $activity->user?->displayName() ?? 'Staff member' }}</p>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                    <p class="mt-1 text-sm font-semibold text-slate-700">{{ $activity->action }}</p>
                    <p class="mt-2 text-xs font-black uppercase tracking-wide text-cyan-700">{{ config('printbuka_admin.role_labels.'.$activity->role, $activity->role) }}{{ $activity->department ? ' / '.$activity->department : '' }}</p>
                </article>
            @empty
                <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm text-slate-600">No staff activity has been logged yet.</p>
            @endforelse
        </div>
    </div>
</section>
