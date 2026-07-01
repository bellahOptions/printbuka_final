<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->string('kyc_status')->default('pending')->after('kyc_completed_at');
            $table->text('kyc_review_notes')->nullable()->after('kyc_status');
            $table->foreignId('kyc_reviewed_by_id')->nullable()->after('kyc_review_notes')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('kyc_reviewed_at')->nullable()->after('kyc_reviewed_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kyc_reviewed_by_id');
            $table->dropColumn(['kyc_status', 'kyc_review_notes', 'kyc_reviewed_at']);
        });
    }
};
