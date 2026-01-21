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
        Schema::table('keluar', function (Blueprint $table) {
            // Ubah status menjadi approval status
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending')->after('kodebarang_k');
            
            // Tanggal peminjaman dan pengembalian untuk aset sewa
            $table->date('tanggal_pinjam')->nullable()->after('tanggal');
            $table->date('tanggal_kembali')->nullable()->after('tanggal_pinjam');
            
            // Tipe request: peminjaman (sewa) atau permintaan (habis pakai)
            $table->enum('tipe_request', ['peminjaman', 'permintaan'])->default('permintaan')->after('status_approval');
            
            // Catatan admin saat approve/reject
            $table->text('catatan_admin')->nullable()->after('status_approval');
            $table->string('diproses_oleh')->nullable()->after('catatan_admin');
            $table->timestamp('tanggal_diproses')->nullable()->after('diproses_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluar', function (Blueprint $table) {
            $table->dropColumn([
                'status_approval',
                'tanggal_pinjam',
                'tanggal_kembali',
                'tipe_request',
                'catatan_admin',
                'diproses_oleh',
                'tanggal_diproses'
            ]);
        });
    }
};
