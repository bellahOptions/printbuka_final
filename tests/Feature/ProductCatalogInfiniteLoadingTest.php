<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogInfiniteLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_uses_infinite_loading_and_hides_specialist_sections(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Business Printing',
            'slug' => 'business-printing',
            'is_active' => true,
        ]);

        foreach (range(1, 18) as $index) {
            Product::query()->create([
                'product_category_id' => $category->id,
                'service_type' => 'print',
                'name' => 'Premium Business Cards '.$index,
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
        }

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSeeText('All products with smart filters and infinite loading.');
        $response->assertSeeText('Loading more products as you scroll...');
        $response->assertDontSeeText('UV DTF Products');
        $response->assertDontSeeText('Laser Engraving Products');
        $response->assertDontSee('?page=2');
    }
}
