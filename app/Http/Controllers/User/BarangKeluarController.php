<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of all outgoing transactions (read-only for user).
     * Hanya tampilkan barang yang masih keluar (status: sedang_dipakai)
     * Barang pinjam yang sudah selesai (stock sudah balik) TIDAK ditampilkan
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with('stock')
            ->where('status', 'sedang_dipakai'); // Hanya barang yang masih keluar

        // Filter berdasarkan kategori
        if ($request->filled('kategori') && in_array($request->kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('sub_kategori', $request->sub_kategori);
            });
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

        return view('user.barang-keluar.index', compact('barangKeluar'));
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
