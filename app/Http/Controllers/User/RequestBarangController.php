<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RequestBarang;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestBarangController extends Controller
{
    /**
     * Display user's requests
     */
    public function index(Request $request)
    {
        $query = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id());
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $requests = $query->orderBy('tanggal_request', 'desc')->paginate(15);
        
        return view('user.request-barang.index', compact('requests'));
    }

    /**
     * Show form to create new request
     */
    public function create()
    {
        // Only show barang sewa dan habis pakai (exclude aset tetap)
        $stocks = Stock::whereIn('kategori', ['barang_sewa', 'habis_pakai'])
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
        
        return view('user.request-barang.create', compact('stocks'));
    }

    /**
     * Store new request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'qty' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'catatan_user' => 'nullable|string',
            'tanggal_mulai_sewa' => 'nullable|date',
            'tanggal_akhir_sewa' => 'nullable|date|after:tanggal_mulai_sewa',
        ]);
        
        // Get stock
        $stock = Stock::findOrFail($validated['idbarang']);
        
        // Validate qty tidak melebihi stok
        if ($validated['qty'] > $stock->stock) {
            return back()->with('error', 'Jumlah permintaan melebihi stok yang tersedia!')->withInput();
        }
        
        // Determine tipe request berdasarkan kategori
        $tipeRequest = match($stock->kategori) {
            'barang_sewa' => 'pinjam_sewa',
            'habis_pakai' => 'pakai_habis_pakai',
            default => null,
        };
        
        if (!$tipeRequest) {
            return back()->with('error', 'Kategori barang tidak valid untuk request!')->withInput();
        }
        
        // Validate rental dates untuk barang sewa
        if ($tipeRequest === 'pinjam_sewa') {
            if (!$request->filled('tanggal_mulai_sewa') || !$request->filled('tanggal_akhir_sewa')) {
                return back()->with('error', 'Tanggal sewa harus diisi untuk barang sewa!')->withInput();
            }
        }
        
        RequestBarang::create([
            'user_id' => Auth::id(),
            'idbarang' => $validated['idbarang'],
            'qty' => $validated['qty'],
            'tipe_request' => $tipeRequest,
            'keperluan' => $validated['keperluan'],
            'catatan_user' => $validated['catatan_user'] ?? null,
            'tanggal_mulai_sewa' => $validated['tanggal_mulai_sewa'] ?? null,
            'tanggal_akhir_sewa' => $validated['tanggal_akhir_sewa'] ?? null,
            'status' => 'pending',
            'tanggal_request' => now(),
        ]);
        
        return redirect()->route('user.request-barang.index')->with('success', 'Permintaan berhasil dikirim!');
    }

    /**
     * Display request detail
     */
    public function show($id)
    {
        $request = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        return view('user.request-barang.show', compact('request'));
    }

    /**
     * Delete request (only pending requests can be deleted)
     */
    public function destroy($id)
    {
        $request = RequestBarang::where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($request->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang bisa dihapus!');
        }
        
        $request->delete();
        
        return redirect()->route('user.request-barang.index')->with('success', 'Permintaan berhasil dihapus!');
    }
}
