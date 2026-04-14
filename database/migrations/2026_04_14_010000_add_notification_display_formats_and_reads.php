<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_notifications', function (Blueprint $table): void {
            $table->string('display_format')->default('alert')->after('type');
        });

        Schema::create('app_notification_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('app_notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reader_key')->index();
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['app_notification_id', 'reader_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notification_reads');

        Schema::table('app_notifications', function (Blueprint $table): void {
            $table->dropColumn('display_format');
        });
    }
};
