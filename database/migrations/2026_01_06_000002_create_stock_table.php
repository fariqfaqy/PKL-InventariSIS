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
            $table->enum('status_kondisi', ['digunakan', 'diperbaiki', 'rusak', 'tersedia', 'selesai'])
                  ->default('tersedia')
                  ->comment('Status kondisi aset: tersedia/digunakan/diperbaiki/rusak/selesai');
            $table->text('keterangan_kondisi')->nullable()
                  ->comment('Keterangan detail kondisi barang');
            $table->date('tanggal_update_kondisi')->nullable()
                  ->comment('Tanggal terakhir update kondisi');
            
            // Image
            $table->string('image')->nullable();
            
            // Rack (Storage Location) - Nullable untuk fleksibilitas
            $table->enum('rack', ['1a', '1b', '1c', '2a', '2b', '2c'])->nullable();
            
            // Category & Type
            $table->enum('kategori', ['aset_sewa', 'material_umum', 'aset_tetap'])
                  ->default('material_umum')
                  ->comment('Kategori utama barang: Aset Sewa, Material Umum, Aset Tetap');
            $table->string('sub_kategori', 50)->nullable()
                  ->comment('Sub kategori untuk Material Umum: barang_habis_pakai atau barang_pinjam');
            $table->string('jenis', 100)->nullable()
                  ->comment('Jenis barang (misalnya: Elektronik, ATK, dll)');
            $table->string('merek', 100)->nullable()
                  ->comment('Merek/brand barang');
            $table->string('tipe', 255)->nullable()
                  ->comment('Tipe/model spesifik barang');
            
            // Rental Info (untuk aset_sewa)
            $table->integer('durasi_sewa')->nullable()
                  ->comment('Durasi sewa dalam bulan (hanya untuk aset_sewa)');
            $table->date('tanggal_mulai_sewa')->nullable()
                  ->comment('Tanggal mulai sewa (hanya untuk aset_sewa)');
            $table->date('tanggal_akhir_sewa')->nullable()
                  ->comment('Tanggal akhir sewa (hanya untuk aset_sewa)');
            
            // Usage Info (untuk tracking pemakaian aset_sewa)
            $table->unsignedBigInteger('user_id')->nullable()
                  ->comment('User yang sedang menggunakan aset (hanya untuk aset_sewa)');
            $table->date('tanggal_mulai_pakai')->nullable()
                  ->comment('Tanggal mulai pakai aktual oleh user');
            $table->date('tanggal_akhir_pakai')->nullable()
                  ->comment('Tanggal akhir pakai aktual oleh user');
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // ✅ CARA LARAVEL: Index menggunakan method Laravel
            // Index untuk performance query berdasarkan kategori dan status
            $table->index(['kategori', 'status_kondisi'], 'idx_stock_kategori_status');
        });
        
        // ❌ CHECK Constraint harus pakai raw SQL (Laravel tidak support)
        // Validasi: aset_sewa stock <= 1 (allow 0 saat selesai)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE stock 
                ADD CONSTRAINT check_aset_sewa_qty 
                CHECK (kategori != 'aset_sewa' OR stock <= 1)
            ");
            
            // ❌ COMMENT juga harus pakai raw SQL (PostgreSQL specific)
            DB::statement("COMMENT ON COLUMN stock.kodebarang IS 'Kode unik barang - untuk aset_sewa harus benar-benar unik (1 kode = 1 item fisik)'");
            DB::statement("COMMENT ON COLUMN stock.status_kondisi IS 'Status kondisi aset: tersedia (ready), digunakan (in use), diperbaiki (under repair), rusak (broken), hilang (lost)'");
        }
        
        // Create stock_histories table untuk real-time tracking
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->string('event_type'); // created, user_assigned, period_extended, status_changed, completed, stock_updated
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('stock_id')->references('idbarang')->on('stock')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('stock');
    }
};
