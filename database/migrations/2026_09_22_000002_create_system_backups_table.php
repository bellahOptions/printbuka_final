<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            // 'manual' — triggered from the admin UI; 'scheduled' — the daily cron.
            $table->string('type', 20)->default('manual');
            // running | success | failed
            $table->string('status', 20)->default('running');
            $table->string('disk', 60)->default('backups');
            // Path of the zip on that disk, once known (spatie names it after
            // the run finishes, so this is null while status = running).
            $table->string('path')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
