<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUG = 'customer_service';

    private const GRANTED = ['vendors.view', 'vendors.manage', 'inventory.view', 'inventory.manage'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = DB::table('roles')->where('slug', self::SLUG)->first();

        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions, true) ?? [];

        if (in_array('*', $permissions, true)) {
            return;
        }

        $permissions = array_values(array_unique(array_merge($permissions, self::GRANTED)));

        DB::table('roles')->where('slug', self::SLUG)->update([
            'permissions' => json_encode($permissions),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = DB::table('roles')->where('slug', self::SLUG)->first();

        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions, true) ?? [];

        $permissions = array_values(array_diff($permissions, self::GRANTED));

        DB::table('roles')->where('slug', self::SLUG)->update([
            'permissions' => json_encode($permissions),
            'updated_at' => now(),
        ]);
    }
};
