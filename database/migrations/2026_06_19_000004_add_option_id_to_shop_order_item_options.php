<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_order_item_options', function (Blueprint $table): void {
            $table->foreignId('shop_product_option_id')
                ->nullable()
                ->after('shop_order_item_id')
                ->constrained('shop_product_options')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shop_order_item_options', function (Blueprint $table): void {
            $table->dropForeignIdFor(\App\Models\ShopProductOption::class);
        });
    }
};
