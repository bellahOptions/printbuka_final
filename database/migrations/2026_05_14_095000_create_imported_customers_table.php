<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imported_customers', function (Blueprint $table): void {
            $table->id();
            $table->string('external_customer_id')->nullable()->unique();
            $table->string('customer_number')->nullable()->index();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_code')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_code')->nullable();
            $table->string('source')->default('csv_import');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('imported_customer_id')->nullable()->after('user_id')->constrained('imported_customers')->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table): void {
            $table->foreignId('imported_customer_id')->nullable()->after('order_id')->constrained('imported_customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('imported_customer_id');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('imported_customer_id');
        });

        Schema::dropIfExists('imported_customers');
    }
};
