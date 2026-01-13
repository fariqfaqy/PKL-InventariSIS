<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StokBarangController extends Controller
{
    /**
     * Display a listing of stock items.
     */
    public function index(Request $request)
    {
        $query = Stock::query();
        
        // Filter by kategori if provided
        if ($request->has('kategori') && in_array($request->kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
        }
        
        $stocks = $query->orderBy('namabarang')->paginate(10);
        $kategori = $request->get('kategori');
        
        return view('admin.stok-barang.index', compact('stocks', 'kategori'));
    }

    /**
     * Show the form for creating a new stock item.
     */
    public function create()
    {
        return view('admin.stok-barang.create');
    }

    /**
     * Store a newly created stock item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kodebarang' => 'required|string|unique:stock,kodebarang|max:255',
            'namabarang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
            'kategori' => 'required|in:barang_sewa,habis_pakai',
            'jenis' => 'required|string|max:100',
            'merek' => 'required|string|max:100',
            'tipe' => 'required|string|max:255',
        ]);

        Stock::create([
            'kodebarang' => $validated['kodebarang'],
            'namabarang' => $validated['namabarang'],
            'deskripsi' => $validated['deskripsi'] ?? 'Barang baru',
            'stock' => $validated['stock'],
            'rack' => $validated['rack'],
            'kategori' => $validated['kategori'],
            'jenis' => $validated['jenis'],
            'merek' => $validated['merek'],
            'tipe' => $validated['tipe'],
            'penginput' => auth()->user()->name,
        ]);

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil ditambahkan!');
    }

    /**
     * Display the specified stock item.
     */
    public function show($idbarang)
    {
        $stock = Stock::with(['incomingTransactions', 'outgoingTransactions'])->findOrFail($idbarang);
        return view('admin.stok-barang.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified stock item.
     */
    public function edit($idbarang)
    {
        $stock = Stock::findOrFail($idbarang);
        return view('admin.stok-barang.edit', compact('stock'));
    }

    /**
     * Update the specified stock item in storage.
     */
    public function update(Request $request, $idbarang)
    {
        $stock = Stock::findOrFail($idbarang);
        
        $validated = $request->validate([
            'kodebarang' => 'required|string|max:255|unique:stock,kodebarang,' . $idbarang . ',idbarang',
            'namabarang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
            'kategori' => 'required|in:barang_sewa,habis_pakai',
            'jenis' => 'required|string|max:100',
            'merek' => 'required|string|max:100',
            'tipe' => 'required|string|max:255',
        ]);

        $stock->update($validated);

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil diperbarui!');
    }

    /**
     * Remove the specified stock item from storage.
     */
    public function destroy($idbarang)
    {
        $stock = Stock::findOrFail($idbarang);
        $stock->delete();

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil dihapus!');
    }

    /**
     * Export stock data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = Stock::query();
        
        // Filter by kategori if provided
        $kategori = $request->get('kategori');
        if ($kategori && in_array($kategori, ['barang_sewa', 'habis_pakai'])) {
            $query->where('kategori', $kategori);
        }
        
        $stocks = $query->orderBy('namabarang')->get();
        
        $pdf = Pdf::loadView('admin.pdf.stok-barang', compact('stocks', 'kategori'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Stok_Barang_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
