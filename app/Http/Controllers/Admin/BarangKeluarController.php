<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use App\Models\Stock;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of outgoing items.
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with('stock');
        
        // Filter by kategori if provided
        if ($request->filled('kategori') && in_array($request->kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
            
            // For Aset Sewa: only show 'selesai' status (exclude sedang_dipakai & ditarik)
            if ($request->kategori === 'aset_sewa' && !$request->filled('status_filter')) {
                $query->where('status', 'selesai');
            }
        }

        // Filter by sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('sub_kategori', $request->sub_kategori);
            });
        }
        
        // Filter by status (khusus untuk Aset Sewa)
        if ($request->filled('status_filter') && $request->kategori === 'aset_sewa') {
            $query->where('status', $request->status_filter);
        }
        
        // Search by kode, nama barang, or penerima (case-insensitive)
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
        
        // Get selected kategori for view conditional rendering
        $selectedKategori = $request->get('kategori', null);
        
        return view('admin.barang-keluar.index', compact('barangKeluar', 'selectedKategori'));
    }

    /**
     * Show the form for creating a new outgoing item.
     */
    public function create()
    {
        // Material Umum: Items with stock > 0
        $stocksMaterialUmum = Stock::where('kategori', 'material_umum')
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
        
        // Aset Sewa: Items yang SEDANG DIGUNAKAN (dari Stock, bukan OutgoingTransaction)
        // Ambil Stock yang kategori = 'aset_sewa' dan status_kondisi = 'digunakan'
        $asetSewaAktif = Stock::with(['user.division'])
            ->where('kategori', 'aset_sewa')
            ->where('status_kondisi', 'digunakan')
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
            
        // Get all users for dropdown (untuk Material Umum)
        $users = \App\Models\User::with('division')
            ->orderBy('name')
            ->get();
            
        return view('admin.barang-keluar.create', compact('stocksMaterialUmum', 'asetSewaAktif', 'users'));
    }

    /**
     * Store a newly created outgoing item in storage.
     */
    public function store(Request $request)
    {
        $kategori = $request->input('kategori');
        
        // ASET SEWA: Selesaikan pemakaian - buat OutgoingTransaction dan update Stock
        if ($kategori === 'aset_sewa') {
            $validated = $request->validate([
                'kategori' => 'required|in:aset_sewa',
                'idbarang' => 'required|exists:stock,idbarang',
            ]);
            
            // Find the Stock
            $stock = Stock::with('user')->findOrFail($validated['idbarang']);
            
            // Validate status - harus sedang digunakan
            if ($stock->status_kondisi !== 'digunakan') {
                return back()->withErrors(['idbarang' => 'Aset ini tidak sedang digunakan atau sudah selesai!'])
                    ->withInput();
            }
            
            // Get user info from stock
            $user = $stock->user;
            $penerima = $user ? $user->name : 'Unknown';
            
            // Create OutgoingTransaction dengan status langsung 'selesai'
            OutgoingTransaction::create([
                'idbarang' => $stock->idbarang,
                'tanggal' => now(),
                'penerima' => $penerima,
                'user_id' => $stock->user_id,
                'kategori' => 'aset_sewa',
                'qty' => 1,
                'keterangan' => 'Aset sewa selesai digunakan',
                'namabarang_k' => $stock->namabarang,
                'kodebarang_k' => $stock->kodebarang,
                'status' => 'selesai',
                'penginput' => Auth::user()->name,
                'tanggal_mulai_pakai' => $stock->tanggal_mulai_pakai,
                'tanggal_akhir_pakai' => $stock->tanggal_akhir_pakai,
                'tanggal_selesai' => now(),
            ]);
            
            // Update stock status kondisi menjadi 'selesai' - barang tidak dapat digunakan kembali
            $stock->update([
                'status_kondisi' => 'selesai',
                'keterangan_kondisi' => 'Pemakaian selesai - Aset telah dikembalikan ke distributor/vendor',
                'tanggal_update_kondisi' => now(),
                'stock' => 0,
            ]);
            
            return redirect()->route('admin.barang-keluar.index', ['kategori' => 'aset_sewa'])
                ->with('success', 'Aset Sewa berhasil diselesaikan! Aset telah dikembalikan.');
        }
        
        // MATERIAL UMUM: Normal outgoing transaction
        $validated = $request->validate([
            'kategori' => 'required|in:material_umum',
            'sub_kategori' => 'required|in:barang_habis_pakai,barang_pinjam',
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);
        
        // Validate kategori match
        if ($stock->kategori !== 'material_umum') {
            return back()->withErrors(['idbarang' => 'Barang ini bukan Material Umum!'])
                ->withInput();
        }

        // Check if stock is sufficient
        if ($stock->stock < $validated['qty']) {
            return back()->withErrors(['qty' => 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock])
                ->withInput();
        }

        // Create outgoing transaction
        OutgoingTransaction::create([
            'idbarang' => $validated['idbarang'],
            'tanggal' => $validated['tanggal'],
            'penerima' => $validated['penerima'],
            'qty' => $validated['qty'],
            'namabarang_k' => $stock->namabarang,
            'kodebarang_k' => $stock->kodebarang,
            'penginput' => Auth::user()->name,
            'user_id' => Auth::id(),
            'kategori' => $stock->kategori,
            'tipe_request' => $validated['sub_kategori'] === 'barang_pinjam' ? 'pinjam_material' : 'pakai_habis_pakai',
            'status' => 'selesai',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Update stock quantity
        $stock->decrement('stock', $validated['qty']);

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil ditambahkan!');
    }

    /**
     * Display the specified outgoing item.
     */
    public function show($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::with(['stock', 'user.division'])->findOrFail($idkeluar);
        return view('admin.barang-keluar.show', compact('barangKeluar'));
    }

    /**
     * Show the form for editing the specified outgoing item.
     */
    public function edit($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        $stocks = Stock::orderBy('namabarang')->get();
        return view('admin.barang-keluar.edit', compact('barangKeluar', 'stocks'));
    }

    /**
     * Update the specified outgoing item in storage.
     */
    public function update(Request $request, $idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        $validated = $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);

        // Calculate stock adjustment
        $qtyDifference = $validated['qty'] - $barangKeluar->qty;
        
        // Check if stock adjustment is valid
        if ($qtyDifference > 0 && $stock->stock < $qtyDifference) {
            return back()->withErrors(['qty' => 'Stok tidak mencukupi untuk penambahan jumlah!'])
                ->withInput();
        }

        // Update stock quantity if there's a difference
        if ($qtyDifference != 0) {
            $stock->decrement('stock', $qtyDifference);
        }

        // Update outgoing transaction
        $barangKeluar->update([
            'idbarang' => $validated['idbarang'],
            'tanggal' => $validated['tanggal'],
            'penerima' => $validated['penerima'],
            'qty' => $validated['qty'],
            'namabarang_k' => $stock->namabarang,
            'kodebarang_k' => $stock->kodebarang,
        ]);

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil diperbarui!');
    }

    /**
     * Remove the specified outgoing item from storage.
     * Hanya menghapus history, tidak mempengaruhi stok.
     */
    public function destroy($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        // Delete transaction only (tidak mempengaruhi stok)
        $barangKeluar->delete();

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'History barang keluar berhasil dihapus!');
    }

    /**
     * Extend the rental period for Aset Sewa.
     */
    public function extendRental(Request $request, $idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        // Validate only for Aset Sewa
        if ($barangKeluar->kategori !== 'aset_sewa') {
            return redirect()->back()->with('error', 'Hanya Aset Sewa yang bisa diperpanjang!');
        }
        
        // Validate new end date
        $validated = $request->validate([
            'tanggal_akhir_pakai' => [
                'required',
                'date',
                'after:' . $barangKeluar->tanggal_akhir_pakai,
            ],
        ], [
            'tanggal_akhir_pakai.required' => 'Tanggal akhir baru harus diisi',
            'tanggal_akhir_pakai.date' => 'Format tanggal tidak valid',
            'tanggal_akhir_pakai.after' => 'Tanggal akhir baru harus setelah tanggal akhir sekarang (' . \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') . ')',
        ]);
        
        // Update end date
        $barangKeluar->update([
            'tanggal_akhir_pakai' => $validated['tanggal_akhir_pakai'],
        ]);
        
        return redirect()->back()->with('success', 'Masa sewa berhasil diperpanjang!');
    }
    
    /**
     * Mark rental as completed and create IncomingTransaction.
     */
    public function completeRental($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        // Hanya untuk Aset Sewa (barang_pinjam tidak ada di OutgoingTransaction lagi)
        if ($barangKeluar->kategori !== 'aset_sewa') {
            return redirect()->back()->with('error', 'Hanya Aset Sewa yang bisa diselesaikan dari barang keluar!');
        }
        
        if ($barangKeluar->status !== 'sedang_dipakai') {
            return redirect()->back()->with('error', 'Pemakaian sudah selesai atau ditarik!');
        }
        
        // Update OutgoingTransaction status
        $barangKeluar->update([
            'status' => 'selesai',
            'tanggal_selesai' => now(),
        ]);
        
        // Create IncomingTransaction (barang masuk kembali)
        \App\Models\IncomingTransaction::create([
            'idbarang' => $barangKeluar->idbarang,
            'tanggal' => now(),
            'keterangan' => 'Return dari: ' . $barangKeluar->penerima . ' (Pemakaian Selesai)',
            'qty' => $barangKeluar->qty,
            'namabarang_m' => $barangKeluar->namabarang_k,
            'kodebarang_m' => $barangKeluar->kodebarang_k,
            'penginput' => Auth::user()->name,
        ]);
        
        // Aset sewa: dikembalikan ke distributor/vendor, tidak bisa digunakan lagi
        if ($barangKeluar->stock) {
            $barangKeluar->stock->update([
                'status_kondisi' => 'selesai',
                'keterangan_kondisi' => 'Pemakaian selesai - Aset telah dikembalikan ke distributor/vendor',
                'tanggal_update_kondisi' => now(),
                'stock' => 0, // Set stock ke 0 karena sudah dikembalikan
            ]);
        }
        
        return redirect()->back()->with('success', 'Pemakaian berhasil diselesaikan! Aset telah dikembalikan dan tidak dapat digunakan kembali.');
    }

    /**
     * Export outgoing transactions to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = OutgoingTransaction::with('stock');

        // Filter by kategori if provided
        $kategori = $request->get('kategori');
        if ($kategori && in_array($kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $kategori);
        }

        // Filter by sub_kategori if provided
        $subKategori = $request->get('sub_kategori');
        if ($subKategori && in_array($subKategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->whereHas('stock', function($q) use ($subKategori) {
                $q->where('sub_kategori', $subKategori);
            });
        }

        $transactions = $query->orderBy('tanggal', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.pdf.barang-keluar', compact('transactions', 'kategori', 'subKategori'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Barang_Keluar_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
