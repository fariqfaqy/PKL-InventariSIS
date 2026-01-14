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
        Schema::table('request_barang', function (Blueprint $table) {
            $table->integer('parent_request_id')->nullable()->after('id_request');
            $table->foreign('parent_request_id')->references('id_request')->on('request_barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_barang', function (Blueprint $table) {
            $table->dropForeign(['parent_request_id']);
            $table->dropColumn('parent_request_id');
        });
    }
};
