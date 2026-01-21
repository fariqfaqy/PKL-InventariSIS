<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSubKategori extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subkategori';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check sub_kategori data in stock table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking sub_kategori in stock table...');
        $this->newLine();

        $stocks = \DB::table('stock')
            ->where('kategori', 'habis_pakai')
            ->select('idbarang', 'namabarang', 'kategori', 'sub_kategori')
            ->get();

        $this->info('Total Material Umum (habis_pakai): ' . $stocks->count());
        $this->newLine();

        $withSub = $stocks->filter(fn($s) => !is_null($s->sub_kategori))->count();
        $withoutSub = $stocks->filter(fn($s) => is_null($s->sub_kategori))->count();

        $this->info("Dengan sub_kategori: $withSub");
        $this->info("Tanpa sub_kategori (NULL): $withoutSub");
        $this->newLine();

        if ($withSub > 0) {
            $this->info('Sample data dengan sub_kategori:');
            $stocks->filter(fn($s) => !is_null($s->sub_kategori))->take(5)->each(function($s) {
                $this->line("  - {$s->namabarang} => sub_kategori: {$s->sub_kategori}");
            });
        }

        if ($withoutSub > 0) {
            $this->newLine();
            $this->warn('Ada ' . $withoutSub . ' barang Material Umum yang belum di-set sub_kategori!');
            $this->info('Admin perlu mengatur sub_kategori (barang_habis_pakai atau barang_pinjam) di halaman master stok.');
        }

        return 0;
    }
}
