<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->foreignId('company_account_id')->nullable()->after('order_id')
                ->constrained('company_accounts')->nullOnDelete();
        });

        // Seed a starting account from the legacy single-account site settings so
        // existing invoice PDFs keep showing the same payment details after this
        // migration runs.
        $settings = DB::table('site_settings')
            ->whereIn('key', ['company_account_name', 'company_account_number', 'company_account_bank_name', 'company_account_note'])
            ->pluck('value', 'key');

        DB::table('company_accounts')->insert([
            'label' => 'Primary Account',
            'account_name' => trim((string) ($settings['company_account_name'] ?? '')) ?: 'Alet Inspirationz',
            'account_number' => trim((string) ($settings['company_account_number'] ?? '')) ?: '0062999338',
            'bank_name' => trim((string) ($settings['company_account_bank_name'] ?? '')) ?: 'Access bank',
            'note' => trim((string) ($settings['company_account_note'] ?? '')) ?: null,
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('company_account_id');
        });
    }
};
