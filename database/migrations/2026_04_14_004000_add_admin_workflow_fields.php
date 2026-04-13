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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('avatar');
            $table->string('department')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('department');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('job_order_number')->nullable()->unique()->after('id');
            $table->string('priority')->default('🟡 Normal')->after('status');
            $table->foreignId('brief_received_by_id')->nullable()->after('priority')->constrained('users')->nullOnDelete();
            $table->timestamp('brief_received_at')->nullable()->after('brief_received_by_id');
            $table->foreignId('assigned_designer_id')->nullable()->after('brief_received_at')->constrained('users')->nullOnDelete();
            $table->timestamp('design_started_at')->nullable()->after('assigned_designer_id');
            $table->timestamp('design_approved_at')->nullable()->after('design_started_at');
            $table->foreignId('production_officer_id')->nullable()->after('design_approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('production_started_at')->nullable()->after('production_officer_id');
            $table->string('material_substrate')->nullable()->after('production_started_at');
            $table->string('finish_lamination')->nullable()->after('material_substrate');
            $table->foreignId('qc_checked_by_id')->nullable()->after('finish_lamination')->constrained('users')->nullOnDelete();
            $table->timestamp('qc_checked_at')->nullable()->after('qc_checked_by_id');
            $table->string('qc_result')->nullable()->after('qc_checked_at');
            $table->timestamp('estimated_delivery_at')->nullable()->after('qc_result');
            $table->timestamp('actual_delivery_at')->nullable()->after('estimated_delivery_at');
            $table->string('delivery_method')->nullable()->after('actual_delivery_at');
            $table->foreignId('dispatched_by_id')->nullable()->after('delivery_method')->constrained('users')->nullOnDelete();
            $table->string('client_review_status')->nullable()->after('dispatched_by_id');
            $table->text('after_sales_action')->nullable()->after('client_review_status');
            $table->timestamp('after_sales_resolved_at')->nullable()->after('after_sales_action');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('after_sales_resolved_at');
            $table->string('payment_status')->default('Awaiting Invoice')->after('amount_paid');
            $table->text('internal_notes')->nullable()->after('payment_status');
            $table->foreignId('verified_by_id')->nullable()->after('internal_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brief_received_by_id');
            $table->dropConstrainedForeignId('assigned_designer_id');
            $table->dropConstrainedForeignId('production_officer_id');
            $table->dropConstrainedForeignId('qc_checked_by_id');
            $table->dropConstrainedForeignId('dispatched_by_id');
            $table->dropConstrainedForeignId('verified_by_id');
            $table->dropColumn([
                'job_order_number',
                'priority',
                'brief_received_at',
                'design_started_at',
                'design_approved_at',
                'production_started_at',
                'material_substrate',
                'finish_lamination',
                'qc_checked_at',
                'qc_result',
                'estimated_delivery_at',
                'actual_delivery_at',
                'delivery_method',
                'client_review_status',
                'after_sales_action',
                'after_sales_resolved_at',
                'amount_paid',
                'payment_status',
                'internal_notes',
                'verified_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'is_active']);
        });
    }
};
