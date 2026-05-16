<section class="mt-8 rounded-md border border-slate-200 bg-white p-6 shadow-sm" wire:poll.15s>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-wide text-pink-700">Live Notifications</p>
            <h2 class="mt-2 text-3xl text-slate-950">Admin alerts</h2>
        </div>
        <div class="flex items-center gap-3">
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-black uppercase tracking-wide text-slate-600 transition hover:border-pink-300 hover:text-pink-700">Mark all read</button>
            @endif
            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Updated {{ $lastUpdated }}</p>
        </div>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($notifications as $notification)
            @php($data = $notification->data)
            <article class="rounded-md border border-slate-200 p-4">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="font-black text-slate-950">{{ $data['title'] ?? 'Notification' }}</p>
                    <div class="flex items-center gap-3">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $data['type'] ?? 'info' }} · {{ $notification->created_at->diffForHumans() }}</p>
                        <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-black text-pink-700 transition hover:text-pink-800">Mark read</button>
                    </div>
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-700">{{ $data['message'] ?? '' }}</p>
            </article>
        @empty
            <p class="rounded-md border border-dashed border-slate-300 p-5 text-sm text-slate-600">No live alerts right now.</p>
        @endforelse
    </div>
</section>
