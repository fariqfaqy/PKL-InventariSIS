<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('keluar', function (Blueprint $table) {
            // Add user_id for relationship
            if (!Schema::hasColumn('keluar', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('penerima');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
        
        // Rename columns using raw SQL (Laravel's renameColumn has issues with PostgreSQL)
        DB::statement('ALTER TABLE keluar RENAME COLUMN tanggal_mulai_sewa TO tanggal_mulai_pakai');
        DB::statement('ALTER TABLE keluar RENAME COLUMN tanggal_akhir_sewa TO tanggal_akhir_pakai');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse rename
        DB::statement('ALTER TABLE keluar RENAME COLUMN tanggal_mulai_pakai TO tanggal_mulai_sewa');
        DB::statement('ALTER TABLE keluar RENAME COLUMN tanggal_akhir_pakai TO tanggal_akhir_sewa');
        
        Schema::table('keluar', function (Blueprint $table) {
            if (Schema::hasColumn('keluar', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
