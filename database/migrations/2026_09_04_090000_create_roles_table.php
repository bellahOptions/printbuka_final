<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('label');
            $table->unsignedTinyInteger('priority')->default(10);
            $table->json('permissions');
            $table->json('dashboard_menu')->nullable();
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed the current, config-defined role set as system roles so the
        // dynamic table starts as a superset of what already exists — no
        // staff member's effective permissions change by adding this table.
        foreach ((array) config('printbuka_admin.roles', []) as $slug => $permissions) {
            DB::table('roles')->insert([
                'slug' => $slug,
                'label' => (string) config("printbuka_admin.role_labels.{$slug}", ucfirst(str_replace('_', ' ', $slug))),
                'priority' => (int) config("printbuka_admin.role_priority.{$slug}", 10),
                'permissions' => json_encode(array_values((array) $permissions)),
                'dashboard_menu' => json_encode(config("printbuka_admin.staff_dashboard_menus.{$slug}", [])),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
