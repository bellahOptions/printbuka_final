<?php

namespace Tests\Feature;

use App\Livewire\Admin\InvoiceCsvImport;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class AdminQuoteCsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_import_quote_csv_as_paid_delivered_jobs(): void
    {
        $admin = $this->admin('super_admin');
        $csv = UploadedFile::fake()->createWithContent('quotes.csv', implode("\n", [
            'Quote Date,Quote ID,Quote Number,Quote Status,Customer ID,Expiry Date,SubTotal,Total,Entity Discount Amount,Notes,Subject,Customer Name,Customer Number,Billing Address,Billing City,Billing State,Billing Country,Shipping Address,Shipping City,Shipping State,Shipping Country,Item Name,Item Desc,Quantity,Item Total',
            '2023-09-21,4271668000000160001,QT-000002,draft,4271668000000107001,,20000.00,20000.00,0.00,Looking forward,BANNER,MRS OLAIDE,CUS-18,,,,,,,,"",BANNER 200x10,Large banner,1.00,16000.00',
            '2023-09-21,4271668000000160001,QT-000002,draft,4271668000000107001,,20000.00,20000.00,0.00,Looking forward,BANNER,MRS OLAIDE,CUS-18,,,,,,,,"",BANNER 200x5,Small banner,1.00,4000.00',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.invoices.import-csv'), ['csv_file' => $csv])
            ->assertRedirect()
            ->assertSessionHas('status');

        $invoice = Invoice::query()->where('invoice_number', 'QT-000002')->firstOrFail();
        $order = $invoice->order;

        $this->assertSame('paid', $invoice->status);
        $this->assertSame('4271668000000160001', $invoice->external_document_id);
        $this->assertSame('4271668000000107001', $invoice->external_customer_id);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('20000.00', (string) $invoice->total_amount);
        $this->assertSame('Delivered', $order->status);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame('20000.00', (string) $order->amount_paid);
        $this->assertSame('quote', $order->service_type);
        $this->assertNull($order->user_id);
        $this->assertNotNull($order->imported_customer_id);
        $this->assertDatabaseHas('imported_customers', [
            'external_customer_id' => '4271668000000107001',
            'name' => 'MRS OLAIDE',
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'legacy+4271668000000107001@import.printbuka.local']);
    }

    public function test_super_admin_can_import_invoice_csv_as_paid_delivered_jobs(): void
    {
        $admin = $this->admin('super_admin');
        $csv = UploadedFile::fake()->createWithContent('invoices.csv', implode("\n", [
            'Invoice Date,Invoice ID,Invoice Number,Invoice Status,Customer ID,Customer Name,Customer Number,Due Date,SubTotal,Total,Entity Discount Amount,Notes,Subject,Billing Address,Billing City,Billing State,Billing Country,Shipping Address,Shipping City,Shipping State,Shipping Country,Item Name,Item Desc,Quantity,Item Total,Item Price',
            '2023-07-10,4271668000000077324,INV-000004,Draft,4271668000000095032,EMERALD DAY SECONDARY SCHOOL,CUS-12,2023-07-10,80000.00,83000.00,0.00,Thanks for your business,LETTERHEAD PAPER,,,,,,,,EMERALD LETTERHEADED PAPER,1000 copies,1000.00,80000.00,80.00',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.invoices.import-csv'), ['csv_file' => $csv])
            ->assertRedirect()
            ->assertSessionHas('status');

        $invoice = Invoice::query()->where('invoice_number', 'INV-000004')->firstOrFail();
        $order = $invoice->order;

        $this->assertSame('paid', $invoice->status);
        $this->assertSame('4271668000000077324', $invoice->external_document_id);
        $this->assertSame('83000.00', (string) $invoice->total_amount);
        $this->assertSame('Delivered', $order->status);
        $this->assertSame('Invoice Settled (100%)', $order->payment_status);
        $this->assertSame('print', $order->service_type);
        $this->assertNull($order->user_id);
        $this->assertSame($order->imported_customer_id, $invoice->imported_customer_id);
    }

    public function test_livewire_import_upload_dispatches_invoice_table_refresh(): void
    {
        $admin = $this->admin('super_admin');
        $this->actingAs($admin);
        $csv = UploadedFile::fake()->createWithContent('quotes.csv', implode("\n", [
            'Quote Date,Quote ID,Quote Number,Customer ID,SubTotal,Total,Entity Discount Amount,Subject,Customer Name,Customer Number,Item Name,Quantity,Item Total',
            '2023-09-21,4271668000000160001,QT-000002,4271668000000107001,20000.00,20000.00,0.00,BANNER,MRS OLAIDE,CUS-18,BANNER 200x10,1.00,20000.00',
        ]));

        Livewire::test(InvoiceCsvImport::class)
            ->set('csvFile', $csv)
            ->call('import')
            ->assertHasNoErrors()
            ->assertDispatched('invoices-imported');

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'QT-000002',
            'status' => 'paid',
        ]);
    }

    public function test_non_super_admin_cannot_import_quote_csv(): void
    {
        $admin = $this->admin('admin');
        $csv = UploadedFile::fake()->createWithContent('quotes.csv', "Quote Number\nQT-1\n");

        $this->actingAs($admin)
            ->post(route('admin.invoices.import-csv'), ['csv_file' => $csv])
            ->assertForbidden();
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
