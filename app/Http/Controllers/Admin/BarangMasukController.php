<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $stocks = Stock::orderBy('kodebarang')->get(['kodebarang', 'namabarang', 'deskripsi', 'rack', 'stock', 'kategori', 'jenis', 'merek', 'tipe']);
        return view('admin.barang-masuk.create', compact('stocks'));
    }

    /**
     * Store a newly created incoming item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kodebarang' => 'required|string|max:255',
            'namabarang' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori' => 'required|in:barang_sewa,habis_pakai,aset_tetap',
            'qty' => 'required|integer|min:1',
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload with security
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Security: Validate MIME type from actual file content
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($image->getMimeType(), $allowedMimes)) {
                return back()->with('error', 'File harus berupa gambar (JPEG, PNG, JPG)!')->withInput();
            }
            
            // Security: Use hash untuk nama file (prevent filename manipulation)
            $extension = $image->getClientOriginalExtension();
            $imageName = hash('sha256', $validated['kodebarang'] . time() . uniqid()) . '.' . $extension;
            
            // Security: Validate filename doesn't contain directory traversal
            if (preg_match('/\.\.|\/|\\\\/', $imageName)) {
                return back()->with('error', 'Nama file tidak valid!')->withInput();
            }
            
            $image->move(public_path('images/barang'), $imageName);
            $imagePath = $imageName; // Simpan hanya nama file
        }

        // Check if stock exists by kodebarang
        $stock = Stock::where('kodebarang', $validated['kodebarang'])->first();

        if ($stock) {
            // If stock exists, update the quantity and other fields
            $stock->increment('stock', $validated['qty']);
            
            // Update kategori, namabarang, deskripsi if provided
            $stock->kategori = $validated['kategori'];
            $stock->namabarang = $validated['namabarang'];
            if (isset($validated['deskripsi'])) {
                $stock->deskripsi = $validated['deskripsi'];
            }
            
            // Update image if new one uploaded
            if ($imagePath) {
                // Delete old image if exists
                if ($stock->image && file_exists(public_path('images/barang/' . $stock->image))) {
                    unlink(public_path('images/barang/' . $stock->image));
                }
                $stock->image = $imagePath;
            }
            
            $stock->save();
            $idbarang = $stock->idbarang;
        } else {
            // If stock doesn't exist, create new stock
            $stock = Stock::create([
                'kodebarang' => $validated['kodebarang'],
                'namabarang' => $validated['namabarang'],
                'stock' => $validated['qty'],
                'deskripsi' => $validated['deskripsi'] ?? 'Barang baru',
                'image' => $imagePath,
                'rack' => $validated['rack'],
                'kategori' => $validated['kategori'],
                'penginput' => auth()->user()->name,
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
    public function show($idmasuk)
    {
        $barangMasuk = IncomingTransaction::with('stock')->findOrFail($idmasuk);
        return view('admin.barang-masuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified incoming item.
     */
    public function edit($idmasuk)
    {
        $barangMasuk = IncomingTransaction::findOrFail($idmasuk);
        $stocks = Stock::orderBy('namabarang')->get();
        return view('admin.barang-masuk.edit', compact('barangMasuk', 'stocks'));
    }

    /**
     * Update the specified incoming item in storage.
     */
    public function update(Request $request, $idmasuk)
    {
        $barangMasuk = IncomingTransaction::findOrFail($idmasuk);
        
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
    public function destroy($idmasuk)
    {
        $barangMasuk = IncomingTransaction::findOrFail($idmasuk);
        
        // Get stock and decrement quantity
        $stock = Stock::findOrFail($barangMasuk->idbarang);
        $stock->decrement('stock', $barangMasuk->qty);
        
        // Delete transaction
        $barangMasuk->delete();

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dihapus!');
    }

    /**
     * Check if stock exists by kodebarang (AJAX)
     */
    public function checkStock(Request $request)
    {
        $kodebarang = $request->input('kodebarang');
        
        if (!$kodebarang) {
            return response()->json(['exists' => false]);
        }

        $stock = Stock::where('kodebarang', $kodebarang)->first();
        
        if ($stock) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'kodebarang' => $stock->kodebarang,
                    'namabarang' => $stock->namabarang,
                    'kategori' => $stock->kategori,
                    'rack' => $stock->rack,
                    'deskripsi' => $stock->deskripsi,
                    'current_stock' => $stock->stock,
                    'jenis' => $stock->jenis,
                    'merek' => $stock->merek,
                    'tipe' => $stock->tipe,
                    'image' => $stock->image,
                ]
            ]);
        }
        
        return response()->json(['exists' => false]);
    }

    /**
     * Export incoming transactions to PDF.
     */
    public function exportPdf()
    {
        $transactions = IncomingTransaction::with('stock')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('admin.pdf.barang-masuk', compact('transactions'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Barang_Masuk_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
