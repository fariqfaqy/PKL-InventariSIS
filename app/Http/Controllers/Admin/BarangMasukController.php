<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of incoming items.
     */
    public function index()
    {
        $barangMasuk = IncomingTransaction::with('stock')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        
        return view('admin.barang-masuk.index', compact('barangMasuk'));
    }

    /**
     * Show the form for creating a new incoming item.
     */
    public function create()
    {
        return view('admin.barang-masuk.create');
    }

    /**
     * Store a newly created incoming item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kodebarang' => 'required|string|max:255',
            'namabarang' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Check if stock exists by kodebarang
        $stock = Stock::where('kodebarang', $validated['kodebarang'])->first();

        if ($stock) {
            // If stock exists, update the quantity
            $stock->increment('stock', $validated['qty']);
            $idbarang = $stock->idbarang;
        } else {
            // If stock doesn't exist, create new stock
            $stock = Stock::create([
                'kodebarang' => $validated['kodebarang'],
                'namabarang' => $validated['namabarang'],
                'stock' => $validated['qty'],
                'deskripsi' => 'Barang baru',
                'rack' => '1a',
                'penginput' => Auth::user()->name,
            ]);
            $idbarang = $stock->idbarang;
        }

        // Create incoming transaction
        IncomingTransaction::create([
            'idbarang' => $idbarang,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
            'qty' => $validated['qty'],
            'namabarang_m' => $validated['namabarang'],
            'kodebarang_m' => $validated['kodebarang'],
            'penginput' => Auth::user()->name,
        ]);

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil ditambahkan!');
    }

    /**
     * Display the specified incoming item.
     */
    public function show(string $id)
    {
        $barangMasuk = IncomingTransaction::with('stock')->findOrFail($id);
        return view('admin.barang-masuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified incoming item.
     */
    public function edit(string $id)
    {
        $barangMasuk = IncomingTransaction::findOrFail($id);
        $stocks = Stock::orderBy('namabarang')->get();
        return view('admin.barang-masuk.edit', compact('barangMasuk', 'stocks'));
    }

    /**
     * Update the specified incoming item in storage.
     */
    public function update(Request $request, string $id)
    {
        $barangMasuk = IncomingTransaction::findOrFail($id);
        
        $validated = $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Get stock data
        $stock = Stock::findOrFail($validated['idbarang']);

        // Calculate stock adjustment
        $qtyDifference = $validated['qty'] - $barangMasuk->qty;
        
        // Update stock quantity if there's a difference
        if ($qtyDifference != 0) {
            $stock->increment('stock', $qtyDifference);
        }

        // Update incoming transaction
        $barangMasuk->update([
            'idbarang' => $validated['idbarang'],
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'],
            'qty' => $validated['qty'],
            'namabarang_m' => $stock->namabarang,
            'kodebarang_m' => $stock->kodebarang,
        ]);

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil diperbarui!');
    }

    /**
     * Remove the specified incoming item from storage.
     */
    public function destroy(string $id)
    {
        $barangMasuk = IncomingTransaction::findOrFail($id);
        
        // Get stock and decrement quantity
        $stock = Stock::findOrFail($barangMasuk->idbarang);
        $stock->decrement('stock', $barangMasuk->qty);
        
        // Delete transaction
        $barangMasuk->delete();

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dihapus!');
    }
}
