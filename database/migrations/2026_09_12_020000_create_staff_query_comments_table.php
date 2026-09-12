<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_query_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_query_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();

            $table->index('staff_query_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_query_comments');
    }
};
