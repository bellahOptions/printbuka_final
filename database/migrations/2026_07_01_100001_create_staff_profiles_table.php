<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('other_names')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_of_employment')->nullable();
            $table->string('sex')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('local_govt_area')->nullable();
            $table->text('present_address')->nullable();
            $table->string('home_telephone')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->text('next_of_kin_home_address')->nullable();
            $table->text('next_of_kin_office_address')->nullable();
            $table->string('post_held')->nullable();
            $table->string('post_telephone')->nullable();
            $table->string('post_email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('pension_pin')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('emergency_contact_notes')->nullable();
            $table->timestamp('kyc_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
