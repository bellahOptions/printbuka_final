<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename pending → order_received, shipped → dispatched
        DB::table('shop_orders')->where('fulfillment_status', 'pending')->update(['fulfillment_status' => 'order_received']);
        DB::table('shop_orders')->where('fulfillment_status', 'shipped')->update(['fulfillment_status' => 'dispatched']);
    }

    public function down(): void
    {
        DB::table('shop_orders')->where('fulfillment_status', 'order_received')->update(['fulfillment_status' => 'pending']);
        DB::table('shop_orders')->where('fulfillment_status', 'dispatched')->update(['fulfillment_status' => 'shipped']);
    }
};
