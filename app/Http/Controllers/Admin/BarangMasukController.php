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
    public function index(Request $request)
    {
        $query = IncomingTransaction::with('stock')
            ->join('stock', 'masuk.idbarang', '=', 'stock.idbarang')
            ->select('masuk.*', 'stock.kategori', 'stock.sub_kategori');
        
        // Filter by kategori if provided
        if ($request->filled('kategori') && in_array($request->kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->where('stock.kategori', $request->kategori);
        }

        // Filter by sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('stock.sub_kategori', $request->sub_kategori);
        }
        
        // Search by kode or nama barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('masuk.kodebarang_m', 'like', '%' . $search . '%')
                  ->orWhere('masuk.namabarang_m', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('masuk.tanggal', $request->tanggal);
        }
        
        $barangMasuk = $query->orderBy('masuk.tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('admin.barang-masuk.index', compact('barangMasuk'));
    }

    /**
     * Show the form for creating a new incoming item.
     */
    public function create()
    {
        $stocks = Stock::orderBy('kodebarang')->get(['kodebarang', 'namabarang', 'deskripsi', 'rack', 'stock', 'kategori', 'jenis', 'merek', 'tipe']);
        $users = \App\Models\User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.barang-masuk.create', compact('stocks', 'users'));
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
            'sub_kategori' => 'nullable|required_if:kategori,habis_pakai|in:barang_habis_pakai,barang_pinjam',
            'qty' => 'required|integer|min:1',
            'rack' => 'nullable|required_if:kategori,habis_pakai|in:1a,1b,1c,2a,2b,2c',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'user_id' => 'nullable|required_if:kategori,barang_sewa|exists:users,id',
            'tanggal_mulai_pakai' => 'nullable|date',
            'tanggal_akhir_pakai' => 'nullable|date|after_or_equal:tanggal_mulai_pakai',
        ], [
            'sub_kategori.required_if' => 'Sub-kategori wajib diisi untuk Material Umum',
            'sub_kategori.in' => 'Sub-kategori harus berupa Material Umum atau Barang Pinjam',
            'rack.required_if' => 'Rak wajib diisi untuk Material Umum',
            'user_id.required_if' => 'User wajib dipilih untuk Aset Sewa',
            'user_id.exists' => 'User yang dipilih tidak valid',
            'tanggal_akhir_pakai.after_or_equal' => 'Tanggal akhir pakai harus sama dengan atau setelah tanggal mulai pakai',
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

        // For Material Umum (habis_pakai), ensure sub_kategori is set
        $actualKategori = $validated['kategori'];
        $actualSubKategori = ($validated['kategori'] === 'habis_pakai' && isset($validated['sub_kategori'])) 
            ? $validated['sub_kategori'] 
            : null;

        // For Aset Sewa: ALWAYS create new stock (1 kode = 1 physical item, no merging)
        // Skip stock checking for barang_sewa
        if ($actualKategori === 'barang_sewa') {
            $stock = null; // Force create new
            $validated['qty'] = 1; // Force qty = 1
        } else {
            // Check if stock exists by kodebarang (for other categories)
            $stock = Stock::where('kodebarang', $validated['kodebarang'])->first();
        }

        if ($stock) {
            // If stock exists, update the quantity and other fields
            $stock->increment('stock', $validated['qty']);
            
            // Update kategori, sub_kategori, namabarang, deskripsi
            $stock->kategori = $actualKategori;
            $stock->sub_kategori = $actualSubKategori;
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
            
            // Update user and rental dates for barang_sewa
            if ($validated['kategori'] === 'barang_sewa' && $request->filled('user_id')) {
                $user = \App\Models\User::findOrFail($validated['user_id']);
                
                // Create OutgoingTransaction for assignment
                \App\Models\OutgoingTransaction::create([
                    'idbarang' => $stock->idbarang,
                    'tanggal' => $validated['tanggal'],
                    'penerima' => $user->name,
                    'user_id' => $validated['user_id'],
                    'kategori' => 'barang_sewa',
                    'qty' => 1, // Aset sewa always 1 item
                    'keterangan' => 'Admin assignment: ' . ($validated['keterangan'] ?? 'Aset sewa baru'),
                    'namabarang_k' => $stock->namabarang,
                    'kodebarang_k' => $stock->kodebarang,
                    'status' => 'sedang_dipakai',
                    'penginput' => Auth::user()->name,
                    'tanggal_mulai_pakai' => $validated['tanggal_mulai_pakai'] ?? now(),
                    'tanggal_akhir_pakai' => $validated['tanggal_akhir_pakai'] ?? null,
                ]);
                
                // Set status kondisi
                $stock->status_kondisi = 'digunakan';
            }
            
            $stock->save();
            $idbarang = $stock->idbarang;
        } else {
            // If stock doesn't exist, create new stock
            $stockData = [
                'kodebarang' => $validated['kodebarang'],
                'namabarang' => $validated['namabarang'],
                'stock' => $validated['qty'],
                'deskripsi' => $validated['deskripsi'] ?? 'Barang baru',
                'image' => $imagePath,
                'rack' => $validated['rack'] ?? null, // Nullable untuk Aset Sewa & Aset Tetap
                'kategori' => $actualKategori,
                'sub_kategori' => $actualSubKategori,
                'penginput' => auth()->user()->name,
            ];
            
            // Set default status kondisi for barang_sewa
            if ($actualKategori === 'barang_sewa') {
                $stockData['status_kondisi'] = 'digunakan'; // Default status
            }
            
            $stock = Stock::create($stockData);
            $idbarang = $stock->idbarang;
            
            // Create OutgoingTransaction for barang_sewa assignment
            if ($actualKategori === 'barang_sewa' && $request->filled('user_id')) {
                $user = \App\Models\User::findOrFail($validated['user_id']);
                
                \App\Models\OutgoingTransaction::create([
                    'idbarang' => $stock->idbarang,
                    'tanggal' => $validated['tanggal'],
                    'penerima' => $user->name,
                    'user_id' => $validated['user_id'],
                    'kategori' => 'barang_sewa',
                    'qty' => 1, // Aset sewa always 1 item
                    'keterangan' => 'Admin assignment: ' . ($validated['keterangan'] ?? 'Aset sewa baru'),
                    'namabarang_k' => $stock->namabarang,
                    'kodebarang_k' => $stock->kodebarang,
                    'status' => 'sedang_dipakai',
                    'penginput' => Auth::user()->name,
                    'tanggal_mulai_pakai' => $validated['tanggal_mulai_pakai'] ?? now(),
                    'tanggal_akhir_pakai' => $validated['tanggal_akhir_pakai'] ?? null,
                ]);
            }
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
    public function exportPdf(Request $request)
    {
        $query = IncomingTransaction::with('stock')
            ->join('stock', 'masuk.idbarang', '=', 'stock.idbarang')
            ->select('masuk.*', 'stock.kategori', 'stock.sub_kategori');

        // Filter by kategori if provided
        $kategori = $request->get('kategori');
        if ($kategori && in_array($kategori, ['barang_sewa', 'habis_pakai', 'aset_tetap'])) {
            $query->where('stock.kategori', $kategori);
        }

        // Filter by sub_kategori if provided
        $subKategori = $request->get('sub_kategori');
        if ($subKategori && in_array($subKategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('stock.sub_kategori', $subKategori);
        }

        $transactions = $query->orderBy('masuk.tanggal', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.pdf.barang-masuk', compact('transactions', 'kategori', 'subKategori'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Barang_Masuk_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
