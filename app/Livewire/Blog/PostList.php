<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PostList extends Component
{
    public int $perPage = 9;

    public int $page = 1;

    public bool $hasMore = true;

    public int $totalResults = 0;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->publishedPostsQuery();

        $this->totalResults = (clone $query)->count();

        $posts = (clone $query)
            ->limit($this->page * $this->perPage)
            ->get();

        $this->hasMore = $posts->count() < $this->totalResults;

        return view('livewire.blog.post-list', [
            'posts' => $posts,
        ]);
    }

    private function publishedPostsQuery(): Builder
    {
        return BlogPost::query()
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->latest('id');
    }
}
