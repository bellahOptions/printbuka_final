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
        Schema::create('shop_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->text('shipping_notes')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('payment_status')->default('pending'); // pending | paid | failed
            $table->string('paystack_reference')->nullable();
            $table->json('paystack_data')->nullable();
            $table->string('fulfillment_status')->default('pending'); // pending | processing | shipped | delivered | cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
