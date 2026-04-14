<section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm" wire:poll.15s>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Live Notifications</p>
            <h2 class="mt-2 text-3xl text-slate-950">Admin alerts</h2>
        </div>
        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Updated {{ $lastUpdated }}</p>
    </div>

    <div class="mt-5 space-y-3">
        @if (filled($settings['notification_message'] ?? null))
            <article class="rounded-md border border-pink-200 bg-pink-50 p-4">
                <p class="text-sm font-black text-pink-800">Notice</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $settings['notification_message'] }}</p>
            </article>
        @endif

        @if (filled($settings['announcement'] ?? null))
            <article class="rounded-md border border-cyan-200 bg-cyan-50 p-4">
                <p class="text-sm font-black text-cyan-800">Announcement</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $settings['announcement'] }}</p>
            </article>
        @endif

        @forelse ($notifications as $notification)
            <article class="rounded-md border border-slate-200 p-4">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="font-black text-slate-950">{{ $notification->title }}</p>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $notification->message }}</p>
            </article>
        @empty
            @unless (filled($settings['notification_message'] ?? null) || filled($settings['announcement'] ?? null))
                <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm text-slate-600">No live alerts right now.</p>
            @endunless
        @endforelse
    </div>
</section>
