<div>
    {{-- Table --}}
    <div class="pb-table-wrapper">
        <table class="pb-table pb-table--cards w-full md:min-w-[700px]">
            <thead>
                <tr>
                    <th>Post</th>
                    <th>Status</th>
                    <th>Author</th>
                    <th>Published</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr wire:key="blog-post-row-{{ $post->id }}">
                        {{-- Post --}}
                        <td data-label="Post">
                            <div class="flex items-center gap-4">
                                @if($post->featuredImageUrl())
                                    <img src="{{ $post->featuredImageUrl() }}"
                                         alt="{{ $post->title }}"
                                         class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-100">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-brand-50 border border-slate-100 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-document-text class="w-5 h-5 text-brand-300" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate max-w-xs">{{ $post->title }}</p>
                                    <p class="text-xs text-slate-400 truncate max-w-xs mt-0.5">/blog/{{ $post->slug }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td data-label="Status">
                            @if($post->status === 'published')
                                <span class="pb-badge pb-badge-success">Published</span>
                            @else
                                <span class="pb-badge pb-badge-warning">Draft</span>
                            @endif
                        </td>

                        {{-- Author --}}
                        <td data-label="Author">
                            @if($post->author)
                                <div class="flex items-center gap-2">
                                    <span class="pb-avatar pb-avatar-sm shrink-0">
                                        <span class="pb-avatar-fallback bg-brand-600 text-white">
                                            {{ strtoupper(substr($post->author->name, 0, 2)) }}
                                        </span>
                                    </span>
                                    <span class="text-sm text-slate-700 font-semibold">{{ $post->author->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Published date --}}
                        <td data-label="Published" class="text-sm text-slate-500">
                            {{ $post->published_at?->format('M j, Y') ?? '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post) }}"
                                       target="_blank"
                                       class="pb-btn pb-btn-icon pb-btn-outline"
                                       title="View live post">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                    </a>
                                @endif
                                <a href="{{ route('admin.blog.edit', $post) }}" class="pb-btn pb-btn-sm pb-btn-ink">
                                    <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                    Edit
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Delete &quot;{{ addslashes($post->title) }}&quot;? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pb-btn pb-btn-sm pb-btn-secondary">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="pb-empty">
                                <x-heroicon-o-document-text class="pb-empty-icon w-10 h-10" />
                                <p class="pb-empty-title">No posts yet.</p>
                                <p class="pb-empty-body">Create your first blog post to get started.</p>
                                <a href="{{ route('admin.blog.create') }}" class="pb-btn pb-btn-md pb-btn-primary mt-2">
                                    <x-heroicon-o-plus class="w-4 h-4" />
                                    Create Post
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs font-medium text-slate-400">
        Showing {{ number_format($posts->count()) }} of {{ number_format($totalCount) }} {{ Str::plural('post', $totalCount) }}
    </p>

    @if ($hasMore)
        <div class="flex flex-col items-center gap-3 py-4" wire:poll.visible="loadMore">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                Loading more posts as you scroll...
            </span>
            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600" wire:loading wire:target="loadMore">
                Loading more posts...
            </span>
            <button type="button" wire:click="loadMore" class="pb-btn pb-btn-md pb-btn-outline">
                Load More
            </button>
        </div>
    @endif
</div>
