<?php

namespace Tests\Feature;

use App\Livewire\Services\DtfOrderForm;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class DtfOrderFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_requirement_on_dtf_service_page(): void
    {
        $this->get(route('services.show', 'dtf'))
            ->assertOk()
            ->assertSee('Sign in to place a DTF order');
    }

    public function test_dtf_livewire_form_calculates_and_redirects_to_paystack(): void
    {
        Mail::fake();

        config()->set('services.paystack.secret_key', 'sk_test_123');

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/dtf-reference',
                ],
            ], 200),
        ]);

        SiteSetting::query()->insert([
            ['key' => 'service_price_dtf', 'value' => '3000', 'group' => 'service_pricing'],
            ['key' => 'service_price_dtf_design', 'value' => '2000', 'group' => 'service_pricing'],
            ['key' => 'service_price_dtf_delivery', 'value' => '800', 'group' => 'service_pricing'],
            ['key' => 'service_dtf_size_price_options', 'value' => "A2|1600\nA3|900\nA4|500\nA5|250\nA6|100", 'group' => 'service_pricing'],
        ]);

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'phone' => '08010000000',
        ]);

        $service = [
            'slug' => 'dtf',
            'name' => 'DTF',
        ];

        Livewire::actingAs($user)
            ->test(DtfOrderForm::class, ['service' => $service])
            ->set('quantity', 3)
            ->set('film_size', 'A3')
            ->set('has_design', 'no')
            ->set('design_brief', 'Need two-color transfer for team shirts.')
            ->set('delivery_type', 'delivery')
            ->set('delivery_city', 'Lagos')
            ->set('delivery_address', '22 Oregun Road, Ikeja')
            ->set('customer_name', $user->displayName())
            ->set('customer_email', $user->email)
            ->set('customer_phone', '08010000000')
            ->call('submit')
            ->assertRedirect('https://checkout.paystack.com/dtf-reference');

        $this->assertDatabaseHas('orders', [
            'service_type' => 'service:dtf',
            'job_type' => 'DTF',
            'quantity' => 3,
            'material_substrate' => 'Film',
            'size_format' => 'A3',
            'delivery_method' => 'Delivery Address',
            'delivery_city' => 'Lagos',
            'total_price' => 14500,
        ]);

        $this->assertDatabaseHas('invoices', [
            'payment_gateway' => 'paystack',
            'total_amount' => 14500,
        ]);
    }

    public function test_dtf_post_route_requires_authentication(): void
    {
        $this->post(route('services.orders.store', 'dtf'), [
            'quantity' => 1,
            'delivery_method' => 'Client Pickup',
            'customer_name' => 'Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '08000000000',
            'artwork_notes' => 'Test',
        ])->assertRedirect(route('login'));
    }
}
