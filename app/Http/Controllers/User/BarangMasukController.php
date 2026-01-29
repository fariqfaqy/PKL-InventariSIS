<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of incoming transactions (read-only for user).
     * Konsisten dengan AdminBarangMasukController
     */
    public function index(Request $request)
    {
        // Join dengan stock untuk filter kategori & sub_kategori (konsisten dengan admin)
        $query = IncomingTransaction::with('stock')
            ->join('stock', 'masuk.idbarang', '=', 'stock.idbarang')
            ->select('masuk.*', 'stock.kategori', 'stock.sub_kategori');
        
        // Filter by kategori if provided
        if ($request->filled('kategori') && in_array($request->kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('stock.kategori', $request->kategori);
        }

        // Filter by sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('stock.sub_kategori', $request->sub_kategori);
        }
        
        // Search by kode or nama barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('masuk.kodebarang_m', 'ILIKE', "%{$search}%")
                  ->orWhere('masuk.namabarang_m', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('masuk.tanggal', $request->tanggal);
        }
        
        $barangMasuk = $query->orderBy('masuk.tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('user.barang-masuk.index', compact('barangMasuk'));
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
