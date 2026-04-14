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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('job_type')->nullable()->after('service_type');
            $table->string('size_format')->nullable()->after('job_type');
            $table->boolean('design_approved_by_client')->default(false)->after('design_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'job_type',
                'size_format',
                'design_approved_by_client',
            ]);
        });
    }
};
