<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_product_options', function (Blueprint $table): void {
            // null = unlimited stock; 0 = out of stock; >0 = units available
            $table->unsignedInteger('stock_quantity')->nullable()->after('price_modifier');
            // Optional variant image (Cloudinary public_id or URL)
            $table->string('image')->nullable()->after('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('shop_product_options', function (Blueprint $table): void {
            $table->dropColumn(['stock_quantity', 'image']);
        });
    }
};
