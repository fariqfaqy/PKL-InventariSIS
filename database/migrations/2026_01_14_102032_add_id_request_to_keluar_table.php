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
            $table->unsignedBigInteger('id_request')->nullable()->after('idkeluar');
            $table->foreign('id_request')->references('id_request')->on('request_barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluar', function (Blueprint $table) {
            $table->dropForeign(['id_request']);
            $table->dropColumn('id_request');
        });
    }
};
