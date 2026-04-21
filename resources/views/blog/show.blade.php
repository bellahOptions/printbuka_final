@extends('layouts.theme')

@section('title', $post->title . ' | Printbuka Blog')

@section('content')
<main class="bg-base-100 py-12">
    <article class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('blog') }}" class="text-sm font-black text-pink-700 hover:text-pink-800">Back to blog</a>

        <header class="mt-5">
            <p class="text-xs font-black uppercase tracking-wide text-slate-500">
                {{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}
            </p>
            <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950">{{ $post->title }}</h1>
            @if($post->excerpt)
                <p class="mt-4 text-lg text-slate-600">{{ $post->excerpt }}</p>
            @endif
        </header>

        @if($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="mt-8 h-[360px] w-full rounded-2xl object-cover" />
        @endif

        <div class="prose prose-slate mt-8 max-w-none">
            {!! $safeContent !!}
        </div>

        @if(!empty($post->additionalImageUrls()))
            <section class="mt-10">
                <h2 class="text-2xl font-black text-slate-950">Gallery</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach($post->additionalImageUrls() as $imageUrl)
                        <img src="{{ $imageUrl }}" alt="Blog image" class="h-52 w-full rounded-xl object-cover" />
                    @endforeach
                </div>
            </section>
        @endif
    </article>

    @if($relatedPosts->isNotEmpty())
        <section class="mx-auto mt-14 max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-black text-slate-950">Related Articles</h2>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                @foreach($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related) }}" class="rounded-xl border border-slate-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-md">
                        <p class="text-xs font-bold uppercase tracking-wide text-pink-600">{{ $related->published_at?->format('M j, Y') ?? $related->created_at->format('M j, Y') }}</p>
                        <h3 class="mt-2 text-lg font-black text-slate-950">{{ $related->title }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $related->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($related->content), 110) }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
