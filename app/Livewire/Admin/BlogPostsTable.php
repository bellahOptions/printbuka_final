<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class BlogPostsTable extends Component
{
    public int $perPage = 20;

    public int $page = 1;

    public bool $hasMore = true;

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
    }

    public function render(): View
    {
        $query = $this->tableQuery();
        $totalCount = (clone $query)->count();
        $posts = (clone $query)->limit($this->page * $this->perPage)->get();
        $this->hasMore = $posts->count() < $totalCount;

        return view('livewire.admin.blog-posts-table', [
            'posts' => $posts,
            'totalCount' => $totalCount,
        ]);
    }

    private function tableQuery(): Builder
    {
        return BlogPost::query()->with('author')->latest();
    }
}
