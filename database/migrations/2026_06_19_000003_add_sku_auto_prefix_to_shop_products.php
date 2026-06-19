<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ensures the sku column exists and adds a stock_sequence counter column
// used by auto-SKU generation in ShopProduct::boot().
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_products', function (Blueprint $table): void {
            // Sequential number used to build SKU: PBK-YYYY-NNNNN
            $table->unsignedInteger('sku_sequence')->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table): void {
            $table->dropColumn('sku_sequence');
        });
    }
};
