<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CONSOLIDATED VERSION - Divisions table dengan semua field
     */
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_divisi');
            $table->string('kode_divisi', 5)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('kepala_divisi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add division_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('email');
            $table->string('nip', 50)->nullable()->unique()->after('division_id')
                  ->comment('Nomor Induk Pegawai');
            $table->string('jabatan', 100)->nullable()->after('nip')
                  ->comment('Jabatan pegawai');
            $table->string('no_telp', 20)->nullable()->after('jabatan')
                  ->comment('Nomor telepon');
            $table->date('tanggal_masuk')->nullable()->after('no_telp')
                  ->comment('Tanggal masuk kerja');
            
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'nip', 'jabatan', 'no_telp', 'tanggal_masuk']);
        });
        
        Schema::dropIfExists('divisions');
    }
};
