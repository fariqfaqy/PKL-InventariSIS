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
            $table->enum('kategori', ['barang_sewa', 'habis_pakai'])->default('habis_pakai')->after('rack');
            $table->string('jenis', 100)->nullable()->after('kategori');
            $table->string('merek', 100)->nullable()->after('jenis');
            $table->string('tipe', 255)->nullable()->after('merek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'jenis', 'merek', 'tipe']);
        });
    }
};
