<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\RackAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StokBarangController extends Controller
{
    /**
     * Display a listing of stock (read-only for user).
     */
    public function index(Request $request)
    {
        $query = Stock::query();

        // Filter berdasarkan kategori
        if ($request->filled('kategori') && in_array($request->kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
            
            // For Aset Sewa: Load user relationship (pengguna disimpan di Stock)
            if ($request->kategori === 'aset_sewa') {
                $query->with(['user.division']);
            }
        }

        // Filter berdasarkan sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('sub_kategori', $request->sub_kategori);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang', 'ILIKE', "%{$search}%")
                  ->orWhere('namabarang', 'ILIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan rak (dari kolom rack di stock)
        if ($request->filled('rack')) {
            $query->where('rack', $request->rack);
        }

        // Filter berdasarkan status stok
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'kritis':
                    $query->where('stock', '<=', 4);
                    break;
                case 'menengah':
                    $query->whereBetween('stock', [5, 9]);
                    break;
                case 'aman':
                    $query->where('stock', '>=', 10);
                    break;
            }
        }
        
        // Filter by status kondisi (untuk aset sewa)
        if ($request->filled('status_kondisi') && in_array($request->status_kondisi, ['tersedia', 'digunakan', 'diperbaiki', 'rusak'])) {
            $query->where('status_kondisi', $request->status_kondisi);
        }

        $stocks = $query->orderBy('namabarang', 'asc') // Konsisten dengan admin
            ->paginate(10) // Konsisten dengan admin
            ->withQueryString();
        
        // Get racks yang tersedia (dari kolom rack di stock)
        $racks = Stock::whereNotNull('rack')
            ->distinct()
            ->pluck('rack')
            ->sort();

        $kategori = $request->get('kategori');

        return view('user.stok-barang.index', compact('stocks', 'racks', 'kategori'));
    }

    /**
     * Display the specified stock detail.
     */
    public function show($id)
    {
        $stock = Stock::with([
            'incomingTransactions', 
            'outgoingTransactions' => function($query) {
                $query->with('user.division')
                      ->orderBy('created_at', 'desc');
            },
            'user.division' // Load user relationship untuk Aset Sewa
        ])->findOrFail($id);
        
        return view('user.stok-barang.show', compact('stock'));
    }
}
