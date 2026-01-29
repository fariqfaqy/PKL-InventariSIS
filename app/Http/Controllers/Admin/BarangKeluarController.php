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
        
        // Search by kode, nama barang, or penerima
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang_k', 'like', '%' . $search . '%')
                  ->orWhere('namabarang_k', 'like', '%' . $search . '%')
                  ->orWhere('penerima', 'like', '%' . $search . '%');
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
        // Only show stocks that are available:
        // - Material Umum: All items with stock > 0
        // - Aset Sewa: Only items with status_kondisi = 'tersedia' (not currently used)
        $stocks = Stock::select('stock.*')
            ->where(function ($query) {
                $query->where('stock.kategori', 'material_umum')
                      ->where('stock.stock', '>', 0);
            })
            ->orWhere(function ($query) {
                $query->where('stock.kategori', 'aset_sewa')
                      ->where('stock.status_kondisi', 'tersedia');
            })
            ->orderBy('stock.kategori')
            ->orderBy('stock.namabarang')
            ->get();
            
        // Get all users for dropdown
        $users = \App\Models\User::with('division')
            ->orderBy('name')
            ->get();
            
        return view('admin.barang-keluar.create', compact('stocks', 'users'));
    }

    /**
     * Store a newly created outgoing item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:aset_sewa,material_umum',
            'sub_kategori' => 'nullable|required_if:kategori,material_umum|in:barang_habis_pakai,barang_pinjam',
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);
        
        // Prevent aset_tetap from being borrowed
        if ($stock->kategori === 'aset_tetap') {
            return back()->withErrors(['idbarang' => 'Aset Tetap tidak dapat dipinjam/dikeluarkan!'])
                ->withInput();
        }

        // For Aset Sewa, qty must always be 1
        if ($stock->kategori === 'aset_sewa' && $validated['qty'] != 1) {
            return back()->withErrors(['qty' => 'Aset Sewa hanya bisa dipinjam 1 item per transaksi!'])
                ->withInput();
        }

        // Check if stock is sufficient
        if ($stock->stock < $validated['qty']) {
            return back()->withErrors(['qty' => 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock])
                ->withInput();
        }

        // Calculate end date for Aset Sewa
        $tanggalAkhirPakai = null;
        if ($stock->kategori === 'aset_sewa') {
            $durasi = $stock->durasi_sewa ?? 30;
            $tanggalAkhirPakai = \Carbon\Carbon::parse($validated['tanggal'])->addDays($durasi);
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
            'sub_kategori' => $stock->sub_kategori,
            'status' => $stock->kategori === 'aset_sewa' ? 'sedang_dipakai' : 'selesai',
            'durasi' => $stock->kategori === 'aset_sewa' ? $stock->durasi_sewa : null,
            'tanggal_akhir_pakai' => $tanggalAkhirPakai,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Update stock quantity
        $stock->decrement('stock', $validated['qty']);
        
        // Update status_kondisi for aset_sewa
        if ($stock->kategori === 'aset_sewa') {
            $stock->status_kondisi = 'digunakan';
            $stock->save();
        }

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
     */
    public function destroy($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        // Get stock and increment quantity (return to stock)
        $stock = Stock::findOrFail($barangKeluar->idbarang);
        $stock->increment('stock', $barangKeluar->qty);
        
        // Delete transaction
        $barangKeluar->delete();

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil dihapus!');
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
     * Mark rental as completed.
     */
    public function completeRental($idkeluar)
    {
        $barangKeluar = OutgoingTransaction::findOrFail($idkeluar);
        
        // Validate only for Aset Sewa with active status
        if ($barangKeluar->kategori !== 'aset_sewa') {
            return redirect()->back()->with('error', 'Hanya Aset Sewa yang bisa diselesaikan!');
        }
        
        if ($barangKeluar->status !== 'sedang_dipakai') {
            return redirect()->back()->with('error', 'Pemakaian sudah selesai atau ditarik!');
        }
        
        // Update status
        $barangKeluar->update([
            'status' => 'selesai',
            'tanggal_selesai' => now(),
        ]);
        
        // KURANGI STOK ketika rental selesai
        // Untuk Aset Sewa: stok berkurang jadi 0 setelah rental selesai (dikembalikan ke distributor)
        if ($barangKeluar->stock) {
            $barangKeluar->stock->decrement('stock');
            
            $barangKeluar->stock->update([
                'status_kondisi' => 'selesai',
                'keterangan_kondisi' => 'Pemakaian selesai - Aset dikembalikan ke distributor (stok = 0)',
                'tanggal_update_kondisi' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Pemakaian berhasil diselesaikan! Aset telah dikembalikan ke distributor (stok menjadi 0).');
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
