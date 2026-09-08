<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\SafeCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('blog.index');
    }

    public function show(BlogPost $post): View
    {
        abort_unless(
            $post->status === 'published'
                && (! $post->published_at || $post->published_at->lte(now())),
            404
        );

        $relatedPostIds = SafeCache::remember("blog:show:{$post->id}:related-post-ids:v1", now()->addMinutes(5), function () use ($post): array {
            return $this->publishedPostsQuery()
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->pluck('id')
                ->all();
        });

        $relatedPosts = $relatedPostIds === []
            ? collect()
            : BlogPost::query()
                ->whereIn('id', $relatedPostIds)
                ->get()
                ->sortBy(function (BlogPost $related) use ($relatedPostIds): int {
                    $index = array_search($related->id, $relatedPostIds, true);

                    return is_int($index) ? $index : PHP_INT_MAX;
                })
                ->values();

        return view('blog.show', [
            'post' => $post,
            'safeContent' => $this->sanitizeHtmlForRender((string) $post->content),
            'relatedPosts' => $relatedPosts,
        ]);
    }

    private function sanitizeHtmlForRender(string $html): string
    {
        $html = preg_replace('/<(script|style)\\b[^>]*>.*?<\\/\\1>/is', '', $html) ?: '';
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><a><img><figure><figcaption><hr>';
        $sanitized = strip_tags($html, $allowedTags);
        $sanitized = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $sanitized) ?: '';

        return preg_replace("/(href|src)\\s*=\\s*([\"'])\\s*javascript:[^\\2]*\\2/i", '$1="#"', $sanitized) ?: '';
    }

    private function publishedPostsQuery(): Builder
    {
        return BlogPost::query()
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->latest('id');
    }
}
