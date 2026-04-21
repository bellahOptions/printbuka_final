<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicCategoryLinkWiringTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_links_only_to_public_categories_and_hides_placeholder_footer_items(): void
    {
        $visibleCategory = $this->category('Branded Gifts');
        $this->activeProduct($visibleCategory);

        $inactiveCategory = $this->category('Inactive Category', ['is_active' => false]);
        $this->activeProduct($inactiveCategory);

        $emptyCategory = $this->category('Empty Category');

        $parentWithChildProducts = $this->category('Parent Category');
        $childWithProducts = $this->category('Parent Child Category', [
            'parent_id' => $parentWithChildProducts->id,
        ]);
        $this->activeProduct($childWithProducts);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee(route('products.category', $visibleCategory))
            ->assertSee(route('products.category', $parentWithChildProducts))
            ->assertDontSee(route('products.category', $inactiveCategory))
            ->assertDontSee(route('products.category', $emptyCategory))
            ->assertDontSee('href="#"', false)
            ->assertDontSeeText('About Printbuka')
            ->assertDontSeeText('Contact Us')
            ->assertDontSeeText('Artwork Guide');
    }

    public function test_categories_index_uses_same_public_visibility_rules(): void
    {
        $visibleCategory = $this->category('Business Cards');
        $this->activeProduct($visibleCategory);

        $categoryWithoutProducts = $this->category('No Product Category');

        $inactiveCategory = $this->category('Hidden Category', ['is_active' => false]);
        $this->activeProduct($inactiveCategory);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSeeText('Business Cards')
            ->assertDontSeeText($categoryWithoutProducts->name)
            ->assertDontSeeText($inactiveCategory->name);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function category(string $name, array $overrides = []): ProductCategory
    {
        return ProductCategory::query()->create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'tag' => 'Category',
            'description' => $name.' description',
            'is_active' => true,
            ...$overrides,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function activeProduct(ProductCategory $category, array $overrides = []): Product
    {
        return Product::query()->create([
            'name' => 'Product '.Str::upper(Str::random(5)),
            'product_category_id' => $category->id,
            'moq' => 10,
            'price' => 1000,
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'paper_type' => 'Matte',
            'paper_size' => 'A4',
            'finishing' => 'Gloss',
            'paper_density' => '300gsm',
            'is_active' => true,
            ...$overrides,
        ]);
    }
}
