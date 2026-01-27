<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED VERSION - Menggabungkan semua perubahan stock table
     */
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            // Primary & Basic Info
            $table->id('idbarang');
            $table->string('namabarang');
            $table->text('deskripsi')->nullable();
            $table->string('kodebarang')->unique();
            $table->string('penginput');
            
            // Stock & Status
            $table->integer('stock')->default(0);
            $table->enum('status_kondisi', ['digunakan', 'diperbaiki', 'rusak', 'tersedia'])
                  ->default('tersedia')
                  ->comment('Status kondisi aset: tersedia/digunakan/diperbaiki/rusak');
            $table->text('keterangan_kondisi')->nullable()
                  ->comment('Keterangan detail kondisi barang');
            $table->date('tanggal_update_kondisi')->nullable()
                  ->comment('Tanggal terakhir update kondisi');
            
            // Image
            $table->string('image')->nullable();
            
            // Rack (Storage Location) - Nullable untuk fleksibilitas
            $table->enum('rack', ['1a', '1b', '1c', '2a', '2b', '2c'])->nullable();
            
            // Category & Type
            $table->enum('kategori', ['barang_sewa', 'habis_pakai', 'aset_tetap'])
                  ->default('habis_pakai')
                  ->comment('Kategori utama barang');
            $table->string('sub_kategori', 50)->nullable()
                  ->comment('Sub kategori untuk habis_pakai: barang_habis_pakai atau barang_pinjam');
            $table->string('jenis', 100)->nullable()
                  ->comment('Jenis barang (misalnya: Elektronik, ATK, dll)');
            $table->string('merek', 100)->nullable()
                  ->comment('Merek/brand barang');
            $table->string('tipe', 255)->nullable()
                  ->comment('Tipe/model spesifik barang');
            
            // Rental Info (untuk barang_sewa)
            $table->integer('durasi_sewa')->nullable()
                  ->comment('Durasi sewa dalam bulan (hanya untuk barang_sewa)');
            $table->date('tanggal_mulai_sewa')->nullable()
                  ->comment('Tanggal mulai sewa (hanya untuk barang_sewa)');
            $table->date('tanggal_akhir_sewa')->nullable()
                  ->comment('Tanggal akhir sewa (hanya untuk barang_sewa)');
            
            // Timestamps
            $table->timestamps();
        });

        // Add constraint untuk sub_kategori validation
        DB::statement("
            ALTER TABLE stock 
            ADD CONSTRAINT check_sub_kategori 
            CHECK (sub_kategori IN ('barang_habis_pakai', 'barang_pinjam') OR sub_kategori IS NULL)
        ");
        
        // Add constraint untuk kategori validation
        DB::statement("
            ALTER TABLE stock 
            ADD CONSTRAINT stock_kategori_check 
            CHECK (kategori IN ('barang_sewa', 'habis_pakai', 'aset_tetap'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
