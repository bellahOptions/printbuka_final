<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            // Positive = stock added; Negative = stock deducted
            $table->integer('change');
            // Running balance after this change
            $table->integer('balance_after');
            // initial, restock, usage, adjustment, damaged, return, other
            $table->string('reason', 30)->default('adjustment');
            // Optional free-text reference, e.g. a job order number or supplier invoice #
            $table->string('reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['inventory_item_id', 'created_at'], 'ism_item_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};
