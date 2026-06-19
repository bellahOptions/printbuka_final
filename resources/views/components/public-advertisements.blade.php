@php
    $only = collect((array) ($placements ?? ['popup', 'top_banner', 'inline_banner', 'floating_card', 'footer_banner']));
    $ads = collect();

    if (\Illuminate\Support\Facades\Schema::hasTable('advertisements')) {
        $ads = \App\Models\Advertisement::query()
            ->active()
            ->whereIn('placement', $only->all())
            ->orderBy('sort_order')
            ->latest()
            ->get()
            ->groupBy('placement');
    }
@endphp

@if ($only->contains('top_banner') && ($ads->get('top_banner') ?? collect())->isNotEmpty())
    @php($ad = $ads->get('top_banner')->first())
    <aside class="border-b border-pink-200 bg-pink-700 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-3 text-sm font-bold sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div>
                <span class="font-black">{{ $ad->title }}</span>
                @if ($ad->body)
                    <span class="ml-2 text-pink-50">{{ $ad->body }}</span>
                @endif
            </div>
            @if ($ad->cta_url && $ad->cta_label)
                <a href="{{ $ad->cta_url }}" class="inline-flex w-fit rounded-md bg-white px-3 py-1.5 text-xs font-black text-pink-700 transition hover:bg-pink-50">{{ $ad->cta_label }}</a>
            @endif
        </div>
    </aside>
@endif

@if ($only->contains('inline_banner') && ($ads->get('inline_banner') ?? collect())->isNotEmpty())
    @php($ad = $ads->get('inline_banner')->first())
    <aside class="bg-white py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-md border border-slate-200 bg-slate-950 text-white shadow-sm">
                <div class="grid gap-5 p-5 sm:grid-cols-[1fr_auto] sm:items-center">
                    <div>
                        <p class="text-lg font-black">{{ $ad->title }}</p>
                        @if ($ad->body)
                            <p class="mt-1 text-sm font-semibold text-slate-300">{{ $ad->body }}</p>
                        @endif
                    </div>
                    @if ($ad->cta_url && $ad->cta_label)
                        <a href="{{ $ad->cta_url }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-pink-600 px-4 text-sm font-black text-white transition hover:bg-pink-700">{{ $ad->cta_label }}</a>
                    @endif
                </div>
            </div>
        </div>
    </aside>
@endif

@if ($only->contains('floating_card') && ($ads->get('floating_card') ?? collect())->isNotEmpty())
    @php($ad = $ads->get('floating_card')->first())
    <aside class="fixed bottom-5 right-5 z-50 w-80 max-w-[calc(100vw-2rem)] rounded-md border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/20">
        @if ($ad->image_url)
            <img src="{{ $ad->image_url }}" alt="" class="mb-3 h-28 w-full rounded-md object-cover">
        @endif
        <p class="font-black text-slate-950">{{ $ad->title }}</p>
        @if ($ad->body)
            <p class="mt-1 text-sm font-semibold leading-5 text-slate-600">{{ $ad->body }}</p>
        @endif
        @if ($ad->cta_url && $ad->cta_label)
            <a href="{{ $ad->cta_url }}" class="mt-3 inline-flex min-h-10 items-center justify-center rounded-md bg-pink-600 px-4 text-sm font-black text-white transition hover:bg-pink-700">{{ $ad->cta_label }}</a>
        @endif
    </aside>
@endif

{{-- ── Popup modal ad (shows once per session per ad, after 1.5 s delay) ── --}}
@if ($only->contains('popup') && ($ads->get('popup') ?? collect())->isNotEmpty())
    @php($popupAd = $ads->get('popup')->first())
    <div x-data="{
            open: false,
            init() {
                const key = 'pb_ad_seen_{{ $popupAd->id }}';
                if (!sessionStorage.getItem(key)) {
                    setTimeout(() => { this.open = true; }, 1500);
                }
            },
            dismiss() {
                sessionStorage.setItem('pb_ad_seen_{{ $popupAd->id }}', '1');
                this.open = false;
            }
         }"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="dismiss()"
         class="fixed inset-0 z-[500] flex items-end justify-center p-4 sm:items-center"
         style="display:none;"
         role="dialog"
         aria-modal="true">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="dismiss()"></div>

        {{-- Modal card --}}
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-6 scale-95"
             class="relative z-10 w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl shadow-slate-900/30">

            {{-- Close button --}}
            <button @click="dismiss()"
                    class="absolute right-3 top-3 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-slate-500 backdrop-blur-sm transition hover:bg-white hover:text-slate-900"
                    aria-label="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            @if ($popupAd->image_url)
                <img src="{{ $popupAd->image_url }}" alt="{{ $popupAd->title }}" class="h-52 w-full object-cover">
            @else
                <div class="h-1.5 w-full bg-gradient-to-r from-pink-500 to-pink-700"></div>
            @endif

            <div class="p-6">
                <p class="text-xl font-black text-slate-950">{{ $popupAd->title }}</p>

                @if ($popupAd->body)
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">{{ $popupAd->body }}</p>
                @endif

                @if ($popupAd->cta_url && $popupAd->cta_label)
                    <a href="{{ $popupAd->cta_url }}"
                       @click="dismiss()"
                       class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-pink-600 px-5 text-sm font-black text-white transition hover:bg-pink-700">
                        {{ $popupAd->cta_label }}
                    </a>
                @endif

                <button @click="dismiss()"
                        class="mt-3 block w-full text-center text-xs font-bold uppercase tracking-wide text-slate-400 transition hover:text-slate-600">
                    No thanks, close
                </button>
            </div>
        </div>
    </div>
@endif

@if ($only->contains('footer_banner') && ($ads->get('footer_banner') ?? collect())->isNotEmpty())
    @php($ad = $ads->get('footer_banner')->first())
    <aside class="border-t border-slate-200 bg-cyan-50 py-5">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <div>
                <p class="font-black text-slate-950">{{ $ad->title }}</p>
                @if ($ad->body)
                    <p class="text-sm font-semibold text-slate-600">{{ $ad->body }}</p>
                @endif
            </div>
            @if ($ad->cta_url && $ad->cta_label)
                <a href="{{ $ad->cta_url }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-cyan-200 bg-white px-4 text-sm font-black text-cyan-800 transition hover:border-pink-300 hover:text-pink-700">{{ $ad->cta_label }}</a>
            @endif
        </div>
    </aside>
@endif
