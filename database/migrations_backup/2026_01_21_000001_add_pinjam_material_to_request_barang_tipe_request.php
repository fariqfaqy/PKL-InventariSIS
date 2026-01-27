<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraint
        DB::statement('ALTER TABLE request_barang DROP CONSTRAINT IF EXISTS request_barang_tipe_request_check');
        
        // Add new constraint with pinjam_material
        DB::statement("
            ALTER TABLE request_barang 
            ADD CONSTRAINT request_barang_tipe_request_check 
            CHECK (tipe_request IN ('pinjam_sewa', 'pakai_habis_pakai', 'pinjam_material'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new constraint
        DB::statement('ALTER TABLE request_barang DROP CONSTRAINT IF EXISTS request_barang_tipe_request_check');
        
        // Restore old constraint
        DB::statement("
            ALTER TABLE request_barang 
            ADD CONSTRAINT request_barang_tipe_request_check 
            CHECK (tipe_request IN ('pinjam_sewa', 'pakai_habis_pakai'))
        ");
    }
};
