<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\RackAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        // Total barang
        $totalBarang = Stock::count();
        $totalStok = Stock::sum('stock');

        // Total barang masuk
        $totalBarangMasuk = IncomingTransaction::sum('qty');
        $barangMasukHariIni = IncomingTransaction::whereDate('tanggal', today())->sum('qty');

        // Total barang keluar
        $totalBarangKeluar = OutgoingTransaction::sum('qty');
        $barangKeluarHariIni = OutgoingTransaction::whereDate('tanggal', today())->sum('qty');

        // Ringkasan stok berdasarkan status
        $stokAman = Stock::where('stock', '>=', 10)->count();
        $stokMenengah = Stock::whereBetween('stock', [5, 9])->count();
        $stokKritis = Stock::where('stock', '<=', 4)->count();

        // Ringkasan jumlah barang per rak (dari kolom rack di stock)
        $barangPerRak = Stock::whereNotNull('rack')
            ->select('rack', 
                DB::raw('count(*) as total'), 
                DB::raw('sum(stock) as total_stock')
            )
            ->groupBy('rack')
            ->get()
            ->keyBy('rack');

        // Rak list - tampilkan semua rak
        $raks = collect(['1a', '1b', '1c', '2a', '2b', '2c']);

        // Recent transactions barang keluar (untuk user)
        // Tampilkan hanya aset sewa yang sedang dipakai milik user ini
        $recentTransactions = OutgoingTransaction::with('stock')
            ->where('penginput', auth()->user()->name) // penginput berisi nama user, bukan email
            ->whereNotNull('diproses_oleh') // sudah di-approve admin
            ->where('status', 'sedang_dipakai') // belum selesai
            ->where('kategori', 'barang_sewa') // hanya aset sewa
            ->orderBy('tanggal', 'desc')
            ->get(); // tampilkan semua tanpa limit
        
        return view('user.dashboard', compact(
            'totalBarang',
            'totalStok',
            'totalBarangMasuk',
            'barangMasukHariIni',
            'totalBarangKeluar',
            'barangKeluarHariIni',
            'stokAman',
            'stokMenengah',
            'stokKritis',
            'barangPerRak',
            'raks',
            'recentTransactions'
        ));
    }

    /**
     * Get active pemakaian data for real-time updates (API endpoint)
     */
    public function getActivePemakaian()
    {
        // Ambil pemakaian yang aktif (hanya aset sewa yang sedang dipakai milik user ini)
        $activePemakaian = OutgoingTransaction::with('stock')
            ->where('penginput', auth()->user()->name) // penginput berisi nama user, bukan email
            ->whereNotNull('diproses_oleh')
            ->where('status', 'sedang_dipakai')
            ->where('kategori', 'barang_sewa') // hanya aset sewa
            ->orderBy('tanggal', 'desc')
            ->get() // tampilkan semua tanpa limit
            ->map(function ($trans) {
                $data = [
                    'id' => $trans->idkeluar,
                    'tanggal' => $trans->tanggal->format('d/m/Y'),
                    'tanggal_raw' => $trans->tanggal->toIso8601String(),
                    'namabarang' => $trans->namabarang_k,
                    'qty' => $trans->qty,
                    'penerima' => $trans->penerima,
                    'kategori' => $trans->kategori,
                    'is_barang_sewa' => $trans->kategori === 'barang_sewa',
                ];

                // Tambahkan info sewa jika aset sewa
                if ($trans->kategori === 'barang_sewa') {
                    $data['durasi_sewa'] = $trans->durasi_sewa;
                    $data['tanggal_mulai_sewa'] = $trans->tanggal_mulai_sewa ? $trans->tanggal_mulai_sewa->format('d/m/Y') : null;
                    $data['tanggal_akhir_sewa'] = $trans->tanggal_akhir_sewa ? $trans->tanggal_akhir_sewa->format('d/m/Y') : null;
                    
                    // Hitung sisa hari sewa
                    if ($trans->tanggal_akhir_sewa) {
                        $today = now()->startOfDay();
                        $endDate = $trans->tanggal_akhir_sewa;
                        $sisaHari = $today->diffInDays($endDate, false);
                        $data['sisa_hari'] = $sisaHari;
                        $data['sisa_hari_text'] = $sisaHari > 0 ? $sisaHari . ' hari lagi' : 'Sudah berakhir';
                        $data['is_expired'] = $sisaHari < 0;
                        $data['is_near_expiry'] = $sisaHari >= 0 && $sisaHari <= 7;
                    }
                }

                return $data;
            });

        return response()->json([
            'success' => true,
            'data' => $activePemakaian,
            'count' => $activePemakaian->count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}