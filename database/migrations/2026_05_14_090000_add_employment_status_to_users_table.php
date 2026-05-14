<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employment_status')->default('active')->after('is_active')->index();
            $table->text('employment_status_reason')->nullable()->after('employment_status');
            $table->timestamp('employment_status_changed_at')->nullable()->after('employment_status_reason');
            $table->foreignId('employment_status_changed_by_id')->nullable()->after('employment_status_changed_at')->constrained('users')->nullOnDelete();
        });

        DB::table('users')
            ->where('role', 'staff_pending')
            ->orWhere(function ($query): void {
                $query->where('is_active', false)->whereNotNull('requested_role');
            })
            ->update(['employment_status' => 'pending']);

        DB::table('users')
            ->where('is_active', false)
            ->where('employment_status', 'active')
            ->update(['employment_status' => 'suspended']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employment_status_changed_by_id');
            $table->dropColumn([
                'employment_status',
                'employment_status_reason',
                'employment_status_changed_at',
            ]);
        });
    }
};
