<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of incoming transactions (read-only for user).
     */
    public function index(Request $request)
    {
        $query = IncomingTransaction::with('stock');

        // Filter berdasarkan kategori barang (barang_sewa/habis_pakai/aset_tetap)
        // Hanya filter jika ada kategori yang dipilih (bukan "semua")
        if ($request->filled('kategori') && in_array($request->kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('kategori', $request->kategori);
            });
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang_m', 'ILIKE', "%{$search}%")
                  ->orWhere('namabarang_m', 'ILIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $barangMasuk = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $kategori = $request->kategori;

        return view('user.barang-masuk.index', compact('barangMasuk', 'kategori'));
    }

    /**
     * Display the specified incoming transaction detail.
     */
    public function show($id)
    {
        $barangMasuk = IncomingTransaction::with('stock')->findOrFail($id);
        return view('user.barang-masuk.show', compact('barangMasuk'));
    }
}
