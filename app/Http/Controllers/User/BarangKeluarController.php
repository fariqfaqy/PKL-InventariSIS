<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of all outgoing transactions (read-only for user).
     * Tampilkan SEMUA barang keluar yang pernah di-assign ke user (semua status)
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with(['stock', 'user.division'])
            ->where(function($q) {
                // Filter: barang yang di-assign ke user (Aset Sewa) ATAU barang yang diterima user (Material Umum)
                $q->where('user_id', auth()->id()) // Aset Sewa
                  ->orWhere('penerima', auth()->user()->name); // Material Umum (barang pinjam/habis pakai)
            });

        // Filter berdasarkan sub_kategori (prioritas lebih tinggi)
        if ($request->filled('sub_kategori')) {
            if ($request->sub_kategori === 'aset_sewa') {
                // Sub-kategori Aset Sewa (kategori barang_sewa)
                $query->where('kategori', 'barang_sewa');
            } elseif (in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
                // Sub-kategori Material Umum (barang habis pakai / barang pinjam)
                $query->where('kategori', 'habis_pakai')
                      ->whereHas('stock', function($q) use ($request) {
                          $q->where('sub_kategori', $request->sub_kategori);
                      });
            }
        } 
        // Filter berdasarkan kategori (hanya apply jika tidak ada sub_kategori)
        elseif ($request->filled('kategori') && in_array($request->kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
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
     * Hanya bisa akses kalau barang tersebut milik user yang login
     */
    public function show($id)
    {
        $barangKeluar = OutgoingTransaction::with(['stock', 'user.division'])
            ->where(function($q) {
                // Security: hanya bisa lihat barang yang di-assign ke user atau diterima user
                $q->where('user_id', auth()->id()) // Aset Sewa
                  ->orWhere('penerima', auth()->user()->name); // Material Umum
            })
            ->findOrFail($id);
        return view('user.barang-keluar.show', compact('barangKeluar'));
    }
}
