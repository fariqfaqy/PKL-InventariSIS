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
        Schema::table('stock', function (Blueprint $table) {
            $table->enum('status_kondisi', ['digunakan', 'diperbaiki', 'rusak'])
                  ->default('digunakan')
                  ->after('stock')
                  ->comment('Status kondisi aset sewa: digunakan/diperbaiki/rusak');
            
            $table->text('keterangan_kondisi')->nullable()->after('status_kondisi')
                  ->comment('Keterangan detail kondisi barang');
            
            $table->date('tanggal_update_kondisi')->nullable()->after('keterangan_kondisi')
                  ->comment('Tanggal terakhir update kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn(['status_kondisi', 'keterangan_kondisi', 'tanggal_update_kondisi']);
        });
    }
};
