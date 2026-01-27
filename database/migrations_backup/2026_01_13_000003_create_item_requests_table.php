<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_barang', function (Blueprint $table) {
            $table->id('id_request');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('idbarang');
            $table->integer('qty');
            $table->enum('tipe_request', ['pinjam_sewa', 'pakai_habis_pakai'])->comment('Pinjam aset sewa atau pakai material umum');
            $table->date('tanggal_mulai_sewa')->nullable()->comment('Untuk aset sewa');
            $table->date('tanggal_akhir_sewa')->nullable()->comment('Untuk aset sewa');
            $table->text('keperluan')->nullable();
            $table->enum('status', ['pending', 'approved', 'processing', 'rejected', 'completed'])->default('pending');
            $table->text('catatan_user')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('diproses_oleh')->nullable()->comment('Admin yang memproses');
            $table->timestamp('tanggal_request')->useCurrent();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('idbarang')->references('idbarang')->on('stock')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_barang');
    }
};
