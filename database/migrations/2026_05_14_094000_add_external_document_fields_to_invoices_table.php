<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->string('external_document_id')->nullable()->after('invoice_number')->index();
            $table->string('external_customer_id')->nullable()->after('external_document_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropColumn(['external_document_id', 'external_customer_id']);
        });
    }
};
