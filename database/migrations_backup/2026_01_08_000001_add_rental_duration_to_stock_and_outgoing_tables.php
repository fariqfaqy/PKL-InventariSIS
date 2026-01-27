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
        // Add durasi_sewa to stock table
        Schema::table('stock', function (Blueprint $table) {
            $table->integer('durasi_sewa')->nullable()->after('kategori')->comment('Durasi sewa dalam bulan (hanya untuk barang_sewa)');
        });

        // Add durasi_sewa to keluar table
        Schema::table('keluar', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('kodebarang_k')->comment('Kategori barang: barang_sewa atau habis_pakai');
            $table->integer('durasi_sewa')->nullable()->after('kategori')->comment('Durasi sewa dalam bulan (hanya untuk barang_sewa)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn('durasi_sewa');
        });

        Schema::table('keluar', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'durasi_sewa']);
        });
    }
};
