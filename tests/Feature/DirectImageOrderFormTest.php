<?php

namespace Tests\Feature;

use App\Livewire\Services\DirectImageOrderForm;
use App\Models\SiteSetting;
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

        SiteSetting::query()->insert([
            ['key' => 'service_price_direct_image_printing', 'value' => '2000', 'group' => 'service_pricing'],
            ['key' => 'service_price_direct_image_printing_design', 'value' => '1500', 'group' => 'service_pricing'],
            ['key' => 'service_price_direct_image_printing_delivery', 'value' => '1000', 'group' => 'service_pricing'],
            ['key' => 'default_material_price_options', 'value' => "Matte|500\nGloss|700", 'group' => 'pricing'],
            ['key' => 'default_size_price_options', 'value' => "A4|300\nA3|450", 'group' => 'pricing'],
        ]);

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
