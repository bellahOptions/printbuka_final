<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            // Needed to re-resolve/verify an account with Paystack (it takes a bank
            // code, not a free-text bank name). Populated automatically on CSV
            // import; nullable because manually-created vendors may not have it.
            $table->string('bank_code', 20)->nullable()->after('bank_name');

            // Set only when Paystack's "resolve account" API actually confirmed the
            // account_name for this account number + bank code. A vendor with a
            // bank_account_name but no verification timestamp came from a manual
            // entry or an import row Paystack could not confirm.
            $table->timestamp('account_verified_at')->nullable()->after('bank_account_number');

            // Provenance for rows created by the CSV importer, e.g. "csv:vendors-list-2026-09-22.csv".
            $table->string('imported_from')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table): void {
            $table->dropColumn(['bank_code', 'account_verified_at', 'imported_from']);
        });
    }
};
