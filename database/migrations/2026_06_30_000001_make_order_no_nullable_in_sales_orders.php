<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('order_no')->nullable()->change();
        });
    }

    public function down(): void
    {
        // First update any null order_no to a generated one
        \App\Models\SalesOrder::whereNull('order_no')->each(function ($order) {
            $order->update(['order_no' => \App\Models\SalesOrder::nextOrderNo()]);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('order_no')->nullable(false)->change();
        });
    }
};