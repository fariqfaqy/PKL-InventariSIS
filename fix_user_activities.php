<?php

/**
 * Script untuk update user_id di tabel keluar (OutgoingTransaction)
 * berdasarkan id_request yang ada
 * 
 * Jalankan dengan: php fix_user_activities.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memperbaiki user_id di tabel keluar...\n\n";

// Update user_id berdasarkan request_barang yang terhubung
$updated = DB::table('keluar')
    ->whereNotNull('id_request')
    ->whereNull('user_id')
    ->get()
    ->each(function($transaction) {
        $request = DB::table('request_barang')
            ->where('id_request', $transaction->id_request)
            ->first();
        
        if ($request && $request->user_id) {
            DB::table('keluar')
                ->where('idkeluar', $transaction->idkeluar)
                ->update(['user_id' => $request->user_id]);
            
            echo "✓ Updated idkeluar {$transaction->idkeluar} -> user_id {$request->user_id}\n";
        }
    });

// Tampilkan statistik
$totalWithUserId = DB::table('keluar')->whereNotNull('user_id')->count();
$totalWithoutUserId = DB::table('keluar')->whereNull('user_id')->count();

echo "\n--- Statistik ---\n";
echo "Total transaksi dengan user_id: {$totalWithUserId}\n";
echo "Total transaksi tanpa user_id: {$totalWithoutUserId}\n";
echo "\nSelesai!\n";
