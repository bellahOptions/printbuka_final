<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_todos', function (Blueprint $table): void {
            $table->string('status')->default('pending')->after('task');
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->nullOnDelete()->after('completed_at');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_id');
            $table->text('review_comments')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('daily_todos', function (Blueprint $table): void {
            $table->dropForeign(['reviewed_by_id']);
            $table->dropColumn(['status', 'completed_at', 'reviewed_by_id', 'reviewed_at', 'review_comments']);
        });
    }
};
