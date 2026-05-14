<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSeededProductCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_remove_seeded_products_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('product-images/featured/seeded.jpg', 'image');
        Storage::disk('public')->put('product-images/gallery/seeded.jpg', 'image');

        $seededProduct = $this->product([
            'name' => 'Seeded Card',
            'is_seeded' => true,
            'featured_image' => 'product-images/featured/seeded.jpg',
            'additional_images' => ['product-images/gallery/seeded.jpg'],
        ]);
        $realProduct = $this->product(['name' => 'Real Staff Product']);
        $admin = $this->admin('super_admin');

        $this->actingAs($admin)
            ->delete(route('admin.products.seeded.destroy'), [
                'confirmation' => 'DELETE SEEDED PRODUCTS',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', '1 seeded product(s) removed.');

        $this->assertDatabaseMissing('products', ['id' => $seededProduct->id]);
        $this->assertDatabaseHas('products', ['id' => $realProduct->id]);
        Storage::disk('public')->assertMissing('product-images/featured/seeded.jpg');
        Storage::disk('public')->assertMissing('product-images/gallery/seeded.jpg');
    }

    public function test_super_admin_can_include_legacy_unmarked_catalog_when_confirmed(): void
    {
        $this->product(['name' => 'Legacy Seed One']);
        $this->product(['name' => 'Legacy Seed Two']);
        $admin = $this->admin('super_admin');

        $this->actingAs($admin)
            ->delete(route('admin.products.seeded.destroy'), [
                'confirmation' => 'DELETE SEEDED PRODUCTS',
                'include_legacy_catalog' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', '2 catalog product(s) removed.');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_non_super_admin_cannot_remove_seeded_products(): void
    {
        $product = $this->product(['is_seeded' => true]);
        $admin = $this->admin('admin');

        $this->actingAs($admin)
            ->delete(route('admin.products.seeded.destroy'), [
                'confirmation' => 'DELETE SEEDED PRODUCTS',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_cleanup_requires_typed_confirmation(): void
    {
        $product = $this->product(['is_seeded' => true]);
        $admin = $this->admin('super_admin');

        $this->actingAs($admin)
            ->from(route('admin.products.index'))
            ->delete(route('admin.products.seeded.destroy'), [
                'confirmation' => 'DELETE PRODUCTS',
            ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasErrors('confirmation');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    private function product(array $attributes = []): Product
    {
        return Product::query()->create(array_replace([
            'name' => 'Product',
            'moq' => 1,
            'price' => 1000,
            'price_unavailable' => false,
            'is_seeded' => false,
            'short_description' => 'Short description',
            'description' => 'Long product description',
            'paper_type' => 'Art Card',
            'paper_size' => 'A5',
            'finishing' => 'Matte',
            'paper_density' => '300gsm',
            'is_active' => true,
        ], $attributes));
    }

    private function admin(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
