<section class="card-hover rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm" wire:poll.10s>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[0.65rem] font-black uppercase tracking-wider text-emerald-700">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Staff Management
                </span>
            </div>
            <h2 class="text-3xl font-black tracking-tight text-slate-950">Top performers and activity</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Top performers are based on recorded staff actions today until response-time metrics are captured per job phase.</p>
        </div>
        <p class="text-xs font-black uppercase tracking-wider text-slate-500">Updated {{ $lastUpdated }}</p>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <div>
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Top performers today</p>
            <div class="mt-3 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-slate-50/70">
                @forelse ($topPerformers as $performer)
                    <article class="flex items-center justify-between gap-4 bg-white/70 p-4 transition-colors hover:bg-pink-50/30">
                        <div class="flex items-center gap-3">
                            @if ($performer->user?->profilePhotoUrl())
                                <img src="{{ $performer->user->profilePhotoUrl() }}" alt="{{ $performer->user->displayName() }}" class="h-10 w-10 rounded-full border border-slate-200 object-cover">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs font-black text-slate-700">
                                    {{ $performer->user?->profileInitials() ?? 'PB' }}
                                </div>
                            @endif
                            <div>
                                <p class="font-black text-slate-950">{{ $performer->user?->displayName() ?? 'Staff member' }}</p>
                                <p class="mt-1 text-xs font-bold text-slate-500">Last action {{ $performer->last_action_at ? \Illuminate\Support\Carbon::parse($performer->last_action_at)->diffForHumans() : 'today' }}</p>
                            </div>
                        </div>
                        <p class="text-3xl font-black text-slate-950">{{ number_format($performer->total) }}</p>
                    </article>
                @empty
                    <p class="bg-white/70 p-5 text-sm font-semibold text-slate-600">No top performers yet today.</p>
                @endforelse
            </div>

            <p class="mt-6 text-xs font-black uppercase tracking-wider text-slate-500">Role statistics</p>
            <div class="mt-3 space-y-3">
                @forelse ($roleCounts as $roleCount)
                    @php($percentage = $staffTotal > 0 ? min(100, ($roleCount->total / $staffTotal) * 100) : 0)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-black text-slate-800">{{ config('printbuka_admin.role_labels.'.$roleCount->role, $roleCount->role) }}</p>
                            <p class="text-2xl font-black text-slate-950">{{ number_format($roleCount->total) }}</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-gradient-to-r from-pink-500 to-pink-600" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 p-5 text-sm font-semibold text-slate-600">No staff roles available.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <p class="text-xs font-black uppercase tracking-wider text-slate-500">Live Staff Activity</p>
                <p class="text-sm font-black text-slate-950">{{ number_format($activitiesToday) }} actions today</p>
            </div>
            <div class="mt-3 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200">
                @forelse ($activities as $activity)
                    <article class="grid gap-2 p-4 transition-colors hover:bg-cyan-50/30 md:grid-cols-[1fr_auto] md:items-center">
                        <div class="flex items-center gap-3">
                            @if ($activity->user?->profilePhotoUrl())
                                <img src="{{ $activity->user->profilePhotoUrl() }}" alt="{{ $activity->user->displayName() }}" class="h-10 w-10 rounded-full border border-slate-200 object-cover">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-xs font-black text-slate-700">
                                    {{ $activity->user?->profileInitials() ?? 'PB' }}
                                </div>
                            @endif
                            <div>
                                <p class="font-black text-slate-950">{{ $activity->user?->displayName() ?? 'Staff member' }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-600">{{ $activity->action }}</p>
                            </div>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-xs font-black uppercase tracking-wider text-cyan-700">{{ config('printbuka_admin.role_labels.'.$activity->role, $activity->role) }}</p>
                            <p class="mt-1 text-xs font-bold text-slate-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </article>
                @empty
                    <p class="p-5 text-sm font-semibold text-slate-600">No staff activity has been logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
