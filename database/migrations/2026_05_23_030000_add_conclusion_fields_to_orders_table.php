<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->boolean('is_concluded')->default(false)->after('status');
            $table->foreignId('concluded_by_id')->nullable()->after('is_concluded')->constrained('users')->nullOnDelete();
            $table->timestamp('concluded_at')->nullable()->after('concluded_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['concluded_by_id']);
            $table->dropColumn(['is_concluded', 'concluded_by_id', 'concluded_at']);
        });
    }
};

