@extends('layouts.new-app')

@section('content')
<main>

    {{-- Hero: featured image with overlay --}}
    <section class="relative overflow-hidden flex items-end" style="min-height: 480px;">

        {{-- Background --}}
        @if($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}"
                 alt="{{ $post->title }}"
                 class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-[#EC268F]"></div>
        @endif

        {{-- Gradient --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/60 to-black/10"></div>

        {{-- Content --}}
        <div class="relative w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <a href="{{ route('blog') }}"
               class="inline-flex items-center gap-1.5 text-white/60 hover:text-white text-sm font-bold mb-6 transition-colors">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Back to Blog
            </a>

            <div class="flex items-center gap-3 mb-4 flex-wrap">
                <span class="bg-[#EC268F] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                    {{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}
                </span>
                @if($post->author)
                    <span class="flex items-center gap-1.5 text-white/60 text-xs font-bold">
                        <span class="w-5 h-5 rounded-full bg-pink-600 flex items-center justify-center text-white text-[9px] font-black">
                            {{ strtoupper(substr($post->author->name, 0, 2)) }}
                        </span>
                        {{ $post->author->name }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <p class="mt-4 text-white/70 text-lg max-w-2xl leading-relaxed">{{ $post->excerpt }}</p>
            @endif
        </div>
    </section>

    {{-- Article body --}}
    <section class="py-14 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-[1fr_300px] gap-12 items-start">

                {{-- Main content --}}
                <article>
                    <div class="prose prose-slate prose-lg max-w-none
                                prose-headings:font-black prose-headings:text-slate-900
                                prose-a:text-[#EC268F] prose-a:no-underline hover:prose-a:underline
                                prose-img:rounded-2xl prose-img:shadow-md
                                prose-blockquote:border-l-[#EC268F] prose-blockquote:bg-pink-50 prose-blockquote:py-1 prose-blockquote:rounded-r-xl">
                        {!! $safeContent !!}
                    </div>

                    {{-- Additional image gallery --}}
                    @if(!empty($post->additionalImageUrls()))
                        <div class="mt-12 pt-10 border-t border-gray-100">
                            <h2 class="text-2xl font-black text-slate-900 mb-5">Gallery</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach($post->additionalImageUrls() as $url)
                                    <img src="{{ $url }}"
                                         alt="Blog image"
                                         class="h-56 w-full rounded-2xl object-cover shadow-sm">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Author card --}}
                    @if($post->author)
                        <div class="mt-12 p-6 rounded-2xl border border-gray-100 bg-slate-50 flex items-center gap-5">
                            <div class="w-14 h-14 rounded-full bg-[#EC268F] flex items-center justify-center text-white font-black text-lg shrink-0">
                                {{ strtoupper(substr($post->author->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-0.5">Written by</p>
                                <p class="text-base font-black text-slate-900">{{ $post->author->name }}</p>
                                <p class="text-sm text-slate-500">{{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    @endif
                </article>

                {{-- Sidebar --}}
                <aside class="space-y-6 lg:sticky lg:top-24">

                    {{-- Share --}}
                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Share this article</p>
                        <div class="flex gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                               target="_blank" rel="noopener"
                               class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-black transition-colors">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                X / Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($post->title.' '.url()->current()) }}"
                               target="_blank" rel="noopener"
                               class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition-colors">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="rounded-2xl bg-[#EC268F] p-5 text-white">
                        <p class="text-xs font-black uppercase tracking-widest text-pink-200 mb-2">Ready to print?</p>
                        <p class="font-black text-lg leading-snug mb-4">Get quality prints delivered to your door.</p>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-1.5 bg-white text-[#EC268F] text-sm font-black px-4 py-2.5 rounded-xl hover:bg-pink-50 transition-colors">
                            Browse Products <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>

                </aside>

            </div>
        </div>
    </section>

    {{-- Related articles --}}
    @if($relatedPosts->isNotEmpty())
        <section class="py-14 px-4 sm:px-6 lg:px-8 bg-slate-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-2xl font-black text-slate-900 mb-8">Related Articles</h2>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach($relatedPosts as $related)
                        @php $relImg = $related->featuredImageUrl() ?? 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80'; @endphp
                        <article class="group relative rounded-2xl overflow-hidden" style="height: 280px;">
                            <img src="{{ $relImg }}" alt="{{ $related->title }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                            <div class="absolute top-4 left-4">
                                <span class="bg-[#EC268F] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                    {{ $related->published_at?->format('M j, Y') ?? $related->created_at->format('M j, Y') }}
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-5 group-hover:opacity-0 group-hover:translate-y-2 transition-all duration-300">
                                <h3 class="text-white font-black text-sm line-clamp-2">{{ $related->title }}</h3>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-400 ease-out rounded-t-2xl p-5">
                                <h3 class="text-slate-900 font-black text-sm mb-2 line-clamp-2">{{ $related->title }}</h3>
                                <p class="text-slate-500 text-xs line-clamp-2 mb-3">{{ $related->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($related->content), 90) }}</p>
                                <a href="{{ route('blog.show', $related) }}"
                                   class="inline-flex items-center gap-1 bg-[#EC268F] hover:bg-pink-700 text-white text-xs font-black px-3 py-1.5 rounded-lg transition-colors">
                                    Read <x-heroicon-o-arrow-right class="w-3 h-3" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</main>
@endsection
