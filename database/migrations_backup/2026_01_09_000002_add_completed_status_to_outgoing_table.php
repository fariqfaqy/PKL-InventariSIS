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
            $table->enum('status', ['sedang_dipakai', 'selesai'])->default('sedang_dipakai')->after('durasi_sewa');
            $table->timestamp('tanggal_selesai')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluar', function (Blueprint $table) {
            $table->dropColumn(['status', 'tanggal_selesai']);
        });
    }
};
