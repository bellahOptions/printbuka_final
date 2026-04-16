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
            $table->boolean('is_express')->default(false)->after('priority');
            $table->boolean('is_sample')->default(false)->after('is_express');
            $table->index(['is_express', 'estimated_delivery_at'], 'orders_express_estimated_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_express_estimated_idx');
            $table->dropColumn(['is_express', 'is_sample']);
        });
    }
};
