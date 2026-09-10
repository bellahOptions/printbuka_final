<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Extra permission strings granted to this specific staff member on
            // top of whatever their role already grants — lets Super Admin give
            // one individual access (e.g. inventory.manage) without changing the
            // permission set for everyone else who shares that role.
            $table->json('permission_overrides')->nullable()->after('employment_status_changed_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('permission_overrides');
        });
    }
};
