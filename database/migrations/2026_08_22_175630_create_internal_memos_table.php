<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_memos', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->json('blocks')->nullable();
            $table->json('recipient_scope')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedInteger('emails_sent')->default(0);
            $table->unsignedInteger('emails_failed')->default(0);
            $table->foreignId('sent_by_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_memos');
    }
};
