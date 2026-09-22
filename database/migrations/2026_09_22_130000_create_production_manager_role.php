<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SLUG = 'production_manager';

    private const PERMISSIONS = [
        'admin.view',
        'orders.view',
        'orders.phase_comment',
        'workflow.approve',
        'sop.verify',
        'production.update',
        'qc.update',
        'packaging.update',
        'delivery.update',
        'large_format.manage',
        'large_format.calculate',
        'inventory.view',
        'inventory.manage',
        'attendance.manage',
        'vendors.view',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('roles') || DB::table('roles')->where('slug', self::SLUG)->exists()) {
            return;
        }

        DB::table('roles')->insert([
            'slug' => self::SLUG,
            'label' => 'Production Manager',
            'priority' => 75,
            'permissions' => json_encode(self::PERMISSIONS),
            'dashboard_menu' => json_encode(['Production Jobs', 'QC & Packaging', 'Phase Approvals', "Today's Tasks"]),
            'is_system' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')->where('slug', self::SLUG)->delete();
    }
};
