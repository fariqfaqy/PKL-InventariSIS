<?php

namespace App\Console\Commands;

use App\Models\RequestBarang;
use App\Models\OutgoingTransaction;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCompleteExpiredRentals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menyelesaikan peminjaman yang sudah melewati tanggal pengembalian';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired rental requests...');
        
        // Cari semua request peminjaman (pinjam_sewa & pinjam_material) yang masih approved
        // dan sudah melewati tanggal pengembalian
        $expiredRentals = RequestBarang::with('stock', 'user')
            ->where('status', 'approved')
            ->whereIn('tipe_request', ['pinjam_sewa', 'pinjam_material'])
            ->whereNotNull('tanggal_akhir_sewa')
            ->whereDate('tanggal_akhir_sewa', '<', now())
            ->get();
        
        if ($expiredRentals->isEmpty()) {
            $this->info('No expired rentals found.');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$expiredRentals->count()} expired rental(s). Processing...");
        
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($expiredRentals as $rental) {
            DB::beginTransaction();
            try {
                // Auto-reject semua pending change requests untuk request ini
                RequestBarang::where('parent_request_id', $rental->id_request)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'catatan_admin' => 'Request ditolak otomatis karena request asli sudah expired dan diselesaikan sistem.',
                        'diproses_oleh' => 'System',
                        'tanggal_diproses' => now(),
                    ]);
                
                // Update OutgoingTransaction status ke 'selesai'
                OutgoingTransaction::where('id_request', $rental->id_request)
                    ->update([
                        'status' => 'selesai',
                        'tanggal_selesai' => now(),
                    ]);
                
                // Update status kondisi HANYA untuk Aset Sewa (barang_sewa)
                if ($rental->stock->kategori === 'barang_sewa') {
                    Stock::where('idbarang', $rental->idbarang)->update([
                        'status_kondisi' => 'digunakan',
                        'keterangan_kondisi' => 'Selesai digunakan (expired - auto completed)',
                        'tanggal_update_kondisi' => now(),
                    ]);
                }
                
                // Kembalikan stok HANYA untuk Barang Pinjam (pinjam_material)
                // Aset Sewa (pinjam_sewa) TIDAK dikembalikan
                if ($rental->tipe_request === 'pinjam_material') {
                    Stock::where('idbarang', $rental->idbarang)
                        ->increment('stock', $rental->qty);
                }
                
                // Update request status
                $rental->update([
                    'status' => 'completed',
                    'catatan_admin' => ($rental->catatan_admin ? $rental->catatan_admin . "\n\n" : '') 
                        . 'Peminjaman diselesaikan otomatis oleh sistem karena sudah melewati tanggal pengembalian.',
                ]);
                
                DB::commit();
                
                $this->line("✓ Completed: Request #{$rental->id_request} - {$rental->stock->nama_barang} (User: {$rental->user->name})");
                $successCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Failed: Request #{$rental->id_request} - {$e->getMessage()}");
                $failedCount++;
            }
        }
        
        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Successfully completed: {$successCount}");
        if ($failedCount > 0) {
            $this->warn("Failed: {$failedCount}");
        }
        
        return Command::SUCCESS;
    }
}
