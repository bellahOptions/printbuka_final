<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use Database\Seeders\DefaultProductCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultProductCategorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_product_categories_and_sub_categories_are_seeded(): void
    {
        $this->seed(DefaultProductCategorySeeder::class);

        $giftItems = ProductCategory::query()
            ->where('name', 'Gift Items')
            ->firstOrFail();

        $this->assertNull($giftItems->parent_id);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Corporate Gifts',
            'parent_id' => $giftItems->id,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Casual Gifts',
            'parent_id' => $giftItems->id,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Anniversary Gifts',
            'parent_id' => $giftItems->id,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'UV DTF Products',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Custom DT',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Stationaries',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Promotionals',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Corporate',
            'parent_id' => null,
        ]);
    }
}
