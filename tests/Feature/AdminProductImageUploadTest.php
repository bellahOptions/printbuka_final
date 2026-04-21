<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Support\LivewireSecureUploads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_featured_and_additional_images(): void
    {
        Storage::fake('public');
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                ...$this->validPayload(),
                'featured_image' => UploadedFile::fake()->image('featured.jpg'),
                'additional_images' => [
                    UploadedFile::fake()->image('gallery-1.jpg'),
                    UploadedFile::fake()->image('gallery-2.png'),
                ],
            ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status', 'Product created.');

        $product = Product::query()->latest('id')->firstOrFail();

        $this->assertNotNull($product->featured_image);
        $this->assertCount(2, (array) $product->additional_images);
        Storage::disk('public')->assertExists($product->featured_image);

        foreach ((array) $product->additional_images as $galleryImagePath) {
            Storage::disk('public')->assertExists((string) $galleryImagePath);
        }
    }

    public function test_admin_can_replace_existing_product_images_while_clearing_old_gallery(): void
    {
        Storage::fake('public');
        $admin = $this->adminUser();

        $oldFeatured = 'product-images/featured/old-featured.jpg';
        $oldGalleryOne = 'product-images/gallery/old-gallery-1.jpg';
        $oldGalleryTwo = 'product-images/gallery/old-gallery-2.jpg';

        Storage::disk('public')->put($oldFeatured, 'old-featured');
        Storage::disk('public')->put($oldGalleryOne, 'old-gallery-1');
        Storage::disk('public')->put($oldGalleryTwo, 'old-gallery-2');

        $product = Product::query()->create([
            ...$this->validPayload(),
            'featured_image' => $oldFeatured,
            'additional_images' => [$oldGalleryOne, $oldGalleryTwo],
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.update', $product), [
                '_method' => 'PUT',
                ...$this->validPayload([
                    'name' => 'Updated Product Name',
                ]),
                'remove_featured_image' => '1',
                'featured_image' => UploadedFile::fake()->image('new-featured.jpg'),
                'remove_additional_images' => '1',
                'additional_images' => [
                    UploadedFile::fake()->image('new-gallery.jpg'),
                ],
            ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status', 'Product updated.');

        $product->refresh();

        $this->assertSame('Updated Product Name', $product->name);
        $this->assertNotSame($oldFeatured, $product->featured_image);
        $this->assertCount(1, (array) $product->additional_images);

        Storage::disk('public')->assertMissing($oldFeatured);
        Storage::disk('public')->assertMissing($oldGalleryOne);
        Storage::disk('public')->assertMissing($oldGalleryTwo);
        Storage::disk('public')->assertExists((string) $product->featured_image);
        Storage::disk('public')->assertExists((string) ($product->additional_images[0] ?? null));
    }

    public function test_admin_can_create_product_images_from_livewire_secure_paths(): void
    {
        Storage::fake('public');
        $admin = $this->adminUser();

        $featuredPath = 'product-images/featured/livewire-featured.jpg';
        $galleryOne = 'product-images/gallery/livewire-gallery-1.jpg';
        $galleryTwo = 'product-images/gallery/livewire-gallery-2.jpg';

        Storage::disk('public')->put($featuredPath, 'featured');
        Storage::disk('public')->put($galleryOne, 'gallery-1');
        Storage::disk('public')->put($galleryTwo, 'gallery-2');

        $this->actingAs($admin)
            ->withSession([LivewireSecureUploads::SESSION_KEY => [$featuredPath, $galleryOne, $galleryTwo]])
            ->post(route('admin.products.store'), [
                ...$this->validPayload(),
                'featured_image_path' => $featuredPath,
                'additional_image_paths' => [$galleryOne, $galleryTwo],
            ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status', 'Product created.');

        $product = Product::query()->latest('id')->firstOrFail();

        $this->assertSame($featuredPath, $product->featured_image);
        $this->assertSame([$galleryOne, $galleryTwo], (array) $product->additional_images);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'service_type' => 'print',
            'name' => 'Sample Product',
            'moq' => 10,
            'price' => 4500,
            'short_description' => 'Quick sample product',
            'description' => 'Detailed sample product description.',
            'paper_type' => 'Art Card 300gsm',
            'paper_size' => 'A4',
            'finishing' => 'Gloss Lamination',
            'paper_density' => '300gsm',
            'is_active' => 1,
        ], $overrides);
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
