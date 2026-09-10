<div x-data="{
        open: false,
        top: 0,
        right: 0,
        toggle() {
            this.open = !this.open;
            if (this.open) this.reposition();
        },
        close() { this.open = false; },
        reposition() {
            const r = this.$refs.bellBtn.getBoundingClientRect();
            this.top  = r.bottom + 8;
            this.right = window.innerWidth - r.right;
        }
     }"
     @keydown.escape.window="close()"
     @resize.window.debounce.100ms="open && reposition()"
     wire:poll.15s
     class="relative">

    {{-- ── Bell button ── --}}
    <button x-ref="bellBtn"
            @click="toggle()"
            type="button"
            class="relative inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-800 transition hover:border-pink-300 hover:text-pink-700"
            aria-label="Notifications"
            :aria-expanded="open.toString()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        @if ($count > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-pink-600 px-1.5 py-0.5 text-[10px] font-black text-white">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </button>

    {{-- ── Dropdown panel + backdrop (teleported to body so nothing can clip it) ──
         x-teleport only clones the template's FIRST element child, so the
         backdrop and panel must share one wrapper — two sibling roots here
         means the second one is silently dropped and never rendered. ── --}}
    @teleport('body')
    <div class="contents">

        {{-- Invisible backdrop — captures outside clicks to close the panel --}}
        <div x-show="open"
             @click="close()"
             class="fixed inset-0 z-[198]"
             style="display:none;">
        </div>

        {{-- The panel itself --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             :style="{ top: top + 'px', right: right + 'px', maxHeight: 'min(480px, calc(100vh - 90px))' }"
             class="fixed z-[199] flex w-80 max-w-[calc(100vw-2rem)] flex-col
                    rounded-xl border border-slate-100 bg-white shadow-2xl shadow-slate-900/15"
             style="display:none;">

            {{-- Panel header --}}
            <div class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-100 px-4 py-3">
                <p class="text-xs font-black uppercase tracking-wide text-pink-700">Notifications</p>
                @if ($count > 0)
                    <button type="button" wire:click="markAllAsRead"
                            class="text-xs font-black uppercase tracking-wide text-slate-500 transition hover:text-pink-700">
                        Mark all read
                    </button>
                @endif
            </div>

            {{-- Scrollable notifications list --}}
            <div class="flex-1 overflow-y-auto overscroll-contain px-3 py-3 space-y-2">
                @forelse ($notifications as $notification)
                    @php($data = $notification->data)
                    @php($resolvedUrl = \App\Support\NotificationActionUrl::resolve($data))
                    <article class="rounded-lg border border-slate-100 bg-slate-50/60 p-3 transition hover:bg-white">
                        <p class="text-sm font-black leading-snug text-slate-950">{{ $data['title'] ?? 'Notification' }}</p>
                        <p class="mt-1.5 text-xs font-medium leading-5 text-slate-600">{{ $data['message'] ?? $data['body'] ?? '' }}</p>
                        <div class="mt-2.5 flex items-center justify-between gap-3">
                            <span class="text-[10px] font-black uppercase tracking-wide text-slate-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            <div class="flex items-center gap-3">
                                @if (filled($resolvedUrl))
                                    <button type="button"
                                            wire:click="markAsReadAndOpen('{{ $notification->id }}', '{{ $resolvedUrl }}')"
                                            class="text-xs font-black text-pink-700 transition hover:text-pink-800">
                                        Open
                                    </button>
                                @endif
                                <button type="button" wire:click="markAsRead('{{ $notification->id }}')"
                                        class="text-xs font-black text-slate-400 transition hover:text-pink-700">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-200 p-4 text-center text-sm font-semibold text-slate-500">
                        No notifications right now.
                    </p>
                @endforelse
            </div>

            {{-- Panel footer --}}
            <div class="shrink-0 border-t border-slate-100 px-4 py-2.5">
                <a href="{{ route('admin.notifications.index') }}"
                   class="block text-center text-xs font-black uppercase tracking-wide text-slate-500 transition hover:text-pink-700">
                    View all notifications
                </a>
            </div>
        </div>

    </div>
    @endteleport

</div>
