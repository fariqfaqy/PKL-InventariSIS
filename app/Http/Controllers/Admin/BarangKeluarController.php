<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use App\Models\Stock;
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
        
        // Filter by tipe_request if provided
        if ($request->filled('tipe') && in_array($request->tipe, ['pinjam_sewa', 'pakai_habis_pakai'])) {
            $query->where('tipe_request', $request->tipe);
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
        
        return view('admin.barang-keluar.index', compact('barangKeluar'));
    }

    /**
     * Show the form for creating a new outgoing item.
     */
    public function create()
    {
        // Exclude aset_tetap from available stocks
        $stocks = Stock::where('stock', '>', 0)
            ->whereIn('kategori', ['barang_sewa', 'habis_pakai', 'barang_pinjam'])
            ->orderBy('kategori')
            ->orderBy('jenis')
            ->orderBy('merek')
            ->orderBy('tipe')
            ->get(['idbarang', 'kodebarang', 'namabarang', 'stock', 'rack', 'kategori', 'jenis', 'merek', 'tipe', 'durasi_sewa', 'sub_kategori']);
        return view('admin.barang-keluar.create', compact('stocks'));
    }

    /**
     * Store a newly created outgoing item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:barang_sewa,material_umum',
            'sub_kategori' => 'nullable|required_if:kategori,material_umum|in:habis_pakai,barang_pinjam',
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);
        
        // Prevent aset_tetap from being borrowed
        if ($stock->kategori === 'aset_tetap') {
            return back()->withErrors(['idbarang' => 'Aset Tetap tidak dapat dipinjam/dikeluarkan!'])
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
            'kategori' => $stock->kategori,
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
        $barangKeluar = OutgoingTransaction::with('stock')->findOrFail($idkeluar);
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
     * Export outgoing transactions to PDF.
     */
    public function exportPdf()
    {
        $transactions = OutgoingTransaction::with('stock')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('admin.pdf.barang-keluar', compact('transactions'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Barang_Keluar_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
