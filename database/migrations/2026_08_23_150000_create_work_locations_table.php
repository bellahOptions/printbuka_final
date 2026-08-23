<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->default(200);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed the single, locked head-office location. Coordinates are an
        // approximate starting point (Shomolu, Lagos) with a generous radius
        // until a super_admin/HR/ops/MD user captures the precise on-site GPS
        // fix via the "Use my current location" action in the admin UI —
        // this repo has no geocoding API access to resolve the exact point.
        DB::table('work_locations')->insert([
            'name' => 'Head Office',
            'address' => '63, Akeju Street, off Shipeolu Street, Shomolu, Lagos',
            'latitude' => 6.5390,
            'longitude' => 3.3850,
            'radius_meters' => 250,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
