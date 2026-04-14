<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog.index', [
            'posts' => BlogPost::query()->with('author')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.form', [
            'post' => new BlogPost(['status' => 'draft']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BlogPost::query()->create([
            ...$this->validated($request),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.blog.index')->with('status', 'Blog post created.');
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.blog.form', [
            'post' => $blog,
        ]);
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $blog->update($this->validated($request, $blog));

        return redirect()->route('admin.blog.index')->with('status', 'Blog post updated.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $blog->delete();

        return back()->with('status', 'Blog post deleted.');
    }

    private function validated(Request $request, ?BlogPost $post = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured_image' => ['nullable', 'string', 'max:1000'],
            'published_at' => ['nullable', 'date'],
        ]);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['published_at'] = $validated['status'] === 'published'
            ? ($validated['published_at'] ?? now())
            : null;

        return $validated;
    }
}
