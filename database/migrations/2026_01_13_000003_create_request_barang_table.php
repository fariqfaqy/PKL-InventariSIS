<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED VERSION - Menggabungkan semua perubahan request_barang table
     */
    public function up(): void
    {
        Schema::create('request_barang', function (Blueprint $table) {
            // Primary
            $table->id('id_request');
            
            // Relations
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('idbarang')->index();
            $table->unsignedBigInteger('parent_request_id')->nullable()->index()
                  ->comment('ID request parent (untuk change/cancellation request)');
            
            // Request Info
            $table->integer('qty');
            $table->enum('tipe_request', [
                'pakai_habis_pakai',     // Material Umum - Barang Habis Pakai
                'pinjam_material'        // Material Umum - Barang Pinjam
            ])->comment('Tipe request untuk Material Umum (pegawai request)');
            
            $table->enum('request_type', ['normal', 'change', 'cancellation'])
                  ->default('normal')
                  ->comment('Tipe request: normal/change/cancellation');
            
            // Rental Dates (untuk pinjam_material)
            $table->date('tanggal_mulai_sewa')->nullable()
                  ->comment('Tanggal mulai pinjam (untuk pinjam_material)');
            $table->date('tanggal_akhir_sewa')->nullable()
                  ->comment('Tanggal akhir pinjam (untuk pinjam_material)');
            
            // Additional Info
            $table->text('keperluan')->nullable()
                  ->comment('Keperluan/alasan request');
            $table->string('penerima')->nullable()
                  ->comment('Nama penerima barang (bisa berbeda dengan user)');
            
            // Status & Processing
            $table->enum('status', ['pending', 'approved', 'processing', 'rejected', 'completed', 'cancelled'])
                  ->default('pending')
                  ->comment('Status request');
            $table->text('catatan_user')->nullable()
                  ->comment('Catatan dari user');
            $table->text('catatan_admin')->nullable()
                  ->comment('Catatan dari admin saat memproses');
            $table->string('diproses_oleh')->nullable()
                  ->comment('Nama admin yang memproses');
            
            // Timestamps
            $table->timestamp('tanggal_request')->useCurrent()
                  ->comment('Tanggal request dibuat');
            $table->timestamp('tanggal_diproses')->nullable()
                  ->comment('Tanggal saat admin memproses');
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('idbarang')->references('idbarang')->on('stock')->onDelete('cascade');
            $table->foreign('parent_request_id')->references('id_request')->on('request_barang')->onDelete('cascade');
        });
        
        // Now add foreign key to keluar table
        Schema::table('keluar', function (Blueprint $table) {
            $table->foreign('id_request')->references('id_request')->on('request_barang')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key from keluar table first (if exists)
        if (Schema::hasTable('keluar')) {
            Schema::table('keluar', function (Blueprint $table) {
                $table->dropForeign(['id_request']);
            });
        }
        
        Schema::dropIfExists('request_barang');
    }
};
