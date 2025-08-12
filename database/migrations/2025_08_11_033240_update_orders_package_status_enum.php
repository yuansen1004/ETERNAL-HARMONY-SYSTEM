<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the enum column to allow the new values
        DB::statement("ALTER TABLE orders MODIFY COLUMN package_status ENUM('active', 'inactive', 'expired', 'pending', 'complete') DEFAULT 'active'");
        
        // Then update existing data to map old values to new ones
        DB::statement("UPDATE orders SET package_status = 'pending' WHERE package_status IN ('active', 'inactive')");
        DB::statement("UPDATE orders SET package_status = 'complete' WHERE package_status = 'expired'");
        
        // Finally, remove the old enum values
        DB::statement("ALTER TABLE orders MODIFY COLUMN package_status ENUM('pending', 'complete') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, modify the enum column to allow the old values
        DB::statement("ALTER TABLE orders MODIFY COLUMN package_status ENUM('pending', 'complete', 'active', 'inactive', 'expired') DEFAULT 'pending'");
        
        // Revert the data changes
        DB::statement("UPDATE orders SET package_status = 'active' WHERE package_status = 'pending'");
        DB::statement("UPDATE orders SET package_status = 'expired' WHERE package_status = 'complete'");
        
        // Finally, revert to the original enum column
        DB::statement("ALTER TABLE orders MODIFY COLUMN package_status ENUM('active', 'inactive', 'expired') DEFAULT 'active'");
    }
};
