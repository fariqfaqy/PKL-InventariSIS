<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetSubKategori extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:subkategori';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default sub_kategori untuk Material Umum yang masih NULL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memperbaiki sub_kategori untuk Material Umum...');
        $this->newLine();

        $stocks = \DB::table('stock')
            ->where('kategori', 'habis_pakai')
            ->whereNull('sub_kategori')
            ->get();

        if ($stocks->count() === 0) {
            $this->info('✅ Semua barang Material Umum sudah memiliki sub_kategori!');
            return 0;
        }

        $this->info('Ditemukan ' . $stocks->count() . ' barang yang perlu di-set sub_kategori:');
        $this->newLine();

        foreach ($stocks as $stock) {
            $this->line("  - {$stock->namabarang} ({$stock->kodebarang})");
        }

        $this->newLine();
        $this->warn('Default: Set semua sebagai "barang_habis_pakai"');
        $this->info('Jika ada yang seharusnya "barang_pinjam", admin bisa edit manual nanti.');
        $this->newLine();

        if (!$this->confirm('Lanjutkan?', true)) {
            $this->error('Dibatalkan.');
            return 1;
        }

        // Update semua barang Material Umum tanpa sub_kategori menjadi barang_habis_pakai
        $updated = \DB::table('stock')
            ->where('kategori', 'habis_pakai')
            ->whereNull('sub_kategori')
            ->update(['sub_kategori' => 'barang_habis_pakai']);

        $this->newLine();
        $this->info("✅ Berhasil update {$updated} barang menjadi 'barang_habis_pakai'");
        $this->info('Pegawai sekarang bisa request barang-barang ini!');
        $this->newLine();
        $this->warn('💡 Jika ada barang yang seharusnya "barang_pinjam":');
        $this->warn('   Admin → Master Stok → Edit barang → Ubah sub-kategori');

        return 0;
    }
}
