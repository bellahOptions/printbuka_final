@php
    $toneClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'error' => 'border-pink-200 bg-pink-50 text-pink-800',
        'info' => 'border-cyan-200 bg-cyan-50 text-cyan-800',
    ];
@endphp

<div class="group/notifications relative" wire:poll.15s>
    @if ($surfaceNotifications->isNotEmpty())
        <div class="fixed bottom-4 left-4 z-[60] w-96 max-w-[calc(100vw-2rem)] space-y-3">
            @foreach ($surfaceNotifications->take(2) as $notification)
                @php($data = $notification->data)
                @php($type = $data['type'] ?? 'info')
                <article class="rounded-md border bg-white p-4 shadow-2xl shadow-slate-900/20 {{ $toneClasses[$type] ?? $toneClasses['info'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black">{{ $data['title'] ?? 'Notification' }}</p>
                            <p class="mt-1 text-sm font-semibold leading-5 text-slate-700">{{ $data['message'] ?? '' }}</p>
                            @if (filled($data['action_url'] ?? null))
                                <a href="{{ $data['action_url'] }}" class="mt-2 inline-flex text-xs font-black uppercase tracking-wide text-pink-700">Open</a>
                            @endif
                        </div>
                        <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-black uppercase tracking-wide opacity-70 transition hover:opacity-100">Read</button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-800 transition hover:border-pink-300 hover:text-pink-700" aria-label="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($count > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-pink-600 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $count > 99 ? '99+' : $count }}</span>
        @endif
    </button>

    <div class="invisible absolute right-0 top-full z-50 mt-3 w-80 max-w-[calc(100vw-2rem)] translate-y-2 rounded-md border border-slate-100 bg-white p-4 opacity-0 shadow-2xl shadow-slate-900/10 transition duration-200 group-hover/notifications:visible group-hover/notifications:translate-y-0 group-hover/notifications:opacity-100 group-focus-within/notifications:visible group-focus-within/notifications:translate-y-0 group-focus-within/notifications:opacity-100">
        <div class="flex items-center justify-between gap-4">
            <p class="text-xs font-black uppercase tracking-wide text-pink-700">Notifications</p>
            @if ($count > 0)
                <button type="button" wire:click="markAllAsRead" class="text-xs font-black uppercase tracking-wide text-slate-500 transition hover:text-pink-700">Mark all read</button>
            @endif
        </div>

        <div class="mt-3 space-y-3">
            @forelse ($notifications as $notification)
                @php($data = $notification->data)
                <article class="rounded-md border border-slate-200 p-3">
                    <p class="text-sm font-black text-slate-950">{{ $data['title'] ?? 'Notification' }}</p>
                    <p class="mt-2 text-xs font-semibold leading-5 text-slate-600">{{ $data['message'] ?? '' }}</p>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-wide text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                        <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-black text-pink-700 transition hover:text-pink-800">Mark read</button>
                    </div>
                </article>
            @empty
                <p class="rounded-md border border-dashed border-slate-300 p-4 text-sm font-semibold text-slate-500">No notifications right now.</p>
            @endforelse
        </div>
    </div>
</div>
