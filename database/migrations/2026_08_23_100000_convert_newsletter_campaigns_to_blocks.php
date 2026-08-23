<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_campaigns', function (Blueprint $table): void {
            $table->json('blocks')->nullable()->after('preheader');
        });

        Schema::table('newsletter_campaigns', function (Blueprint $table): void {
            $table->dropColumn(['headline', 'message', 'cta_label', 'cta_url']);
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_campaigns', function (Blueprint $table): void {
            $table->string('headline')->nullable();
            $table->text('message')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url', 2048)->nullable();
        });

        Schema::table('newsletter_campaigns', function (Blueprint $table): void {
            $table->dropColumn('blocks');
        });
    }
};
