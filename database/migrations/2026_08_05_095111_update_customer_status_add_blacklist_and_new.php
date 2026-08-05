<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing customers with 'pending' or 'rejected' status to 'new'
        // Keep 'active' as is
        DB::table('customers')
            ->whereIn('status', ['pending', 'rejected'])
            ->update(['status' => 'new']);
        
        // Now we have: new, active, blacklist as valid statuses
        // Default for new customer registrations should be 'new'
        Schema::table('customers', function (Blueprint $table) {
            $table->string('status')->default('new')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'new' back to 'pending' and 'blacklist' to 'active'
        DB::table('customers')
            ->where('status', 'new')
            ->update(['status' => 'pending']);
            
        DB::table('customers')
            ->where('status', 'blacklist')
            ->update(['status' => 'active']);
            
        Schema::table('customers', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }
};
