@extends('layouts.theme')

@section('title', 'Printbuka Blog')

@section('content')
<main class="bg-base-100 py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 rounded-2xl bg-slate-950 px-6 py-10 text-white">
            <p class="text-xs font-black uppercase tracking-wider text-cyan-300">Printbuka Blog</p>
            <h1 class="mt-3 text-4xl font-black">Guides, updates and ideas for better print jobs.</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-300">Explore practical tips for design, production, branding, and gifts.</p>
        </div>

        @if ($posts->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @php
                        $image = $post->featuredImageUrl() ?? 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('blog.show', $post) }}" class="block">
                            <img src="{{ $image }}" alt="{{ $post->title }}" class="h-52 w-full object-cover" />
                        </a>
                        <div class="p-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-pink-600">
                                {{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}
                            </p>
                            <h2 class="mt-2 text-xl font-black text-slate-950">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-pink-600">{{ $post->title }}</a>
                            </h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
                            <a href="{{ route('blog.show', $post) }}" class="mt-4 inline-flex text-sm font-black text-pink-700 hover:text-pink-800">Read article</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-xl font-black text-slate-900">No blog posts are published yet.</p>
                <p class="mt-2 text-sm text-slate-500">Check back shortly for fresh updates.</p>
            </div>
        @endif
    </div>
</main>
@endsection
