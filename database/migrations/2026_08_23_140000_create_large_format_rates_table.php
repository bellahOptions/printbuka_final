<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('large_format_rates', function (Blueprint $table) {
            $table->id();
            $table->string('material')->unique();
            $table->decimal('rate_per_sqft', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('large_format_rates');
    }
};
