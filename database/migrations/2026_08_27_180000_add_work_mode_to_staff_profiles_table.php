<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            // Null until the staff member declares it — triggers the one-time
            // work-status prompt. 'onsite', 'hybrid', or 'remote'.
            $table->string('work_mode')->nullable()->after('kyc_reviewed_at');

            // Only meaningful for 'hybrid': the weekday abbreviations (Mon..Sat)
            // the staff member is expected in the office. Any other working day
            // is implicitly remote for them.
            $table->json('onsite_days')->nullable()->after('work_mode');

            $table->timestamp('work_mode_set_at')->nullable()->after('onsite_days');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn(['work_mode', 'onsite_days', 'work_mode_set_at']);
        });
    }
};
