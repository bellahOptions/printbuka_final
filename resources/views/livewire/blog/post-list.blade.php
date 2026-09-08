<div>
    @if($posts->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                @php
                    $image = $post->featuredImageUrl()
                        ?? 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80';
                @endphp

                {{-- Magazine-style card --}}
                <article class="group relative rounded-2xl overflow-hidden cursor-pointer" style="height: 340px;">

                    {{-- Full-bleed image --}}
                    <img src="{{ $image }}"
                         alt="{{ $post->title }}"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80';">

                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

                    {{-- Date pill --}}
                    <div class="absolute top-4 left-4">
                        <span class="bg-[#EC268F] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                            {{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}
                        </span>
                    </div>

                    {{-- Arrow icon --}}
                    <a href="{{ route('blog.show', $post) }}"
                       class="absolute top-4 right-4 w-8 h-8 bg-white/15 backdrop-blur-sm border border-white/25 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 hover:bg-white hover:text-slate-900 transition-all duration-300"
                       aria-label="Read {{ $post->title }}">
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                    </a>

                    {{-- Resting: title --}}
                    <div class="absolute bottom-0 left-0 right-0 p-5 group-hover:opacity-0 group-hover:translate-y-2 transition-all duration-300">
                        <h2 class="text-white font-black text-base leading-snug line-clamp-2">{{ $post->title }}</h2>
                        @if($post->author)
                            <p class="text-white/50 text-xs mt-1">By {{ $post->author->name }}</p>
                        @endif
                    </div>

                    {{-- Hover panel --}}
                    <div class="absolute bottom-0 left-0 right-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-400 ease-out rounded-t-2xl p-5">
                        <h2 class="text-slate-900 font-black text-sm leading-snug mb-2 line-clamp-2">{{ $post->title }}</h2>
                        <p class="text-slate-500 text-xs leading-relaxed mb-4 line-clamp-2">
                            {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}
                        </p>
                        <div class="flex items-center justify-between gap-3">
                            @if($post->author)
                                <span class="text-xs font-bold text-slate-400">{{ $post->author->name }}</span>
                            @endif
                            <a href="{{ route('blog.show', $post) }}"
                               class="inline-flex items-center gap-1 bg-[#EC268F] hover:bg-pink-700 text-white text-xs font-black px-4 py-2 rounded-lg transition-colors shrink-0 ml-auto">
                                Read <x-heroicon-o-arrow-right class="w-3 h-3" />
                            </a>
                        </div>
                    </div>

                </article>
            @endforeach
        </div>

        @if($this->hasMore)
            <div class="mt-10 flex flex-col items-center gap-3" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more articles as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more articles...
                </span>
                <button type="button" wire:click="loadMore" class="btn btn-outline border-slate-300 hover:border-pink-400 hover:text-pink-700 font-black">
                    Load More
                </button>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-center">
                <p class="text-sm font-black text-slate-700">You have reached the end of the blog.</p>
            </div>
        @endif

    @else
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-pink-50 flex items-center justify-center mx-auto mb-4">
                <x-heroicon-o-document-text class="w-8 h-8 text-pink-300" />
            </div>
            <p class="text-xl font-black text-slate-900">No articles published yet.</p>
            <p class="text-sm mt-2 text-slate-500">Check back shortly for fresh guides and updates.</p>
        </div>
    @endif
</div>
