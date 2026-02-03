<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration gabungan untuk fitur Aset Sewa:
     * 1. Tambah kolom user_id, tanggal_mulai_pakai, tanggal_akhir_pakai ke stock
     * 2. Buat table stock_histories untuk tracking
     * 3. Update constraint check_aset_sewa_qty untuk allow stock=0
     */
    public function up(): void
    {
        // 1. Tambah kolom ke stock table untuk Aset Sewa (hanya jika belum ada)
        Schema::table('stock', function (Blueprint $table) {
            if (!Schema::hasColumn('stock', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('status_kondisi');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('stock', 'tanggal_mulai_pakai')) {
                $table->date('tanggal_mulai_pakai')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('stock', 'tanggal_akhir_pakai')) {
                $table->date('tanggal_akhir_pakai')->nullable()->after('tanggal_mulai_pakai');
            }
        });

        // 2. Buat table stock_histories untuk real-time tracking (hanya jika belum ada)
        if (!Schema::hasTable('stock_histories')) {
            Schema::create('stock_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_id');
                $table->string('event_type'); // created, user_assigned, period_extended, status_changed, completed, stock_updated
                $table->text('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->foreign('stock_id')->references('idbarang')->on('stock')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->index('event_type');
                $table->index('created_at');
            });
        }

        // 3. Update constraint untuk allow aset_sewa stock = 0 (saat selesai)
        DB::statement('ALTER TABLE stock DROP CONSTRAINT IF EXISTS check_aset_sewa_qty');
        DB::statement("ALTER TABLE stock ADD CONSTRAINT check_aset_sewa_qty CHECK (kategori != 'aset_sewa' OR stock <= 1)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraint
        DB::statement('ALTER TABLE stock DROP CONSTRAINT IF EXISTS check_aset_sewa_qty');
        DB::statement("ALTER TABLE stock ADD CONSTRAINT check_aset_sewa_qty CHECK (kategori != 'aset_sewa' OR stock = 1)");

        // Drop stock_histories table
        Schema::dropIfExists('stock_histories');

        // Drop columns from stock
        Schema::table('stock', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'tanggal_mulai_pakai', 'tanggal_akhir_pakai']);
        });
    }
};
