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
        // Tampilkan Aset Sewa yang sedang digunakan oleh user ini
        // Data diambil dari Stock (bukan OutgoingTransaction)
        $recentTransactions = Stock::with('user.division')
            ->where('kategori', 'aset_sewa')
            ->where('status_kondisi', 'digunakan')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal_mulai_pakai', 'desc')
            ->limit(5)
            ->get()
            ->map(function($stock) {
                // Hitung durasi sewa dalam tahun
                $durasiSewa = null;
                if ($stock->tanggal_mulai_pakai && $stock->tanggal_akhir_pakai) {
                    $start = \Carbon\Carbon::parse($stock->tanggal_mulai_pakai);
                    $end = \Carbon\Carbon::parse($stock->tanggal_akhir_pakai);
                    $totalDays = $start->diffInDays($end);
                    $durasiSewa = round($totalDays / 365, 1); // Konversi ke tahun
                }
                
                // Format ke object yang mirip OutgoingTransaction untuk view compatibility
                return (object) [
                    'idkeluar' => 'AS-' . $stock->idbarang,
                    'tanggal' => $stock->tanggal_mulai_pakai ? \Carbon\Carbon::parse($stock->tanggal_mulai_pakai) : now(),
                    'namabarang_k' => $stock->namabarang,
                    'kodebarang_k' => $stock->kodebarang,
                    'qty' => 1,
                    'penerima' => $stock->user->name ?? '-',
                    'kategori' => 'aset_sewa',
                    'status' => 'sedang_dipakai',
                    'tanggal_mulai_pakai' => $stock->tanggal_mulai_pakai,
                    'tanggal_akhir_pakai' => $stock->tanggal_akhir_pakai,
                    'tanggal_akhir_sewa' => $stock->tanggal_akhir_pakai ? \Carbon\Carbon::parse($stock->tanggal_akhir_pakai) : null, // Alias untuk view
                    'durasi_sewa' => $durasiSewa,
                    'stock' => $stock,
                ];
            });
        
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
        // Ambil Aset Sewa yang sedang digunakan oleh user ini (dari Stock)
        $activePemakaian = Stock::with('user.division')
            ->where('kategori', 'aset_sewa')
            ->where('status_kondisi', 'digunakan')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal_mulai_pakai', 'desc')
            ->get()
            ->map(function ($stock) {
                $data = [
                    'id' => 'AS-' . $stock->idbarang,
                    'tanggal' => $stock->tanggal_mulai_pakai ? \Carbon\Carbon::parse($stock->tanggal_mulai_pakai)->format('d/m/Y') : now()->format('d/m/Y'),
                    'tanggal_raw' => $stock->tanggal_mulai_pakai ? \Carbon\Carbon::parse($stock->tanggal_mulai_pakai)->toIso8601String() : now()->toIso8601String(),
                    'namabarang' => $stock->namabarang,
                    'kodebarang' => $stock->kodebarang,
                    'qty' => 1,
                    'penerima' => $stock->user->name ?? '-',
                    'kategori' => 'aset_sewa',
                    'is_aset_sewa' => true,
                    'status_kondisi' => $stock->status_kondisi,
                ];

                // Info tanggal pakai untuk aset sewa
                $data['tanggal_mulai_pakai'] = $stock->tanggal_mulai_pakai ? \Carbon\Carbon::parse($stock->tanggal_mulai_pakai)->format('d/m/Y') : null;
                $data['tanggal_akhir_pakai'] = $stock->tanggal_akhir_pakai ? \Carbon\Carbon::parse($stock->tanggal_akhir_pakai)->format('d/m/Y') : null;
                
                // Hitung sisa hari pakai
                if ($stock->tanggal_akhir_pakai) {
                    $today = now()->startOfDay();
                    $endDate = \Carbon\Carbon::parse($stock->tanggal_akhir_pakai)->startOfDay();
                    $sisaHari = $today->diffInDays($endDate, false);
                    $data['sisa_hari'] = $sisaHari;
                    $data['sisa_hari_text'] = $sisaHari > 0 ? $sisaHari . ' hari lagi' : 'Sudah berakhir';
                    $data['is_expired'] = $sisaHari < 0;
                    $data['is_near_expiry'] = $sisaHari >= 0 && $sisaHari <= 7;
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