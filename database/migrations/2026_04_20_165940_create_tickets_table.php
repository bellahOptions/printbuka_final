<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tickets table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->string('category');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->text('message');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index('ticket_number');
            $table->index('status');
            $table->index('user_id');
        });

        // Ticket replies table
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_staff_reply')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index('ticket_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
    }
};