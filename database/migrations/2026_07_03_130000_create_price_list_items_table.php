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
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 20);
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('service_slug', 60)->nullable();
            $table->string('component_group', 30)->nullable();
            $table->string('component_key', 60)->nullable();
            $table->string('label');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'product_id', 'component_group']);
            $table->index(['category', 'service_slug', 'component_group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};
