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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('role')->constrained('divisions')->onDelete('set null');
            $table->string('nip', 50)->nullable()->after('division_id')->unique();
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('no_telp', 20)->nullable()->after('jabatan');
            $table->date('tanggal_masuk')->nullable()->after('no_telp');
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
    }
};
