<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table): void {
            // Plain-English sentence fragment ("created an inventory item
            // 'A4 Paper'") generated for mutating requests — null for page
            // views and anything the narrator can't confidently describe, so
            // the existing raw action/route text always remains the fallback.
            $table->string('description', 500)->nullable()->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }
};
