<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            if (! Schema::hasColumn('trainings', 'status')) {
                $table->string('status')->default('pending')->after('referral_source');
            }

            if (! Schema::hasColumn('trainings', 'decision_note')) {
                $table->text('decision_note')->nullable()->after('status');
            }

            if (! Schema::hasColumn('trainings', 'decided_at')) {
                $table->timestamp('decided_at')->nullable()->after('decision_note');
            }

            if (! Schema::hasColumn('trainings', 'decided_by_id')) {
                $table->foreignId('decided_by_id')->nullable()->after('decided_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            if (Schema::hasColumn('trainings', 'decided_by_id')) {
                $table->dropConstrainedForeignId('decided_by_id');
            }

            foreach (['decided_at', 'decision_note', 'status'] as $column) {
                if (Schema::hasColumn('trainings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
