<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('evaluated_by_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('period_month'); // 1-12
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('overall_rating')->default(3); // 1-5
            $table->unsignedTinyInteger('punctuality_rating')->nullable();
            $table->unsignedTinyInteger('quality_of_work_rating')->nullable();
            $table->unsignedTinyInteger('teamwork_rating')->nullable();
            $table->unsignedTinyInteger('communication_rating')->nullable();
            $table->unsignedTinyInteger('initiative_rating')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, acknowledged
            $table->boolean('staff_acknowledged')->default(false);
            $table->timestamp('staff_acknowledged_at')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'period_month', 'period_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_evaluations');
    }
};
