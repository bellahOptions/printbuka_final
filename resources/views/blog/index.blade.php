@extends('layouts.new-app')

@section('content')
<main>

    {{-- Hero --}}
    <section class="bg-brand-600 overflow-hidden flex items-center justify-center" style="min-height: 320px;">
        <div class="w-[70%] mx-auto px-4 sm:px-6 lg:px-8 py-16 flex flex-col items-center text-center">
            <span class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-5 border border-white/30">
                <span class="w-2 h-2 rounded-full bg-white"></span>
                Printbuka Blog
            </span>
            <h1 class="pb-display text-4xl sm:text-5xl text-white leading-tight mb-4">
                Guides, ideas &amp; updates<br>for better <span class="text-brand-200">print jobs.</span>
            </h1>
            <p class="text-white/80 text-lg max-w-xl">
                Practical tips on design, production, branding and gifting — from our team to yours.
            </p>
        </div>
    </section>

    {{-- Posts grid --}}
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <livewire:blog.post-list />
        </div>
    </section>

</main>
@endsection
