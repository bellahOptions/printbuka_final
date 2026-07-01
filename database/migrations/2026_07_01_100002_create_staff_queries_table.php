<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_by_id')->constrained('users')->restrictOnDelete();
            $table->string('query_number')->unique();
            $table->date('query_date');
            $table->string('query_type'); // verbal_warning, written_warning, query, suspension_notice, termination_notice
            $table->string('subject');
            $table->text('description');
            $table->date('response_due_date')->nullable();
            $table->text('staff_response')->nullable();
            $table->timestamp('staff_responded_at')->nullable();
            $table->string('status')->default('pending'); // pending, responded, closed, escalated
            $table->foreignId('resolved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_queries');
    }
};
