<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StokBarangController extends Controller
{
    /**
     * Display a listing of stock items.
     */
    public function index()
    {
        $stocks = Stock::orderBy('namabarang')->paginate(10);
        return view('admin.stok-barang.index', compact('stocks'));
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
        ]);

        Stock::create([
            'kodebarang' => $validated['kodebarang'],
            'namabarang' => $validated['namabarang'],
            'deskripsi' => $validated['deskripsi'] ?? 'Barang baru',
            'stock' => $validated['stock'],
            'rack' => $validated['rack'],
            'penginput' => auth()->user()->name,
        ]);

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil ditambahkan!');
    }

    /**
     * Display the specified stock item.
     */
    public function show(string $id)
    {
        $stock = Stock::with(['incomingTransactions', 'outgoingTransactions'])->findOrFail($id);
        return view('admin.stok-barang.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified stock item.
     */
    public function edit(string $id)
    {
        $stock = Stock::findOrFail($id);
        return view('admin.stok-barang.edit', compact('stock'));
    }

    /**
     * Update the specified stock item in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = Stock::findOrFail($id);
        
        $validated = $request->validate([
            'kodebarang' => 'required|string|max:255|unique:stock,kodebarang,' . $id . ',idbarang',
            'namabarang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
        ]);

        $stock->update($validated);

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil diperbarui!');
    }

    /**
     * Remove the specified stock item from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil dihapus!');
    }
}
