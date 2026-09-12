<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->enum('status', ['active', 'concluded', 'abandoned'])->default('active');
            $table->boolean('escalation_requested')->default(false);
            $table->foreignId('ticket_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->enum('sender', ['user', 'bot', 'staff']);
            $table->text('message');
            $table->timestamps();

            $table->index('chat_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};
