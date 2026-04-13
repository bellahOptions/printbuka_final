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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->string('service_type')->default('print')->after('user_id');
            $table->integer('quantity')->after('service_type');
            $table->decimal('unit_price', 10, 2)->after('quantity');
            $table->decimal('total_price', 10, 2)->after('unit_price');
            $table->string('customer_name')->after('total_price');
            $table->string('customer_email')->after('customer_name');
            $table->string('customer_phone')->after('customer_email');
            $table->string('delivery_city')->nullable()->after('customer_phone');
            $table->string('delivery_address')->nullable()->after('delivery_city');
            $table->text('artwork_notes')->nullable()->after('delivery_address');
            $table->string('status')->default('pending')->after('artwork_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'service_type',
                'quantity',
                'unit_price',
                'total_price',
                'customer_name',
                'customer_email',
                'customer_phone',
                'delivery_city',
                'delivery_address',
                'artwork_notes',
                'status',
            ]);
        });
    }
};
