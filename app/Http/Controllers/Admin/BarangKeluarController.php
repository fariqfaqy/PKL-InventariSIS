<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of outgoing items.
     */
    public function index()
    {
        $barangKeluar = OutgoingTransaction::with('stock')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        
        return view('admin.barang-keluar.index', compact('barangKeluar'));
    }

    /**
     * Show the form for creating a new outgoing item.
     */
    public function create()
    {
        $stocks = Stock::where('stock', '>', 0)
            ->orderBy('kategori')
            ->orderBy('jenis')
            ->orderBy('merek')
            ->orderBy('tipe')
            ->get(['idbarang', 'kodebarang', 'namabarang', 'stock', 'rack', 'kategori', 'jenis', 'merek', 'tipe']);
        return view('admin.barang-keluar.create', compact('stocks'));
    }

    /**
     * Store a newly created outgoing item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);

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
}
