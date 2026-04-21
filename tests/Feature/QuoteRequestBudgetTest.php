<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Support\LivewireSecureUploads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteRequestBudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_submit_quote_with_budget_for_review(): void
    {
        $product = Product::query()->create([
            'name' => 'Business Card',
            'moq' => 100,
            'price' => 5000,
            'short_description' => 'Premium business card print',
            'description' => 'Premium business card print',
            'paper_type' => 'Matte',
            'paper_size' => 'A6',
            'finishing' => 'Gloss',
            'paper_density' => '300gsm',
            'is_active' => true,
        ]);

        $this->post(route('quotes.store'), [
            'product_id' => $product->id,
            'job_type' => 'Business Card',
            'size_format' => 'A6',
            'quantity' => 500,
            'quote_budget' => 150000,
            'customer_name' => 'Jane Client',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '08012345678',
            'delivery_city' => 'Lagos',
            'delivery_address' => '10 Admiralty Way',
            'material_substrate' => 'Art Card',
            'finish_lamination' => 'Gloss Lamination',
            'artwork_notes' => 'Please deliver in one batch.',
        ])->assertSessionHasNoErrors();

        $order = Order::query()->firstOrFail();

        $this->assertSame('quote', $order->service_type);
        $this->assertSame('150000.00', $order->quote_budget);
    }

    public function test_quote_accepts_livewire_secure_image_assets(): void
    {
        Storage::fake('public');
        $product = $this->product();

        $imagePath = 'job-assets/images/quote-artwork.jpg';
        Storage::disk('public')->put($imagePath, 'image-content');

        $this->withSession([LivewireSecureUploads::SESSION_KEY => [$imagePath]])
            ->post(route('quotes.store'), $this->payload($product, [
                'job_asset_image_paths' => [$imagePath],
            ]))
            ->assertSessionHasNoErrors();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame($imagePath, $order->job_image_assets[0]['path'] ?? null);
    }

    public function test_quote_rejects_tampered_livewire_image_assets(): void
    {
        Storage::fake('public');
        $product = $this->product();

        $tamperedPath = 'job-assets/images/tampered-artwork.jpg';
        Storage::disk('public')->put($tamperedPath, 'image-content');

        $this->from(route('quotes.create'))
            ->post(route('quotes.store'), $this->payload($product, [
                'job_asset_image_paths' => [$tamperedPath],
            ]))
            ->assertRedirect(route('quotes.create'))
            ->assertSessionHasErrors('job_asset_image_paths');
    }

    public function test_quote_rejects_document_uploads_and_requires_external_links_instead(): void
    {
        Storage::fake('public');
        $product = $this->product();

        $this->from(route('quotes.create'))
            ->post(route('quotes.store'), $this->payload($product, [
                'job_asset_files' => [UploadedFile::fake()->create('artwork.zip', 256, 'application/zip')],
            ]))
            ->assertRedirect(route('quotes.create'))
            ->assertSessionHasErrors('asset_drive_links');
    }

    public function test_quote_accepts_allowed_external_drive_links_and_appends_them_to_notes(): void
    {
        $product = $this->product();
        $links = "https://drive.google.com/file/d/123/view\nhttps://1drv.ms/u/s!ABCDE";

        $this->post(route('quotes.store'), $this->payload($product, [
            'asset_drive_links' => $links,
            'artwork_notes' => 'Please review files.',
        ]))->assertSessionHasNoErrors();

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertStringContainsString('External asset links:', (string) $order->artwork_notes);
        $this->assertStringContainsString('drive.google.com', (string) $order->artwork_notes);
        $this->assertStringContainsString('1drv.ms', (string) $order->artwork_notes);
    }

    private function product(): Product
    {
        return Product::query()->create([
            'name' => 'Business Card',
            'moq' => 100,
            'price' => 5000,
            'short_description' => 'Premium business card print',
            'description' => 'Premium business card print',
            'paper_type' => 'Matte',
            'paper_size' => 'A6',
            'finishing' => 'Gloss',
            'paper_density' => '300gsm',
            'is_active' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'product_id' => $product->id,
            'job_type' => 'Business Card',
            'size_format' => 'A6',
            'quantity' => 500,
            'quote_budget' => 150000,
            'customer_name' => 'Jane Client',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '08012345678',
            'delivery_city' => 'Lagos',
            'delivery_address' => '10 Admiralty Way',
            'material_substrate' => 'Art Card',
            'finish_lamination' => 'Gloss Lamination',
            'artwork_notes' => 'Please deliver in one batch.',
        ], $overrides);
    }
}
