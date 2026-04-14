@php
    $formatGroups = $surfaceNotifications->groupBy(fn ($notification) => $notification->display_format ?: 'alert');
    $toneClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'urgent' => 'border-pink-200 bg-pink-50 text-pink-800',
        'info' => 'border-cyan-200 bg-cyan-50 text-cyan-800',
    ];
    $solidToneClasses = [
        'success' => 'border-emerald-400 bg-emerald-600 text-white',
        'warning' => 'border-amber-400 bg-amber-500 text-slate-950',
        'urgent' => 'border-pink-500 bg-pink-700 text-white',
        'info' => 'border-cyan-400 bg-cyan-600 text-white',
    ];
@endphp

<div class="group/notifications relative" wire:poll.15s>
    @if (($formatGroups->get('marquee') ?? collect())->isNotEmpty())
        <div class="fixed inset-x-0 top-0 z-[70] border-b border-pink-200 bg-pink-700 py-2 text-white shadow-lg">
            <div class="mx-auto flex max-w-7xl items-center gap-4 overflow-hidden px-4 sm:px-6 lg:px-8">
                <span class="rounded-md bg-white/15 px-3 py-1 text-[10px] font-black uppercase tracking-wider">Live</span>
                <div class="min-w-0 flex-1 overflow-hidden">
                    <div class="animate-[marquee_24s_linear_infinite] whitespace-nowrap text-sm font-black">
                        @foreach ($formatGroups->get('marquee') as $notification)
                            <span class="mx-6">{{ $notification->title }}: {{ $notification->message }}</span>
                        @endforeach
                    </div>
                </div>
                @foreach ($formatGroups->get('marquee')->take(1) as $notification)
                    <button type="button" wire:click="markAsRead({{ $notification->id }})" class="rounded-md border border-white/30 px-3 py-1 text-xs font-black transition hover:bg-white hover:text-pink-700">Read</button>
                @endforeach
            </div>
        </div>
    @endif

    @if (($formatGroups->get('flash') ?? collect())->isNotEmpty())
        <div class="fixed inset-x-4 top-20 z-[60] mx-auto max-w-3xl space-y-2">
            @foreach ($formatGroups->get('flash') as $notification)
                <article class="flex items-start justify-between gap-4 rounded-md border p-4 shadow-xl {{ $solidToneClasses[$notification->type] ?? $solidToneClasses['info'] }}">
                    <div>
                        <p class="font-black">{{ $notification->title }}</p>
                        <p class="mt-1 text-sm font-semibold opacity-90">{{ $notification->message }}</p>
                    </div>
                    <button type="button" wire:click="markAsRead({{ $notification->id }})" class="rounded-md border border-current px-3 py-1 text-xs font-black opacity-80 transition hover:opacity-100">Read</button>
                </article>
            @endforeach
        </div>
    @endif

    @if (($formatGroups->get('toast') ?? collect())->isNotEmpty())
        <div class="fixed right-4 top-24 z-[60] w-80 max-w-[calc(100vw-2rem)] space-y-3">
            @foreach ($formatGroups->get('toast') as $notification)
                <article class="rounded-md border bg-white p-4 shadow-2xl shadow-slate-900/20 {{ $toneClasses[$notification->type] ?? $toneClasses['info'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm font-semibold leading-5 text-slate-700">{{ $notification->message }}</p>
                        </div>
                        <button type="button" wire:click="markAsRead({{ $notification->id }})" class="text-xs font-black uppercase tracking-wide opacity-70 transition hover:opacity-100">Read</button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @foreach (($formatGroups->get('modal') ?? collect())->take(1) as $notification)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
            <article class="w-full max-w-lg rounded-md border border-slate-200 bg-white p-6 shadow-2xl">
                <p class="text-xs font-black uppercase tracking-wider text-pink-700">{{ ucfirst($notification->type) }} Notification</p>
                <h2 class="mt-3 text-3xl font-black text-slate-950">{{ $notification->title }}</h2>
                <p class="mt-4 text-sm font-semibold leading-6 text-slate-600">{{ $notification->message }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" wire:click="markAsRead({{ $notification->id }})" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Mark as Read</button>
                </div>
            </article>
        </div>
    @endforeach

    @if (($formatGroups->get('alert') ?? collect())->isNotEmpty())
        <div class="fixed bottom-4 left-4 z-[60] w-96 max-w-[calc(100vw-2rem)] space-y-3">
            @foreach ($formatGroups->get('alert') as $notification)
                <article class="rounded-md border bg-white p-4 shadow-2xl shadow-slate-900/20 {{ $toneClasses[$notification->type] ?? $toneClasses['info'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-black">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm font-semibold leading-5 text-slate-700">{{ $notification->message }}</p>
                        </div>
                        <button type="button" wire:click="markAsRead({{ $notification->id }})" class="text-xs font-black uppercase tracking-wide opacity-70 transition hover:opacity-100">Read</button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-800 transition hover:border-pink-300 hover:text-pink-700" aria-label="Notifications">
        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
            <path d="M10 20a2 2 0 0 0 4 0" />
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
            @else
                <p class="text-xs font-black uppercase tracking-wide text-slate-400">Live</p>
            @endif
        </div>

        <div class="mt-3 space-y-3">
            @forelse ($notifications as $notification)
                <article class="rounded-md border border-slate-200 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-black text-slate-950">{{ $notification->title }}</p>
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-black uppercase text-slate-600">{{ $notification->display_format ?? 'alert' }}</span>
                    </div>
                    <p class="mt-2 text-xs font-semibold leading-5 text-slate-600">{{ $notification->message }}</p>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <span class="text-[10px] font-black uppercase tracking-wide text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                        <button type="button" wire:click="markAsRead({{ $notification->id }})" class="text-xs font-black text-pink-700 transition hover:text-pink-800">Mark read</button>
                    </div>
                </article>
            @empty
                <p class="rounded-md border border-dashed border-slate-300 p-4 text-sm font-semibold text-slate-500">No notifications right now.</p>
            @endforelse
        </div>
    </div>
</div>
