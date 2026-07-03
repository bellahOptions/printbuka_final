<?php

namespace Tests\Feature;

use App\Livewire\Services\DirectImageOrderForm;
use App\Models\PriceListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DirectImageOrderFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_requirement_on_direct_image_service_page(): void
    {
        $this->get(route('services.show', 'direct-image-printing'))
            ->assertOk()
            ->assertSee('Sign in to place a Direct Image order');
    }

    public function test_direct_image_livewire_form_calculates_and_redirects_to_paystack(): void
    {
        Mail::fake();
        Storage::fake('public');

        config()->set('services.paystack.secret_key', 'sk_test_123');

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/direct-image-reference',
                ],
            ], 200),
        ]);

        PriceListItem::query()->create(['category' => 'service', 'service_slug' => 'direct-image-printing', 'label' => 'Direct Image Printing', 'price' => 2000]);
        PriceListItem::query()->create(['category' => 'service', 'service_slug' => 'direct-image-printing', 'component_group' => 'fee', 'component_key' => 'design_fee', 'label' => 'Design Fee', 'price' => 1500]);
        PriceListItem::query()->create(['category' => 'service', 'service_slug' => 'direct-image-printing', 'component_group' => 'fee', 'component_key' => 'delivery_fee', 'label' => 'Delivery Fee', 'price' => 1000]);
        PriceListItem::query()->create(['category' => 'product', 'component_group' => 'material', 'label' => 'Matte', 'price' => 500]);
        PriceListItem::query()->create(['category' => 'product', 'component_group' => 'material', 'label' => 'Gloss', 'price' => 700]);
        PriceListItem::query()->create(['category' => 'product', 'component_group' => 'size', 'label' => 'A4', 'price' => 300]);
        PriceListItem::query()->create(['category' => 'product', 'component_group' => 'size', 'label' => 'A3', 'price' => 450]);

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone' => '08010000000',
        ]);

        $service = [
            'slug' => 'direct-image-printing',
            'name' => 'Direct Image Printing',
        ];

        Livewire::actingAs($user)
            ->test(DirectImageOrderForm::class, ['service' => $service])
            ->set('quantity', 2)
            ->set('paper_type', 'Matte')
            ->set('paper_size', 'A4')
            ->set('has_design', 'no')
            ->set('design_brief', 'Need a simple one-color brand concept.')
            ->set('delivery_type', 'delivery')
            ->set('delivery_city', 'Lagos')
            ->set('delivery_address', '12 Allen Avenue, Ikeja')
            ->set('customer_name', $user->displayName())
            ->set('customer_email', $user->email)
            ->set('customer_phone', '08010000000')
            ->call('submit')
            ->assertRedirect('https://checkout.paystack.com/direct-image-reference');

        $this->assertDatabaseHas('orders', [
            'service_type' => 'service:direct-image-printing',
            'job_type' => 'Direct Image Printing',
            'quantity' => 2,
            'material_substrate' => 'Matte',
            'size_format' => 'A4',
            'delivery_method' => 'Delivery Address',
            'delivery_city' => 'Lagos',
            'total_price' => 8100,
        ]);

        $this->assertDatabaseHas('invoices', [
            'payment_gateway' => 'paystack',
            'total_amount' => 8100,
        ]);
    }

    public function test_direct_image_post_route_requires_authentication(): void
    {
        $this->post(route('services.orders.store', 'direct-image-printing'), [
            'quantity' => 1,
            'delivery_method' => 'Client Pickup',
            'customer_name' => 'Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '08000000000',
            'artwork_notes' => 'Test',
        ])->assertRedirect(route('login'));
    }
}
