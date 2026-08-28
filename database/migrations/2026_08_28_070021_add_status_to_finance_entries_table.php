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
        Schema::table('finance_entries', function (Blueprint $table) {
            // Only meaningful for type === 'income' — an invoice payment that
            // was later reversed. Expense entries stay 'completed' forever.
            $table->string('status')->default('completed')->after('entry_type');
            $table->foreignId('refunded_by_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('refunded_at')->nullable()->after('refunded_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('refunded_by_id');
            $table->dropColumn(['status', 'refunded_at']);
        });
    }
};
