@extends('layouts.theme')

@section('title', $title.' | Printbuka')

@section('content')
    @php
        $rawContent = trim((string) ($policy?->content ?? ''));
        $isHtmlContent = $rawContent !== '' && $rawContent !== strip_tags($rawContent);
        $renderedContent = $isHtmlContent ? $rawContent : nl2br(e($rawContent));
    @endphp

    <main class="bg-[#f4fbfb] px-4 py-14 text-slate-900 sm:px-6 lg:px-8">
        <section class="mx-auto max-w-5xl space-y-8">
            <div class="rounded-2xl bg-slate-950 p-8 text-white shadow-xl shadow-cyan-950/20 sm:p-10">
                <p class="inline-flex rounded-md bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-cyan-100">Legal</p>
                <h1 class="mt-5 text-4xl font-black leading-tight sm:text-5xl">{{ $title }}</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-200">{{ $summary }}</p>
                @if ($policy?->published_at || $policy?->updated_at)
                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.12em] text-slate-300">
                        Last updated:
                        {{ optional($policy?->published_at ?? $policy?->updated_at)->format('F j, Y') }}
                    </p>
                @endif
            </div>

            <article class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm sm:p-10">
                @if ($rawContent !== '')
                    <div class="prose prose-slate max-w-none prose-headings:font-black prose-a:text-pink-700 prose-a:no-underline hover:prose-a:underline">
                        {!! $renderedContent !!}
                    </div>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-900">
                        This document is currently being updated. Please check back soon.
                    </div>
                @endif
            </article>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('policies.terms') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Terms</a>
                <a href="{{ route('policies.privacy') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Privacy</a>
                <a href="{{ route('policies.refund') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 transition hover:border-pink-300 hover:text-pink-700">Refund</a>
            </div>
        </section>
    </main>
@endsection

