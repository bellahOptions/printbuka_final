<?php

namespace Tests\Feature;

use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\LivewireSecureUploads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderDeliveryAddressSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.paystack.secret_key', '');
    }

    public function test_selected_saved_delivery_address_is_used_for_order_delivery_fields(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'first_name' => 'Ngozi',
            'last_name' => 'Okafor',
            'phone' => '08011112222',
            'email' => 'ngozi@example.com',
        ]);

        DeliveryAddress::query()->create([
            'user_id' => $user->id,
            'label' => 'Home',
            'recipient_name' => 'Ngozi Okafor',
            'phone' => '08011112222',
            'city' => 'Lagos',
            'address' => '4 Home Street',
            'landmark' => null,
            'is_default' => true,
        ]);
        $office = DeliveryAddress::query()->create([
            'user_id' => $user->id,
            'label' => 'Office',
            'recipient_name' => 'Ngozi Okafor',
            'phone' => '08011112222',
            'city' => 'Abuja',
            'address' => '18 Office Crescent',
            'landmark' => 'Opposite bank',
            'is_default' => false,
        ]);

        $product = $this->createProduct();

        $response = $this->actingAs($user)->post(
            route('orders.store', $product),
            $this->orderPayload([
                'delivery_city' => 'Manual City',
                'delivery_address' => 'Manual Address',
                'delivery_address_id' => $office->id,
            ])
        );

        $order = Order::query()->latest('id')->firstOrFail();

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('orders.success', $order));
        $this->assertSame('Abuja', $order->delivery_city);
        $this->assertSame('18 Office Crescent', $order->delivery_address);
        $this->assertSame('Ngozi Okafor', $order->customer_name);
        $this->assertSame('ngozi@example.com', $order->customer_email);
        $this->assertSame('08011112222', $order->customer_phone);
        $this->assertDatabaseHas('invoices', ['order_id' => $order->id]);
    }

    public function test_user_cannot_submit_someone_elses_delivery_address_for_checkout(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $anotherUser = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $foreignAddress = DeliveryAddress::query()->create([
            'user_id' => $anotherUser->id,
            'label' => 'Foreign',
            'recipient_name' => 'Other User',
            'phone' => '08000000000',
            'city' => 'Kano',
            'address' => '9 Secret Street',
            'landmark' => null,
            'is_default' => true,
        ]);

        $product = $this->createProduct();

        $this->actingAs($user)
            ->from(route('orders.create', $product))
            ->post(route('orders.store', $product), $this->orderPayload([
                'delivery_address_id' => $foreignAddress->id,
            ]))
            ->assertRedirect(route('orders.create', $product))
            ->assertSessionHasErrors('delivery_address_id');
    }

    public function test_order_accepts_livewire_secure_image_paths_as_job_assets(): void
    {
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $product = $this->createProduct();
        $imagePath = 'job-assets/images/livewire-artwork.jpg';
        Storage::disk('public')->put($imagePath, 'artwork');

        $this->actingAs($user)
            ->withSession([LivewireSecureUploads::SESSION_KEY => [$imagePath]])
            ->post(route('orders.store', $product), $this->orderPayload([
                'job_asset_image_paths' => [$imagePath],
            ]))
            ->assertSessionHasNoErrors();

        $order = Order::query()->latest('id')->firstOrFail();
        $assets = (array) $order->job_image_assets;

        $this->assertCount(1, $assets);
        $this->assertSame($imagePath, $assets[0]['path'] ?? null);
    }

    public function test_order_rejects_document_uploads_for_customers(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $product = $this->createProduct();

        $this->actingAs($user)
            ->from(route('orders.create', $product))
            ->post(route('orders.store', $product), $this->orderPayload([
                'job_asset_files' => [UploadedFile::fake()->create('design.pdf', 200, 'application/pdf')],
            ]))
            ->assertRedirect(route('orders.create', $product))
            ->assertSessionHasErrors('job_asset_files.0');
    }

    public function test_order_accepts_allowed_external_drive_links_and_adds_them_to_notes(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('orders.store', $product), $this->orderPayload([
                'asset_drive_links' => "https://www.mediafire.com/file/abc123/sample.zip/file\nhttps://dropbox.com/s/xyz/file.pdf",
            ]))
            ->assertSessionHasNoErrors();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertStringContainsString('External asset links:', (string) $order->artwork_notes);
        $this->assertStringContainsString('mediafire.com', (string) $order->artwork_notes);
        $this->assertStringContainsString('dropbox.com', (string) $order->artwork_notes);
    }

    private function createProduct(): Product
    {
        return Product::query()->create([
            'name' => 'Business Cards',
            'moq' => 100,
            'price' => 2000,
            'short_description' => 'Premium business card printing',
            'description' => 'Premium business card printing for teams.',
            'paper_type' => 'Matte',
            'material_price_options' => [
                ['label' => 'Matte', 'price' => 0],
            ],
            'paper_size' => 'A4',
            'size_price_options' => [
                ['label' => 'A4', 'price' => 0],
            ],
            'finishing' => 'Gloss',
            'finish_price_options' => [
                ['label' => 'Gloss', 'price' => 0],
            ],
            'delivery_price_options' => [
                ['label' => 'Pickup', 'price' => 0],
                ['label' => 'Deliver to address', 'price' => 1000],
            ],
            'paper_density' => '350gsm',
            'is_active' => true,
        ]);
    }

    private function orderPayload(array $overrides = []): array
    {
        return [
            'quantity' => 100,
            'size_format' => 'A4',
            'material_substrate' => 'Matte',
            'finish_lamination' => 'Gloss',
            'delivery_method' => 'Deliver to address',
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '08099998888',
            'delivery_city' => 'Lagos',
            'delivery_address' => '2 Manual Street',
            'artwork_notes' => 'Please print cleanly.',
            ...$overrides,
        ];
    }
}
