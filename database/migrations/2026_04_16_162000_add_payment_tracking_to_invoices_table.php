<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_reference')
                ->nullable()
                ->unique()
                ->after('invoice_number');
            $table->string('payment_gateway')
                ->nullable()
                ->after('payment_reference');
            $table->timestamp('paid_at')
                ->nullable()
                ->after('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_gateway', 'paid_at']);
        });
    }
};
