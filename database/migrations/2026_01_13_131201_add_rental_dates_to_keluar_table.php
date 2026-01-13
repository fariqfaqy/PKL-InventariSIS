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
            $table->date('tanggal_mulai_sewa')->nullable()->after('tanggal_kembali');
            $table->date('tanggal_akhir_sewa')->nullable()->after('tanggal_mulai_sewa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluar', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_sewa', 'tanggal_akhir_sewa']);
        });
    }
};
