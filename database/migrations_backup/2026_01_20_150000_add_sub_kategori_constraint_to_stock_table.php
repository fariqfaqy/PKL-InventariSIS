<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Struktur Kategori:
     * - habis_pakai (Material Umum) -> sub: barang_habis_pakai, barang_pinjam
     * - barang_sewa (Aset Sewa) -> hanya admin
     * - aset_tetap (Aset Tetap) -> hanya admin
     */
    public function up(): void
    {
        // Drop existing constraint first (jika ada yang konflik)
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_sub_kategori_check");
        
        Schema::table('stock', function (Blueprint $table) {
            // Pastikan kolom sub_kategori ada dan nullable
            if (!Schema::hasColumn('stock', 'sub_kategori')) {
                $table->string('sub_kategori', 50)->nullable()->after('kategori');
            }
        });

        // TIDAK update data existing - biarkan NULL dulu
        // Admin yang akan set manual lewat form edit
        
        // Add check constraint untuk sub_kategori (hanya validasi value yang allowed)
        DB::statement("
            ALTER TABLE stock 
            ADD CONSTRAINT stock_sub_kategori_check 
            CHECK (
                sub_kategori IN ('barang_habis_pakai', 'barang_pinjam') OR sub_kategori IS NULL
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraint
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_sub_kategori_check");
    }
};
