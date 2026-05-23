<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_todos', function (Blueprint $table): void {
            $table->unsignedTinyInteger('review_rating')->nullable()->after('review_comments');
        });
    }

    public function down(): void
    {
        Schema::table('daily_todos', function (Blueprint $table): void {
            $table->dropColumn('review_rating');
        });
    }
};

