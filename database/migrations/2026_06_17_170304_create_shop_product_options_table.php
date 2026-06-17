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
        Schema::create('shop_product_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_product_option_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_product_options');
    }
};
