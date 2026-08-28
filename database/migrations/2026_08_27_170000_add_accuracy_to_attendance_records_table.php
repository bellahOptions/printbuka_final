<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->unsignedInteger('clock_in_accuracy_meters')->nullable()->after('clock_in_distance_meters');
            $table->unsignedInteger('clock_out_accuracy_meters')->nullable()->after('clock_out_distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['clock_in_accuracy_meters', 'clock_out_accuracy_meters']);
        });
    }
};
