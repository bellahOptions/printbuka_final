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
            $table->foreignId('order_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique()->after('order_id');
            $table->decimal('subtotal', 10, 2)->after('invoice_number');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('total_amount', 10, 2)->after('discount_amount');
            $table->string('status')->default('sent')->after('total_amount');
            $table->timestamp('issued_at')->nullable()->after('status');
            $table->timestamp('due_at')->nullable()->after('issued_at');
            $table->timestamp('sent_at')->nullable()->after('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropColumn([
                'invoice_number',
                'subtotal',
                'tax_amount',
                'discount_amount',
                'total_amount',
                'status',
                'issued_at',
                'due_at',
                'sent_at',
            ]);
        });
    }
};
