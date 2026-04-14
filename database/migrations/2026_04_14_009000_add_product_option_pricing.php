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
        Schema::table('products', function (Blueprint $table) {
            $table->json('size_price_options')->nullable()->after('paper_size');
            $table->json('material_price_options')->nullable()->after('paper_type');
            $table->json('finish_price_options')->nullable()->after('finishing');
            $table->json('delivery_price_options')->nullable()->after('finish_price_options');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->json('pricing_breakdown')->nullable()->after('job_image_assets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('pricing_breakdown');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'size_price_options',
                'material_price_options',
                'finish_price_options',
                'delivery_price_options',
            ]);
        });
    }
};
