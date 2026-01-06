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
        Schema::create('masuk', function (Blueprint $table) {
            $table->id('idmasuk');
            $table->unsignedBigInteger('idbarang');
            $table->timestamp('tanggal')->useCurrent();
            $table->text('keterangan');
            $table->integer('qty');
            $table->string('namabarang_m');
            $table->string('penginput');
            $table->string('kodebarang_m');
            $table->timestamps();

            // Foreign key
            $table->foreign('idbarang')->references('idbarang')->on('stock')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masuk');
    }
};
