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
        Schema::create('shop_order_item_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_order_item_id')->constrained()->cascadeOnDelete();
            $table->string('group_name');
            $table->string('option_name');
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_item_options');
    }
};
