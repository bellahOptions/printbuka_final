<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            if (! Schema::hasColumn('trainings', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('trainings', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('trainings', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('last_name');
            }

            if (! Schema::hasColumn('trainings', 'gender')) {
                $table->string('gender')->nullable()->after('date_of_birth');
            }

            if (! Schema::hasColumn('trainings', 'phone_whatsapp')) {
                $table->string('phone_whatsapp', 50)->nullable()->after('gender');
            }

            if (! Schema::hasColumn('trainings', 'email')) {
                $table->string('email')->nullable()->after('phone_whatsapp');
            }

            if (! Schema::hasColumn('trainings', 'contact_address')) {
                $table->text('contact_address')->nullable()->after('email');
            }

            if (! Schema::hasColumn('trainings', 'city_state')) {
                $table->string('city_state')->nullable()->after('contact_address');
            }

            if (! Schema::hasColumn('trainings', 'educational_qualification')) {
                $table->string('educational_qualification')->nullable()->after('city_state');
            }

            if (! Schema::hasColumn('trainings', 'desired_skill')) {
                $table->string('desired_skill')->nullable()->after('educational_qualification');
            }

            if (! Schema::hasColumn('trainings', 'employment_status')) {
                $table->string('employment_status')->nullable()->after('desired_skill');
            }

            if (! Schema::hasColumn('trainings', 'experience_level')) {
                $table->string('experience_level')->nullable()->after('employment_status');
            }

            if (! Schema::hasColumn('trainings', 'has_laptop')) {
                $table->boolean('has_laptop')->default(false)->after('experience_level');
            }

            if (! Schema::hasColumn('trainings', 'availability')) {
                $table->string('availability')->nullable()->after('has_laptop');
            }

            if (! Schema::hasColumn('trainings', 'portfolio_url')) {
                $table->string('portfolio_url', 500)->nullable()->after('availability');
            }

            if (! Schema::hasColumn('trainings', 'motivation')) {
                $table->text('motivation')->nullable()->after('portfolio_url');
            }

            if (! Schema::hasColumn('trainings', 'referral_source')) {
                $table->string('referral_source')->nullable()->after('motivation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table): void {
            foreach ([
                'first_name',
                'last_name',
                'date_of_birth',
                'gender',
                'phone_whatsapp',
                'email',
                'contact_address',
                'city_state',
                'educational_qualification',
                'desired_skill',
                'employment_status',
                'experience_level',
                'has_laptop',
                'availability',
                'portfolio_url',
                'motivation',
                'referral_source',
            ] as $column) {
                if (Schema::hasColumn('trainings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
