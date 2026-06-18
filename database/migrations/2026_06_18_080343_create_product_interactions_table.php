<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('product_type', 10); // 'catalog' | 'shop'
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_type', 'product_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_interactions');
    }
};
