<section class="pb-card p-6 mt-8" wire:poll.15s>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-brand-700">Live Notifications</p>
            <h2 class="pb-section-title mt-2 text-3xl">Admin alerts</h2>
        </div>
        <div class="flex items-center gap-3">
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="pb-btn pb-btn-sm pb-btn-outline text-xs">Mark all read</button>
            @endif
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Updated {{ $lastUpdated }}</p>
        </div>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($notifications as $notification)
            @php($data = $notification->data)
            <article class="pb-card p-4">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="font-semibold text-slate-900">{{ $data['title'] ?? 'Notification' }}</p>
                    <div class="flex items-center gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $data['type'] ?? 'info' }} · {{ $notification->created_at->diffForHumans() }}</p>
                        <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-semibold text-brand-700 transition hover:text-brand-800">Mark read</button>
                    </div>
                </div>
                <p class="mt-2 text-sm text-slate-700">{{ $data['message'] ?? '' }}</p>
            </article>
        @empty
            <div class="pb-empty">
                <p class="pb-empty-title">No live alerts right now.</p>
            </div>
        @endforelse
    </div>
</section>
