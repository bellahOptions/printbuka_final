@php
    $only = collect((array) ($placements ?? ['top_banner', 'inline_banner', 'floating_card', 'footer_banner']));
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
