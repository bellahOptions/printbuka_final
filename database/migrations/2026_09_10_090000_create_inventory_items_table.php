<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->string('sku', 30)->nullable()->unique();
            $table->unsignedInteger('sku_sequence')->nullable();
            $table->string('name');
            $table->string('category', 60)->nullable();
            $table->string('unit', 30)->default('pieces');
            $table->integer('quantity_on_hand')->default(0);
            $table->unsignedInteger('reorder_level')->nullable();
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
