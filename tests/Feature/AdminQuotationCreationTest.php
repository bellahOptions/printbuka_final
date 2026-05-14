<?php

namespace Tests\Feature;

use App\Mail\InvoicePaidReceiptMail;
use App\Livewire\Admin\CustomerQuickCreate;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
        $this->assertMatchesRegularExpression('/^QT-\d{6}$/', (string) $invoice->invoice_number);
        $this->assertSame('unpaid', $invoice->status);
        $this->assertSame('50500.00', $invoice->total_amount);
    }

    public function test_staff_can_create_quotation_with_custom_line_items_and_mark_it_paid(): void
    {
        Mail::fake();

        $admin = $this->adminUser('operations');
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
            'first_name' => 'Custom',
            'last_name' => 'Client',
            'email' => 'custom.client@example.com',
            'phone' => '08011112222',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.invoices.quotations.store'), [
                'customer_id' => $customer->id,
                'customer_name' => 'Ignored Name',
                'customer_email' => 'ignored@example.com',
                'customer_phone' => '08000000000',
                'job_type' => 'Custom Campaign',
                'line_items' => [
                    [
                        'description' => 'Creative design',
                        'quantity' => 1,
                        'rate' => 20000,
                    ],
                    [
                        'description' => 'Print production',
                        'quantity' => 50,
                        'rate' => 400,
                    ],
                ],
                'tax_amount' => 2000,
                'discount_amount' => 1000,
                'invoice_status' => 'paid',
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('status');

        $order = Order::query()->latest('id')->firstOrFail();
        $invoice = Invoice::query()->where('order_id', $order->id)->firstOrFail();
        $lineItems = $order->pricing_breakdown['line_items'] ?? [];

        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('41000.00', (string) $invoice->total_amount);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame('41000.00', (string) $order->amount_paid);
        $this->assertCount(2, $lineItems);
        $this->assertSame('Creative design', $lineItems[0]['description']);
        $this->assertSame(1, (int) $lineItems[0]['quantity']);
        $this->assertSame(20000.0, (float) $lineItems[0]['rate']);

        Mail::assertSent(InvoicePaidReceiptMail::class, function (InvoicePaidReceiptMail $mail): bool {
            return $mail->hasTo('custom.client@example.com');
        });
    }

    public function test_admin_can_create_quotation_with_catalog_line_items_and_item_specs(): void
    {
        $admin = $this->adminUser('super_admin');

        $product = Product::query()->create([
            'name' => 'Branded Mug',
            'moq' => 10,
            'price' => 12000,
            'short_description' => 'Ceramic mug',
            'description' => 'Premium branded mug printing.',
            'paper_type' => 'Ceramic',
            'material_price_options' => [['label' => 'Ceramic', 'price' => 0]],
            'paper_size' => '11oz',
            'size_price_options' => [['label' => '11oz', 'price' => 0]],
            'finishing' => 'Gloss',
            'finish_price_options' => [['label' => 'Gloss', 'price' => 0]],
            'density_price_options' => [['label' => 'Standard', 'price' => 0]],
            'delivery_price_options' => [['label' => 'Pickup', 'price' => 0]],
            'paper_density' => 'Standard',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.invoices.quotations.store'), [
                'customer_name' => 'Catalog Quote Client',
                'customer_email' => 'catalog.quote@example.com',
                'customer_phone' => '08012341234',
                'job_type' => 'Corporate Gifts',
                'line_items' => [
                    [
                        'source_type' => 'product',
                        'catalog_item_key' => 'product:'.$product->id,
                        'description' => '',
                        'size' => '11oz',
                        'color' => 'Navy Blue',
                        'finishing' => 'Gloss Lamination',
                        'quantity' => 2,
                        'rate' => 0,
                    ],
                    [
                        'source_type' => 'service',
                        'catalog_item_key' => 'service:dtf',
                        'description' => 'DTF shirt transfer',
                        'size' => 'XL',
                        'color' => 'White',
                        'finishing' => 'No Finish',
                        'quantity' => 10,
                        'rate' => 2800,
                    ],
                ],
                'tax_amount' => 0,
                'discount_amount' => 0,
                'send_email' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('status');

        $order = Order::query()->latest('id')->firstOrFail();
        $invoice = Invoice::query()->where('order_id', $order->id)->firstOrFail();
        $lineItems = $order->pricing_breakdown['line_items'] ?? [];

        $this->assertCount(2, $lineItems);
        $this->assertSame('product', $lineItems[0]['source_type'] ?? null);
        $this->assertSame('product:'.$product->id, $lineItems[0]['catalog_item_key'] ?? null);
        $this->assertSame('11oz', $lineItems[0]['size'] ?? null);
        $this->assertSame('Navy Blue', $lineItems[0]['color'] ?? null);
        $this->assertSame('Gloss Lamination', $lineItems[0]['finishing'] ?? null);
        $this->assertStringContainsString('Branded Mug', (string) ($lineItems[0]['description'] ?? ''));
        $this->assertSame(12000.0, (float) ($lineItems[0]['rate'] ?? 0));
        $this->assertSame('52000.00', (string) $invoice->subtotal);
        $this->assertSame('52000.00', (string) $invoice->total_amount);
    }

    public function test_admin_can_mark_existing_quotation_as_paid_from_invoice_index(): void
    {
        Mail::fake();

        $admin = $this->adminUser('super_admin');

        $order = Order::query()->create([
            'service_type' => 'quote',
            'quantity' => 2,
            'unit_price' => 5000,
            'total_price' => 10000,
            'customer_name' => 'Quote Client',
            'customer_email' => 'quote.client@example.com',
            'customer_phone' => '08012345678',
            'status' => 'Quote Requested',
            'job_order_number' => 'QTE-20260417-ABC123',
            'payment_status' => 'Awaiting Invoice',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-20260417-ABC123',
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 10000,
            'status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.invoices.mark-paid', $invoice))
            ->assertRedirect();

        $invoice->refresh();
        $order->refresh();

        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame('10000.00', (string) $order->amount_paid);

        Mail::assertSent(InvoicePaidReceiptMail::class, function (InvoicePaidReceiptMail $mail): bool {
            return $mail->hasTo('quote.client@example.com');
        });
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
