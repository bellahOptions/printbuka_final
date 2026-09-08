<div>
    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="px-5 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Post</th>
                    <th class="px-5 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Status</th>
                    <th class="px-5 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Author</th>
                    <th class="px-5 py-4 text-xs font-black uppercase tracking-widest text-slate-400">Published</th>
                    <th class="px-5 py-4 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($posts as $post)
                    <tr wire:key="blog-post-row-{{ $post->id }}" class="hover:bg-slate-50 transition-colors">
                        {{-- Post --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                @if($post->featuredImageUrl())
                                    <img src="{{ $post->featuredImageUrl() }}"
                                         alt="{{ $post->title }}"
                                         class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-100">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-pink-50 border border-slate-100 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-document-text class="w-5 h-5 text-pink-300" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 truncate max-w-xs">{{ $post->title }}</p>
                                    <p class="text-xs text-slate-400 truncate max-w-xs mt-0.5">/blog/{{ $post->slug }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-black px-2.5 py-1 rounded-full border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-xs font-black px-2.5 py-1 rounded-full border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    Draft
                                </span>
                            @endif
                        </td>

                        {{-- Author --}}
                        <td class="px-5 py-4">
                            @if($post->author)
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-pink-600 flex items-center justify-center text-white text-[9px] font-black shrink-0">
                                        {{ strtoupper(substr($post->author->name, 0, 2)) }}
                                    </span>
                                    <span class="text-sm text-slate-700 font-semibold">{{ $post->author->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Published date --}}
                        <td class="px-5 py-4 text-sm text-slate-500">
                            {{ $post->published_at?->format('M j, Y') ?? '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post) }}"
                                       target="_blank"
                                       class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-400 transition-colors"
                                       title="View live post">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                    </a>
                                @endif
                                <a href="{{ route('admin.blog.edit', $post) }}"
                                   class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-pink-600 text-white text-xs font-black px-3 py-1.5 rounded-lg transition-colors">
                                    <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                    Edit
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Delete &quot;{{ addslashes($post->title) }}&quot;? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-red-600 hover:text-white text-slate-600 text-xs font-black px-3 py-1.5 rounded-lg transition-colors">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-document-text class="w-7 h-7 text-slate-300" />
                            </div>
                            <p class="font-black text-slate-700">No posts yet.</p>
                            <p class="text-sm text-slate-400 mt-1">Create your first blog post to get started.</p>
                            <a href="{{ route('admin.blog.create') }}"
                               class="inline-flex items-center gap-1.5 mt-4 bg-pink-600 hover:bg-pink-700 text-white text-sm font-black px-4 py-2 rounded-xl transition-colors">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                Create Post
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs font-bold text-slate-400">
        Showing {{ number_format($posts->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('post', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more posts as you scroll...
            </span>
            <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                Loading more posts...
            </span>
            <button type="button" wire:click="loadMore" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 transition hover:border-pink-400 hover:text-pink-700">
                Load More
            </button>
        </div>
    @endif
</div>
