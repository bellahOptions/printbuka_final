<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Firebase FCM was replaced with the Web Push channel — see
// database/migrations/2026_09_12_092301_create_push_subscriptions_table.php
// for its polymorphic push_subscriptions table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('staff_push_subscriptions');
    }

    public function down(): void
    {
        Schema::create('staff_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_token')->unique();
            $table->enum('platform', ['ios', 'android'])->default('android');
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }
};
