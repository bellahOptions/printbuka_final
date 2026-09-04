<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Roles that existed before the role set was consolidated down to:
     * super_admin, managing_director, hr, operations_manager, customer_service,
     * personal_assistant, designer, office_assistant, machine_operator, staff_pending.
     *
     * Any user still carrying one of these legacy values fails every
     * `canAdmin()` check (config('printbuka_admin.roles.<role>') resolves to
     * an empty permission array) and is bounced with a 403 on every admin
     * route, even though their account is otherwise active.
     *
     * `designer` was later restored as a first-class role (see
     * config/printbuka_admin.php), so it — and its near-duplicate variants —
     * are no longer remapped away here; only genuinely retired role slugs are.
     *
     * @var array<string, string>
     */
    private const LEGACY_ROLE_MAP = [
        'admin' => 'super_admin',
        'it' => 'super_admin',
        'management' => 'managing_director',
        'operations' => 'operations_manager',
        'supervisor' => 'operations_manager',
        'marketing' => 'customer_service',
        'finance' => 'customer_service',
        'graphic_designer' => 'designer',
        'creative_designer' => 'designer',
        'logistics' => 'personal_assistant',
        'production' => 'machine_operator',
        'production_assistant' => 'machine_operator',
        'operator' => 'machine_operator',
        'qc' => 'operations_manager',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::LEGACY_ROLE_MAP as $legacyRole => $currentRole) {
            DB::table('users')
                ->where('role', $legacyRole)
                ->update(['role' => $currentRole]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: several legacy roles collapse onto the same
        // current role (e.g. designer/graphic_designer/creative_designer
        // all map to personal_assistant), so the original value can't be
        // recovered.
    }
};
