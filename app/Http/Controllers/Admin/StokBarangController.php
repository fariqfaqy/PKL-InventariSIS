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
        if ($request->filled('kategori') && in_array($request->kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $request->kategori);
            
            // For Aset Sewa: Load latest OutgoingTransaction (active or completed)
            if ($request->kategori === 'aset_sewa') {
                $query->with(['outgoingTransactions' => function($q) {
                    $q->whereNull('id_request')
                      ->latest();
                }]);
            }
        }

        // Filter by sub_kategori (khusus untuk Material Umum)
        if ($request->filled('sub_kategori') && in_array($request->sub_kategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('sub_kategori', $request->sub_kategori);
        }
        
        // Search by kode or nama barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang', 'ILIKE', "%{$search}%")
                  ->orWhere('namabarang', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by rack
        if ($request->filled('rack')) {
            $query->where('rack', $request->rack);
        }
        
        // Filter by stock status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'aman':
                    $query->where('stock', '>=', 10);
                    break;
                case 'menengah':
                    $query->whereBetween('stock', [5, 9]);
                    break;
                case 'kritis':
                    $query->where('stock', '<=', 4);
                    break;
            }
        }
        
        $stocks = $query->orderBy('namabarang')->paginate(10)->withQueryString();
        $kategori = $request->get('kategori');
        
        // Get distinct racks for filter dropdown
        $availableRacks = Stock::whereNotNull('rack')
            ->where('rack', '!=', '')
            ->distinct()
            ->orderBy('rack')
            ->pluck('rack');
        
        return view('admin.stok-barang.index', compact('stocks', 'kategori', 'availableRacks'));
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
            'kategori' => 'required|in:aset_sewa,material_umum,aset_tetap',
            'sub_kategori' => 'nullable|required_if:kategori,material_umum|in:barang_habis_pakai,barang_pinjam',
            'jenis' => 'required|string|max:100',
            'merek' => 'required|string|max:100',
            'tipe' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nama_pengguna' => 'nullable|string|max:255',
            'durasi_pakai' => 'nullable|integer|min:1',
            'tanggal_mulai_pakai' => 'nullable|date',
            'tanggal_akhir_pakai' => 'nullable|date|after_or_equal:tanggal_mulai_pakai',
        ], [
            'sub_kategori.required_if' => 'Sub-kategori wajib diisi untuk Material Umum',
            'sub_kategori.in' => 'Sub-kategori tidak valid',
        ]);

        // Handle image upload with security
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Security: Validate MIME type from actual file content (not extension)
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($image->getMimeType(), $allowedMimes)) {
                return back()->with('error', 'File harus berupa gambar (JPEG, PNG, JPG)!')->withInput();
            }
            
            // Security: Use hash untuk nama file (prevent filename manipulation)
            $extension = $image->getClientOriginalExtension();
            $imageName = hash('sha256', $validated['kodebarang'] . time() . uniqid()) . '.' . $extension;
            
            // Security: Validate final filename doesn't contain directory traversal
            if (preg_match('/\.\.|\/|\\\\/', $imageName)) {
                return back()->with('error', 'Nama file tidak valid!')->withInput();
            }
            
            $image->move(public_path('images/barang'), $imageName);
            $imagePath = $imageName;
        }

        Stock::create([
            'kodebarang' => $validated['kodebarang'],
            'namabarang' => $validated['namabarang'],
            'deskripsi' => $validated['deskripsi'] ?? 'Barang baru',
            'stock' => $validated['stock'],
            'rack' => $validated['rack'],
            'kategori' => $validated['kategori'],
            'sub_kategori' => $validated['kategori'] === 'material_umum' ? $validated['sub_kategori'] : null,
            'jenis' => $validated['jenis'],
            'merek' => $validated['merek'],
            'tipe' => $validated['tipe'],
            'image' => $imagePath,
            'penginput' => auth()->user()->name,
            'nama_pengguna' => $validated['nama_pengguna'] ?? null,
            'durasi_pakai' => $validated['durasi_pakai'] ?? null,
            'tanggal_mulai_pakai' => $validated['tanggal_mulai_pakai'] ?? null,
            'tanggal_akhir_pakai' => $validated['tanggal_akhir_pakai'] ?? null,
            'status_kondisi' => $validated['kategori'] === 'aset_sewa' ? 'digunakan' : null,
        ]);

        return redirect()->route('admin.stok-barang.index')
            ->with('success', 'Stok barang berhasil ditambahkan!');
    }

    /**
     * Display the specified stock item.
     */
    public function show($idbarang)
    {
        $stock = Stock::with([
            'incomingTransactions', 
            'outgoingTransactions' => function($query) {
                $query->with('user.division')
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($idbarang);
        
        // Get active rental for Aset Sewa
        $activeRental = null;
        if ($stock->kategori === 'aset_sewa') {
            $activeRental = $stock->outgoingTransactions()
                ->where('status', 'sedang_dipakai')
                ->whereNull('id_request')
                ->with('user.division')
                ->first();
        }
        
        return view('admin.stok-barang.show', compact('stock', 'activeRental'));
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
            'kategori' => 'required|in:aset_sewa,material_umum,aset_tetap',
            'sub_kategori' => 'nullable|required_if:kategori,material_umum|in:barang_habis_pakai,barang_pinjam',
            'jenis' => 'required|string|max:100',
            'merek' => 'required|string|max:100',
            'tipe' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'sub_kategori.required_if' => 'Sub-kategori wajib diisi untuk Material Umum',
            'sub_kategori.in' => 'Sub-kategori tidak valid',
        ]);

        // Handle image upload if present with security
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Security: Validate MIME type from actual file content
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($image->getMimeType(), $allowedMimes)) {
                return back()->with('error', 'File harus berupa gambar (JPEG, PNG, JPG)!')->withInput();
            }
            
            // Delete old image if exists
            if ($stock->image && file_exists(public_path('images/barang/' . $stock->image))) {
                unlink(public_path('images/barang/' . $stock->image));
            }
            
            // Security: Use hash untuk nama file
            $extension = $image->getClientOriginalExtension();
            $imageName = hash('sha256', $validated['kodebarang'] . time() . uniqid()) . '.' . $extension;
            
            // Security: Validate filename doesn't contain directory traversal
            if (preg_match('/\.\.|\/|\\\\/', $imageName)) {
                return back()->with('error', 'Nama file tidak valid!')->withInput();
            }
            
            $image->move(public_path('images/barang'), $imageName);
            $validated['image'] = $imageName;
        }

        // Set sub_kategori based on kategori
        if ($validated['kategori'] === 'material_umum') {
            $validated['sub_kategori'] = $validated['sub_kategori'];
        } else {
            $validated['sub_kategori'] = null;
        }

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
        if ($kategori && in_array($kategori, ['aset_sewa', 'material_umum', 'aset_tetap'])) {
            $query->where('kategori', $kategori);
        }

        // Filter by sub_kategori if provided
        $subKategori = $request->get('sub_kategori');
        if ($subKategori && in_array($subKategori, ['barang_habis_pakai', 'barang_pinjam'])) {
            $query->where('sub_kategori', $subKategori);
        }
        
        $stocks = $query->orderBy('namabarang')->get();
        
        $pdf = Pdf::loadView('admin.pdf.stok-barang', compact('stocks', 'kategori', 'subKategori'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Stok_Barang_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
