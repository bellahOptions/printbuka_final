<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SeoIndexingAndCachingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_outputs_seo_meta_and_discovery_links(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee(route('sitemap'), false)
            ->assertSee(route('llms'), false);
    }

    public function test_sitemap_includes_public_static_and_dynamic_urls(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Business Printing',
            'slug' => 'business-printing-'.Str::lower(Str::random(5)),
            'tag' => 'Print',
            'description' => 'Business printing products.',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Premium Business Card',
            'product_category_id' => $category->id,
            'service_type' => 'print',
            'moq' => 100,
            'price' => 2500,
            'short_description' => 'Premium print quality',
            'description' => 'Heavy card stock with clean finish.',
            'paper_type' => 'Art Card',
            'paper_size' => '85x55mm',
            'finishing' => 'Matt Lamination',
            'paper_density' => '350gsm',
            'is_active' => true,
        ]);

        $post = BlogPost::query()->create([
            'title' => 'How to Prepare Print-Ready Files',
            'slug' => 'print-ready-files-'.Str::lower(Str::random(5)),
            'excerpt' => 'Practical tips for preparing files.',
            'content' => '<p>Print-ready checklist</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('products.show', $product), false)
            ->assertSee(route('products.category', $category), false)
            ->assertSee(route('blog.show', $post), false);
    }

    public function test_llms_txt_endpoint_returns_indexing_hints(): void
    {
        $response = $this->get(route('llms'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('## Canonical', false)
            ->assertSee(route('products.index'), false)
            ->assertSee(route('sitemap'), false);
    }
}
