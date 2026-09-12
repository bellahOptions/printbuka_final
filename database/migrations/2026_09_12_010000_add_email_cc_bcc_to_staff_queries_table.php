<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_queries', function (Blueprint $table) {
            $table->text('cc_emails')->nullable()->after('description');
            $table->text('bcc_emails')->nullable()->after('cc_emails');
            $table->timestamp('email_last_sent_at')->nullable()->after('bcc_emails');
            $table->unsignedInteger('email_send_count')->default(0)->after('email_last_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('staff_queries', function (Blueprint $table) {
            $table->dropColumn(['cc_emails', 'bcc_emails', 'email_last_sent_at', 'email_send_count']);
        });
    }
};
