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
        Schema::table('stock', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['current_user_id']);
            // Then drop column
            $table->dropColumn('current_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->unsignedBigInteger('current_user_id')->nullable()->after('status_kondisi');
            $table->foreign('current_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
