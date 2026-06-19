<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product_stock_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_product_id')->constrained()->cascadeOnDelete();
            // null = product-level entry (no specific variant); set when variant stock changes
            $table->foreignId('shop_product_option_id')->nullable()->constrained()->nullOnDelete();
            // Positive = stock added; Negative = stock deducted
            $table->integer('change');
            // Running balance after this change (null when stock is unmanaged)
            $table->unsignedInteger('balance_after')->nullable();
            // admin_set = initial stock entry; admin_adjust = manual correction; sale = purchase; refund = order cancelled/refunded
            $table->string('reason', 30)->default('admin_set');
            // Shop order reference for sale/refund entries
            $table->string('reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['shop_product_id', 'shop_product_option_id', 'created_at'], 'spl_product_option_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product_stock_logs');
    }
};
