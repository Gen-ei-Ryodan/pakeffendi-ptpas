<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('sales_id')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->index(['sales_id', 'customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['sales_id', 'customer_id', 'status']);
            $table->dropConstrainedForeignId('sales_id');
        });
    }
};
