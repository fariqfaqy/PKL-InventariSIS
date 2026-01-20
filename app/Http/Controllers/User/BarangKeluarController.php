<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of all outgoing transactions (read-only for user).
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with('stock');

        // Filter berdasarkan tipe (peminjaman=sewa, permintaan=habis_pakai)
        // Hanya filter jika ada tipe yang dipilih (bukan "semua")
        if ($request->filled('tipe') && in_array($request->tipe, ['peminjaman', 'permintaan'])) {
            if ($request->tipe == 'peminjaman') {
                // Peminjaman = Barang Sewa
                $query->where('kategori', 'barang_sewa');
            } else {
                // Permintaan = Barang Habis Pakai
                $query->where('kategori', 'habis_pakai');
            }
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang_k', 'ILIKE', "%{$search}%")
                  ->orWhere('namabarang_k', 'ILIKE', "%{$search}%")
                  ->orWhere('penerima', 'ILIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $barangKeluar = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $tipe = $request->tipe;

        return view('user.barang-keluar.index', compact('barangKeluar', 'tipe'));
    }

    /**
     * Display the specified outgoing transaction detail.
     */
    public function show($id)
    {
        $barangKeluar = OutgoingTransaction::with('stock')->findOrFail($id);
        return view('user.barang-keluar.show', compact('barangKeluar'));
    }
}
