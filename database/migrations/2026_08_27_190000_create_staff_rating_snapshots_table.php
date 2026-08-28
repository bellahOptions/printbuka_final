<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_rating_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('period_type'); // 'week' or 'month'
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('attendance_score', 5, 2)->default(0);
            $table->decimal('overtime_score', 5, 2)->default(0);
            $table->decimal('supervisor_score', 5, 2)->default(0);
            $table->decimal('activity_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->unique(['period_type', 'period_start', 'user_id'], 'staff_rating_unique_period_user');
            $table->index(['period_type', 'period_start', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_rating_snapshots');
    }
};
