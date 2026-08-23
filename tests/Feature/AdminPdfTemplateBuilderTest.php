<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\StaffProfile;
use App\Models\User;
use App\Support\PdfTemplateOverrides;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPdfTemplateBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
            'two_factor_confirmed_at' => now(),
        ]);

        StaffProfile::query()->create([
            'user_id' => $user->id,
            'kyc_status' => 'approved',
        ]);

        return $user;
    }

    public function test_super_admin_can_view_and_edit_the_pdf_template_list(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pdf-templates.index'))
            ->assertOk()
            ->assertSee('Invoice PDF (Customer Copy)');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pdf-templates.edit', 'pdf.invoice_customer'))
            ->assertOk();
    }

    public function test_non_super_admin_cannot_access_pdf_templates(): void
    {
        foreach (['hr', 'managing_director', 'operations_manager'] as $role) {
            $staff = $this->makeStaff($role);

            $this->actingAs($staff)
                ->withSession(['staff_2fa_verified' => true])
                ->get(route('admin.pdf-templates.index'))
                ->assertForbidden();
        }
    }

    public function test_saving_a_pdf_template_overrides_the_generated_document(): void
    {
        $admin = $this->makeStaff('super_admin');

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->put(route('admin.pdf-templates.update', 'pdf.invoice_customer'), [
                'intro_blocks' => json_encode([
                    ['id' => '1', 'type' => 'paragraph', 'text' => 'Custom PDF intro for {{customer_name}}.'],
                ]),
                'outro_blocks' => json_encode([
                    ['id' => '2', 'type' => 'paragraph', 'text' => 'Custom PDF outro.'],
                ]),
            ])
            ->assertRedirect(route('admin.pdf-templates.index'));

        $this->assertDatabaseHas('email_templates', ['key' => 'pdf.invoice_customer']);

        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::query()->create([
            'user_id' => $customer->id,
            'job_order_number' => 'PB-2026-0001',
            'customer_name' => 'Jane Client',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '08000000000',
            'job_type' => 'Business Cards',
            'quantity' => 100,
            'unit_price' => 500,
            'total_price' => 50000,
            'channel' => 'Manual',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'priority' => '🟡 Normal',
            'service_type' => 'catalog',
        ]);
        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-0001',
            'subtotal' => 50000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'status' => 'pending',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ]);
        $invoice->load('order.product');

        $data = array_merge(
            ['invoice' => $invoice],
            PdfTemplateOverrides::forKey('pdf.invoice_customer', ['customer_name' => 'Jane Client'])
        );

        $html = view('invoices.pdf', $data)->render();

        $this->assertStringContainsString('Custom PDF intro for Jane Client.', $html);
        $this->assertStringContainsString('Custom PDF outro.', $html);
    }

    public function test_an_untouched_pdf_template_still_renders_its_default_content(): void
    {
        $data = PdfTemplateOverrides::forKey('pdf.invoice_customer');

        $this->assertSame('', $data['introHtml']);
        $this->assertSame('', $data['outroHtml']);
    }

    public function test_preview_endpoint_reflects_unsaved_block_edits_live(): void
    {
        $admin = $this->makeStaff('super_admin');

        $introBlocks = json_encode([
            ['id' => '1', 'type' => 'paragraph', 'text' => 'LIVE-PREVIEW-MARKER-not-yet-saved'],
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->get(route('admin.pdf-templates.preview', 'pdf.invoice_customer').'?'.http_build_query([
                'intro_blocks' => $introBlocks,
                'outro_blocks' => '[]',
            ]));

        $response->assertOk();
        $response->assertSee('LIVE-PREVIEW-MARKER-not-yet-saved');

        // Confirms nothing was persisted by merely previewing.
        $this->assertDatabaseMissing('email_templates', ['key' => 'pdf.invoice_customer']);
    }

    public function test_reverting_a_pdf_template_removes_the_override(): void
    {
        $admin = $this->makeStaff('super_admin');

        EmailTemplate::query()->create([
            'key' => 'pdf.invoice_customer',
            'name' => 'Invoice PDF (Customer Copy)',
            'intro_blocks' => [['id' => '1', 'type' => 'paragraph', 'text' => 'x']],
        ]);

        $this->actingAs($admin)
            ->withSession(['staff_2fa_verified' => true])
            ->post(route('admin.pdf-templates.reset', 'pdf.invoice_customer'))
            ->assertRedirect(route('admin.pdf-templates.index'));

        $this->assertDatabaseMissing('email_templates', ['key' => 'pdf.invoice_customer']);
    }
}
