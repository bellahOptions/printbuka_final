<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show(BlogPost $post): View
    {
        abort_unless(
            $post->status === 'published'
                && (! $post->published_at || $post->published_at->lte(now())),
            404
        );

        $relatedPosts = BlogPost::query()
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->latest('published_at')
            ->limit(3)
            ->get();

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
}
