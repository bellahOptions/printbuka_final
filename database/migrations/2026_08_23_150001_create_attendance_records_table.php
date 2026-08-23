<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('work_date');
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->decimal('clock_in_lat', 10, 7)->nullable();
            $table->decimal('clock_in_lng', 10, 7)->nullable();
            $table->decimal('clock_out_lat', 10, 7)->nullable();
            $table->decimal('clock_out_lng', 10, 7)->nullable();
            $table->unsignedInteger('clock_in_distance_meters')->nullable();
            $table->unsignedInteger('clock_out_distance_meters')->nullable();
            $table->boolean('clock_in_within_geofence')->nullable();
            $table->boolean('clock_out_within_geofence')->nullable();
            $table->string('clock_in_photo_path')->nullable();
            $table->string('clock_out_photo_path')->nullable();
            $table->string('status')->default('present'); // present, late, absent, on_leave, half_day
            $table->text('flagged_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('corrected_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
