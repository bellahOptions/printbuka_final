<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('gender')->nullable();
            $table->string('phone_whatsapp', 50);
            $table->string('email');
            $table->text('contact_address');
            $table->string('city_state');
            $table->string('educational_qualification');
            $table->string('desired_skill');
            $table->string('employment_status')->nullable();
            $table->string('experience_level')->nullable();
            $table->boolean('has_laptop')->default(false);
            $table->string('availability');
            $table->string('portfolio_url', 500)->nullable();
            $table->text('motivation');
            $table->string('referral_source')->nullable();
            $table->string('status')->default('pending');
            $table->text('decision_note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('desired_skill');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
