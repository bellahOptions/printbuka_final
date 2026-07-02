<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('access_restricted')->default(false)->after('is_active');
            $table->text('access_restricted_reason')->nullable()->after('access_restricted');
            $table->foreignId('access_restricted_by_id')->nullable()->constrained('users')->nullOnDelete()->after('access_restricted_reason');
            $table->timestamp('access_restricted_at')->nullable()->after('access_restricted_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['access_restricted_by_id']);
            $table->dropColumn(['access_restricted', 'access_restricted_reason', 'access_restricted_by_id', 'access_restricted_at']);
        });
    }
};
