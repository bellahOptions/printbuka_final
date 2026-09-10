<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table): void {
            // consumable = depletable material (paper, ink, vinyl...); equipment = durable
            // asset tracked by condition/assignment (plates, machines, tools...)
            $table->string('item_type', 20)->default('consumable')->after('category');
            $table->string('serial_number', 100)->nullable()->after('sku');
            $table->string('condition', 30)->nullable()->after('serial_number');
            $table->foreignId('assigned_to')->nullable()->after('location')->constrained('users')->nullOnDelete();
            $table->date('purchase_date')->nullable()->after('assigned_to');
            $table->date('warranty_expiry')->nullable()->after('purchase_date');
            $table->date('last_serviced_at')->nullable()->after('warranty_expiry');
            $table->date('next_service_due')->nullable()->after('last_serviced_at');

            $table->index(['item_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropIndex(['item_type', 'is_active']);
            $table->dropColumn([
                'item_type', 'serial_number', 'condition', 'purchase_date',
                'warranty_expiry', 'last_serviced_at', 'next_service_due',
            ]);
        });
    }
};
