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
        // PostgreSQL: Drop old constraint and add new one with 'selesai' value
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_status_kondisi_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_status_kondisi_check CHECK (status_kondisi::text = ANY (ARRAY['digunakan'::character varying, 'diperbaiki'::character varying, 'rusak'::character varying, 'tersedia'::character varying, 'selesai'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Restore original constraint without 'selesai'
        DB::statement("ALTER TABLE stock DROP CONSTRAINT IF EXISTS stock_status_kondisi_check");
        DB::statement("ALTER TABLE stock ADD CONSTRAINT stock_status_kondisi_check CHECK (status_kondisi::text = ANY (ARRAY['digunakan'::character varying, 'diperbaiki'::character varying, 'rusak'::character varying, 'tersedia'::character varying]::text[]))");
    }
};
