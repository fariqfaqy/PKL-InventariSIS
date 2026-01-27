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
        // PostgreSQL: Alter constraint to add 'cancelled' status
        DB::statement("
            ALTER TABLE request_barang 
            DROP CONSTRAINT IF EXISTS request_barang_status_check;
        ");
        
        DB::statement("
            ALTER TABLE request_barang 
            ADD CONSTRAINT request_barang_status_check 
            CHECK (status IN ('pending', 'approved', 'processing', 'rejected', 'completed', 'cancelled'));
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE request_barang 
            DROP CONSTRAINT IF EXISTS request_barang_status_check;
        ");
        
        DB::statement("
            ALTER TABLE request_barang 
            ADD CONSTRAINT request_barang_status_check 
            CHECK (status IN ('pending', 'approved', 'processing', 'rejected', 'completed'));
        ");
    }
};
