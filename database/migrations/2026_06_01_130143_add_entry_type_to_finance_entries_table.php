<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds entry_type to distinguish manual expense/income from auto-generated entries
     * and "Credit from CEO" funding entries.
     */
    public function up(): void
    {
        Schema::table('finance_entries', function (Blueprint $table) {
            $table->string('entry_type', 50)->nullable()->after('type')
                ->comment('Manual entry sub-type: null (default), credit_from_ceo, auto_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_entries', function (Blueprint $table) {
            $table->dropColumn('entry_type');
        });
    }
};
