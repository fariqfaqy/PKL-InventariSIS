<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Ringkasan jumlah barang per rak
        $barangPerRak = Stock::select('rack', DB::raw('count(*) as total'), DB::raw('sum(stock) as total_stock'))
            ->groupBy('rack')
            ->get()
            ->keyBy('rack');

        // Rak list
        $raks = ['1a', '1b', '1c', '2a', '2b', '2c', '3a', '3b', '3c'];

        // Recent transactions barang keluar (untuk user)
        $recentTransactions = OutgoingTransaction::with('stock')
            ->where('penginput', auth()->user()->email)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
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
}

