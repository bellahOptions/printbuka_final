<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Department is derived from role, not typed freehand — this backfills
     * the department each existing system role auto-assigns to its holders.
     */
    private const DEPARTMENTS = [
        'super_admin' => 'Process & Technology',
        'managing_director' => 'Executive',
        'hr' => 'Human Resources',
        'operations_manager' => 'Operations',
        'production_manager' => 'Production',
        'customer_service' => 'Customer Service',
        'personal_assistant' => 'Executive',
        'designer' => 'Creative',
        'office_assistant' => 'Administration',
        'machine_operator' => 'Production',
        'social_media_manager' => 'Marketing',
    ];

    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->string('department')->nullable()->after('label');
        });

        if (! Schema::hasTable('roles')) {
            return;
        }

        foreach (self::DEPARTMENTS as $slug => $department) {
            DB::table('roles')->where('slug', $slug)->update(['department' => $department]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn('department');
        });
    }
};
