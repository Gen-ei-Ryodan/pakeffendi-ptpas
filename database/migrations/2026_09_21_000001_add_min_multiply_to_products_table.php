<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('min_multiply_qty')->default(1)->after('no_urut_status');
            $table->char('min_multiply_notes', 50)->nullable()->after('min_multiply_qty');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['min_multiply_qty', 'min_multiply_notes']);
        });
    }
};
