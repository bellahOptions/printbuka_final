<?php

namespace Tests\Feature;

use App\Livewire\Admin\CustomerQuickCreate;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminQuotationCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_allowed_roles_can_access_quotation_creator(): void
    {
        $operations = $this->adminUser('operations');
        $customerCare = $this->adminUser('customer_service');
        $designer = $this->adminUser('designer');

        $this->actingAs($operations)
            ->get(route('admin.invoices.quotations.create'))
            ->assertOk();

        $this->actingAs($customerCare)
            ->get(route('admin.invoices.quotations.create'))
            ->assertOk();

        $this->actingAs($designer)
            ->get(route('admin.invoices.quotations.create'))
            ->assertForbidden();
    }

    public function test_super_admin_can_create_quotation_for_existing_customer(): void
    {
        $admin = $this->adminUser('super_admin');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'first_name' => 'Mary',
            'last_name' => 'Client',
            'email' => 'mary.client@example.com',
            'phone' => '08055555555',
            'companyName' => 'Mary Co',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.invoices.quotations.store'), [
                'customer_id' => $customer->id,
                'customer_name' => 'Ignored Name',
                'customer_email' => 'ignored@example.com',
                'customer_phone' => '08000000000',
                'job_type' => 'Business Cards',
                'size_format' => 'A4',
                'quantity' => 5,
                'unit_price' => 10000,
                'tax_amount' => 1000,
                'discount_amount' => 500,
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('status');

        $order = Order::query()->latest('id')->firstOrFail();
        $invoice = Invoice::query()->where('order_id', $order->id)->firstOrFail();

        $this->assertSame('quote', $order->service_type);
        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame($admin->id, $order->created_by_admin_id);
        $this->assertSame($admin->id, $order->brief_received_by_id);
        $this->assertSame($customer->displayName(), $order->customer_name);
        $this->assertSame($customer->email, $order->customer_email);
        $this->assertMatchesRegularExpression('/^QTE-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', (string) $order->job_order_number);
        $this->assertMatchesRegularExpression('/^INV-\d{8}(?:\d{6})?-[A-Z0-9]{6,8}$/', (string) $invoice->invoice_number);
        $this->assertSame('unpaid', $invoice->status);
        $this->assertSame('50500.00', $invoice->total_amount);
    }

    public function test_staff_can_quick_create_customer_via_livewire_component(): void
    {
        $admin = $this->adminUser('operations');
        $this->actingAs($admin);

        Livewire::test(CustomerQuickCreate::class)
            ->set('first_name', 'Quick')
            ->set('last_name', 'Customer')
            ->set('email', 'quick.customer@example.com')
            ->set('phone', '08077777777')
            ->set('companyName', 'Quick Ventures')
            ->call('createCustomer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'quick.customer@example.com',
            'role' => 'customer',
            'first_name' => 'Quick',
            'last_name' => 'Customer',
            'phone' => '08077777777',
            'companyName' => 'Quick Ventures',
            'is_active' => true,
        ]);
    }

    private function adminUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
