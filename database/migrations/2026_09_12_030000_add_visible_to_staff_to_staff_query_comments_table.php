<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_query_comments', function (Blueprint $table) {
            $table->boolean('visible_to_staff')->default(false)->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('staff_query_comments', function (Blueprint $table) {
            $table->dropColumn('visible_to_staff');
        });
    }
};
