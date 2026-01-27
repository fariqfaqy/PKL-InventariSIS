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
        Schema::create('stock', function (Blueprint $table) {
            $table->id('idbarang');
            $table->string('namabarang');
            $table->text('deskripsi')->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->string('penginput');
            $table->string('kodebarang')->unique();
            $table->enum('rack', ['1a', '1b', '1c', '2a', '2b', '2c']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
