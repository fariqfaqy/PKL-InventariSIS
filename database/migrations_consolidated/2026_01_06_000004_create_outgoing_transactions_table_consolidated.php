<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED VERSION - Menggabungkan semua perubahan keluar table
     */
    public function up(): void
    {
        Schema::create('keluar', function (Blueprint $table) {
            // Primary
            $table->id('idkeluar');
            
            // Relations
            $table->unsignedBigInteger('id_request')->nullable()
                  ->comment('Link ke request_barang (jika dari request user)');
            $table->unsignedBigInteger('idbarang');
            $table->unsignedBigInteger('user_id')->nullable()
                  ->comment('ID user yang menggunakan barang');
            
            // Transaction Info
            $table->dateTime('tanggal');
            $table->string('penerima');
            $table->integer('qty');
            $table->string('namabarang_k');
            $table->string('kodebarang_k');
            $table->string('penginput');
            
            // Type & Category
            $table->string('tipe')->nullable()
                  ->comment('Tipe transaksi: peminjaman/permintaan');
            $table->string('kategori')->nullable()
                  ->comment('Kategori barang: barang_sewa, habis_pakai, aset_tetap');
            
            // Rental Duration (untuk barang_sewa)
            $table->integer('durasi_sewa')->nullable()
                  ->comment('Durasi sewa dalam bulan (hanya untuk barang_sewa)');
            
            // Status & Completion
            $table->enum('status', ['sedang_dipakai', 'selesai'])
                  ->default('sedang_dipakai')
                  ->comment('Status pemakaian barang');
            $table->dateTime('tanggal_selesai')->nullable()
                  ->comment('Tanggal barang dikembalikan/selesai digunakan');
            
            // Approval System
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->comment('Status approval dari admin');
            $table->string('tipe_request')->nullable()
                  ->comment('Tipe request: pinjam_sewa, pakai_habis_pakai, pinjam_material');
            $table->text('catatan_admin')->nullable()
                  ->comment('Catatan dari admin saat memproses');
            $table->string('diproses_oleh')->nullable()
                  ->comment('Nama admin yang memproses');
            $table->dateTime('tanggal_diproses')->nullable()
                  ->comment('Tanggal saat admin memproses');
            
            // Rental Dates (untuk tracking peminjaman)
            $table->date('tanggal_pinjam')->nullable()
                  ->comment('Tanggal mulai pinjam (user input)');
            $table->date('tanggal_kembali')->nullable()
                  ->comment('Tanggal rencana pengembalian (user input)');
            $table->date('tanggal_mulai_sewa')->nullable()
                  ->comment('Tanggal mulai sewa (dari stock)');
            $table->date('tanggal_akhir_sewa')->nullable()
                  ->comment('Tanggal akhir sewa (dari stock)');
            $table->date('tanggal_mulai_pakai')->nullable()
                  ->comment('Tanggal mulai pakai (alternatif)');
            $table->date('tanggal_akhir_pakai')->nullable()
                  ->comment('Tanggal akhir pakai (alternatif)');
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('idbarang')->references('idbarang')->on('stock')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // Note: id_request foreign key akan ditambahkan setelah request_barang table dibuat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluar');
    }
};
