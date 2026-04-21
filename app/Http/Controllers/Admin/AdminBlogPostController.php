<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $validated = $this->validated($request);

        BlogPost::query()->create([
            ...$this->payloadFromRequest($request, $validated),
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
        $validated = $this->validated($request, $blog);

        $blog->update($this->payloadFromRequest($request, $validated, $blog));

        return redirect()->route('admin.blog.index')->with('status', 'Blog post updated.');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $this->deleteStoredImagePath($blog->featured_image);

        foreach ((array) $blog->additional_images as $imagePath) {
            $this->deleteStoredImagePath(is_string($imagePath) ? $imagePath : null);
        }

        $blog->delete();

        return back()->with('status', 'Blog post deleted.');
    }

    private function validated(Request $request, ?BlogPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured_image' => ['nullable', 'url', 'max:1000'],
            'featured_image_file' => ['nullable', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'additional_images_files' => ['nullable', 'array', 'max:10'],
            'additional_images_files.*' => ['image', 'max:5120'],
            'remove_additional_images' => ['nullable', 'array'],
            'remove_additional_images.*' => ['integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payloadFromRequest(Request $request, array $validated, ?BlogPost $post = null): array
    {
        $baseSlug = Str::slug((string) ($validated['slug'] ?: $validated['title']));
        $content = $this->sanitizeHtml((string) $validated['content']);

        $payload = [
            'title' => (string) $validated['title'],
            'slug' => $this->resolveUniqueSlug($baseSlug, $post?->id),
            'excerpt' => filled($validated['excerpt'] ?? null) ? trim((string) $validated['excerpt']) : null,
            'content' => $content,
            'status' => (string) $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now())
                : null,
        ];

        $existingFeaturedImage = (string) ($post?->featured_image ?? '');
        $featuredImage = $existingFeaturedImage !== '' ? $existingFeaturedImage : null;

        if ($request->boolean('remove_featured_image')) {
            $this->deleteStoredImagePath($existingFeaturedImage !== '' ? $existingFeaturedImage : null);
            $featuredImage = null;
        }

        if ($request->hasFile('featured_image_file')) {
            $this->deleteStoredImagePath($existingFeaturedImage !== '' ? $existingFeaturedImage : null);
            $featuredImage = $request->file('featured_image_file')->store('blog/featured', 'public');
        } elseif ($request->filled('featured_image')) {
            $this->deleteStoredImagePath($existingFeaturedImage !== '' ? $existingFeaturedImage : null);
            $featuredImage = trim((string) $validated['featured_image']);
        }

        $payload['featured_image'] = $featuredImage;

        $additionalImages = collect((array) ($post?->additional_images ?? []))
            ->filter(fn ($value): bool => is_string($value) && filled($value))
            ->values();

        $removeIndexes = collect((array) ($validated['remove_additional_images'] ?? []))
            ->map(fn ($value): int => (int) $value)
            ->unique()
            ->sortDesc()
            ->values();

        foreach ($removeIndexes as $index) {
            $imagePath = $additionalImages->get($index);

            if (is_string($imagePath)) {
                $this->deleteStoredImagePath($imagePath);
            }

            $additionalImages->forget($index);
        }

        $newlyUploaded = collect();

        if ($request->hasFile('additional_images_files')) {
            foreach ((array) $request->file('additional_images_files') as $file) {
                if (! $file) {
                    continue;
                }

                $path = $file->store('blog/additional', 'public');
                $additionalImages->push($path);
                $newlyUploaded->push($path);
            }
        }

        if ($newlyUploaded->isNotEmpty()) {
            $inlineImagesHtml = $newlyUploaded
                ->map(fn (string $path): string => '<p><img src="'.e(Storage::disk('public')->url($path)).'" alt="Blog image" /></p>')
                ->implode('');

            $payload['content'] = $this->sanitizeHtml($payload['content'].' '.$inlineImagesHtml);
        }

        $payload['additional_images'] = $additionalImages->values()->all();

        return $payload;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $html) ?: '';

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><a><img><figure><figcaption><hr>';
        $sanitized = strip_tags($html, $allowedTags);

        $sanitized = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $sanitized) ?: '';
        $sanitized = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^\2]*\2/i', '$1="#"', $sanitized) ?: '';

        return trim($sanitized);
    }

    private function resolveUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'post';
        $counter = 1;

        while (
            BlogPost::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function deleteStoredImagePath(?string $path): void
    {
        if (! filled($path) || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
