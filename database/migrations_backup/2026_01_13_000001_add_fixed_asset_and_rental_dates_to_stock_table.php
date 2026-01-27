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
        // For PostgreSQL, we need to recreate the check constraint
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_kategori_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_kategori_check CHECK (kategori IN ('barang_sewa', 'habis_pakai', 'aset_tetap'))");
        
        Schema::table('stock', function (Blueprint $table) {
            // Add rental date fields (only for barang_sewa)
            $table->date('tanggal_mulai_sewa')->nullable()->after('durasi_sewa');
            $table->date('tanggal_akhir_sewa')->nullable()->after('tanggal_mulai_sewa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original check constraint
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_kategori_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_kategori_check CHECK (kategori IN ('barang_sewa', 'habis_pakai'))");
        
        Schema::table('stock', function (Blueprint $table) {
            // Drop rental date fields
            $table->dropColumn(['tanggal_mulai_sewa', 'tanggal_akhir_sewa']);
        });
    }
};
