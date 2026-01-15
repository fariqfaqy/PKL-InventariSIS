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
        Schema::table('request_barang', function (Blueprint $table) {
            // Add request_type ENUM column
            $table->enum('request_type', ['normal', 'change', 'cancellation'])
                ->default('normal')
                ->after('tipe_request');
        });
        
        // Migrate existing data
        // - Normal requests: parent_request_id IS NULL
        // - Change requests: parent_request_id NOT NULL AND keperluan NOT LIKE '%PEMBATALAN%'
        // - Cancellation requests: parent_request_id NOT NULL AND keperluan LIKE '%PEMBATALAN%'
        
        DB::statement("
            UPDATE request_barang 
            SET request_type = CASE
                WHEN parent_request_id IS NULL THEN 'normal'
                WHEN parent_request_id IS NOT NULL AND (
                    UPPER(keperluan) LIKE '%PEMBATALAN%' OR 
                    UPPER(catatan_user) LIKE '%PEMBATALAN%'
                ) THEN 'cancellation'
                WHEN parent_request_id IS NOT NULL THEN 'change'
                ELSE 'normal'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_barang', function (Blueprint $table) {
            $table->dropColumn('request_type');
        });
    }
};
