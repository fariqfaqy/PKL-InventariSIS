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
            ->where('user_id', Auth::id()) // Filter by user_id (konsisten dengan admin)
            ->where('status', 'sedang_dipakai') // belum selesai
            ->where('kategori', 'aset_sewa') // hanya aset sewa
            ->orderBy('tanggal', 'desc')
            ->limit(5) // Limit untuk dashboard
            ->get();
        
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
            ->where('user_id', Auth::id()) // Filter by user_id (konsisten dengan admin)
            ->where('status', 'sedang_dipakai')
            ->where('kategori', 'aset_sewa') // hanya aset sewa
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($trans) {
                $data = [
                    'id' => $trans->idkeluar,
                    'tanggal' => $trans->tanggal->format('d/m/Y'),
                    'tanggal_raw' => $trans->tanggal->toIso8601String(),
                    'namabarang' => $trans->namabarang_k,
                    'qty' => $trans->qty,
                    'penerima' => $trans->penerima,
                    'kategori' => $trans->kategori,
                    'is_aset_sewa' => $trans->kategori === 'aset_sewa',
                ];

                // Tambahkan info pakai untuk aset sewa (gunakan tanggal_mulai/akhir_pakai)
                if ($trans->kategori === 'aset_sewa') {
                    $data['tanggal_mulai_pakai'] = $trans->tanggal_mulai_pakai ? $trans->tanggal_mulai_pakai->format('d/m/Y') : null;
                    $data['tanggal_akhir_pakai'] = $trans->tanggal_akhir_pakai ? $trans->tanggal_akhir_pakai->format('d/m/Y') : null;
                    
                    // Hitung sisa hari pakai
                    if ($trans->tanggal_akhir_pakai) {
                        $today = now()->startOfDay();
                        $endDate = $trans->tanggal_akhir_pakai;
                        $sisaHari = $today->diffInDays($endDate, false);
                        $data['sisa_hari'] = $sisaHari;
                        $data['sisa_hari_text'] = $sisaHari > 0 ? $sisaHari . ' hari lagi' : 'Sudah berakhir';
                        $data['is_expired'] = $sisaHari < 0;
                        $data['is_near_expiry'] = $sisaHari >= 0 && $sisaHari <= 7;
                    }
                    
                    // Status kondisi dari stock
                    if ($trans->stock) {
                        $data['status_kondisi'] = $trans->stock->status_kondisi;
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