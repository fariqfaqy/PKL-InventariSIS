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
        // Update constraint to include barang_pinjam
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_kategori_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_kategori_check CHECK (kategori IN ('barang_sewa', 'habis_pakai', 'barang_pinjam', 'aset_tetap'))");
        
        // Add sub_kategori column
        Schema::table('stock', function (Blueprint $table) {
            $table->enum('sub_kategori', ['habis_pakai', 'barang_pinjam'])
                  ->nullable()
                  ->after('kategori')
                  ->comment('Sub-kategori untuk Material Umum: habis_pakai atau barang_pinjam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn('sub_kategori');
        });
        
        // Revert constraint back to original
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_kategori_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_kategori_check CHECK (kategori IN ('barang_sewa', 'habis_pakai', 'aset_tetap'))");
    }
};
