<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->insertOrIgnore([
            'slug' => 'social_media_manager',
            'label' => 'Social Media Manager',
            'priority' => 40,
            'permissions' => json_encode(['admin.view', 'blog.view', 'blog.manage', 'advertisements.manage']),
            'dashboard_menu' => json_encode(['Blog', 'Advertisements', "Today's Tasks"]),
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('slug', 'social_media_manager')->delete();
    }
};
