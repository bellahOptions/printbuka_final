<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The 2026_07_03_130000_create_price_list_items_table migration was marked
 * as run in some environments (this one included) but the table itself was
 * missing — most likely a database reset/restore that didn't roll the
 * migrations table back with it. This recreates it idempotently instead of
 * touching migration history, so it's safe to run anywhere regardless of
 * whether that drift exists there too.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('price_list_items')) {
            return;
        }

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

    public function down(): void
    {
        // Intentionally a no-op — the original migration owns this table's
        // lifecycle; this one only ever repairs a missing copy of it.
    }
};
