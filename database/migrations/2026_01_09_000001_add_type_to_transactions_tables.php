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
        // Add tipe column to masuk table
        Schema::table('masuk', function (Blueprint $table) {
            $table->enum('tipe', ['peminjaman', 'permintaan'])->default('permintaan')->after('kodebarang_m');
        });

        // Add tipe column to keluar table
        Schema::table('keluar', function (Blueprint $table) {
            $table->enum('tipe', ['peminjaman', 'permintaan'])->default('permintaan')->after('kodebarang_k');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masuk', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });

        Schema::table('keluar', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
