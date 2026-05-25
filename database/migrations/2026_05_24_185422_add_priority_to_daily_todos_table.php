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
        Schema::table('daily_todos', function (Blueprint $table) {
            $table->unsignedTinyInteger('priority')->default(0)->after('status')->comment('0 = normal, 1 = urgent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_todos', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
