<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of outgoing transactions (read-only for user).
     * Menampilkan SEMUA transaksi barang keluar (tidak di-filter per user)
     * Konsisten dengan Admin logic
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with(['stock', 'user.division']);
        // TIDAK ada filter user_id - tampilkan semua transaksi untuk semua user
        
        // Filter by kategori
        if ($request->filled('kategori') && in_array($request->kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
            
            // For Aset Sewa: only show 'selesai' status (exclude sedang_dipakai & ditarik)
            // Konsisten dengan Admin - hanya tampilkan yang sudah diselesaikan
            if ($request->kategori === 'aset_sewa' && !$request->filled('status_filter')) {
                $query->where('status', 'selesai');
            }
        }
        
        // Filter by sub_kategori (khusus Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('kategori', 'material_umum')
                  ->whereHas('stock', function($q) use ($request) {
                      $q->where('sub_kategori', $request->sub_kategori);
                  });
        }
        
        // Filter by status (untuk aset_sewa)
        if ($request->filled('status_filter') && $request->kategori === 'aset_sewa') {
            $query->where('status', $request->status_filter);
        }
        
        // Search by kode, nama, or penerima
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang_k', 'ILIKE', "%{$search}%")
                  ->orWhere('namabarang_k', 'ILIKE', "%{$search}%")
                  ->orWhere('penerima', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        $barangKeluar = $query->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('user.barang-keluar.index', compact('barangKeluar'));
    }

    /**
     * Display the specified outgoing transaction detail.
     * Security: hanya bisa akses jika barang di-assign ke user yang login
     */
    public function show($id)
    {
        $barangKeluar = OutgoingTransaction::with(['stock', 'user.division'])
            ->where('user_id', Auth::id()) // Security check
            ->findOrFail($id);
            
        return view('user.barang-keluar.show', compact('barangKeluar'));
    }
}
