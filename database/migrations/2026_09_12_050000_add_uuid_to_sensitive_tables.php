<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tables that get an opaque routing UUID: users, invoices, finance
     * entries, payroll runs/entries, vendors — the models where a guessable
     * sequential ID in the URL is an actual information-disclosure risk.
     */
    private array $tables = ['users', 'invoices', 'finance_entries', 'payroll_runs', 'payroll_entries', 'vendors'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable()->unique()->after('id');
            });

            DB::table($table)->whereNull('uuid')->orderBy('id')->chunkById(500, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('uuid');
            });
        }
    }
};
