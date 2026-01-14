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
        // Request biasa (bukan request perubahan)
        $requests = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->whereNull('parent_request_id')
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Request perubahan (yang punya parent_request_id)
        $changeRequests = RequestBarang::with(['stock', 'parentRequest'])
            ->where('user_id', Auth::id())
            ->whereNotNull('parent_request_id')
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        return view('user.request-barang.index', compact('requests', 'changeRequests'));
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
        
        return redirect()->route('user.pemakaian.index')->with('success', 'Permintaan berhasil dikirim!');
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
     * Show edit form
     */
    public function edit($id)
    {
        $requestBarang = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        // Only pending and approved can be edited
        if (!in_array($requestBarang->status, ['pending', 'approved'])) {
            return redirect()->route('user.pemakaian.index')
                ->with('error', 'Request ini tidak bisa diedit!');
        }
        
        // Calculate available stock
        // Jika approved: stok tersedia = stok current + qty yang sudah diambil
        // Jika pending: stok tersedia = stok current
        $availableStock = $requestBarang->status === 'approved' 
            ? $requestBarang->stock->stock + $requestBarang->qty
            : $requestBarang->stock->stock;
        
        // Get available stocks
        $stocks = Stock::whereIn('kategori', ['barang_sewa', 'habis_pakai'])
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
        
        return view('user.request-barang.edit', compact('requestBarang', 'stocks', 'availableStock'));
    }

    /**
     * Update request
     */
    public function update(Request $request, $id)
    {
        $requestBarang = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        $validated = $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'qty' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'catatan_user' => 'nullable|string',
            'tanggal_mulai_sewa' => 'nullable|date',
            'tanggal_akhir_sewa' => 'nullable|date|after:tanggal_mulai_sewa',
        ]);
        
        $stock = Stock::findOrFail($validated['idbarang']);
        
        // If pending, update directly
        if ($requestBarang->status === 'pending') {
            // Validate qty
            if ($validated['qty'] > $stock->stock) {
                return back()->with('error', 'Jumlah melebihi stok tersedia!')->withInput();
            }
            
            $requestBarang->update([
                'idbarang' => $validated['idbarang'],
                'qty' => $validated['qty'],
                'keperluan' => $validated['keperluan'],
                'catatan_user' => $validated['catatan_user'] ?? null,
                'tanggal_mulai_sewa' => $validated['tanggal_mulai_sewa'] ?? null,
                'tanggal_akhir_sewa' => $validated['tanggal_akhir_sewa'] ?? null,
            ]);
            
            return redirect()->route('user.pemakaian.index')
                ->with('success', 'Request berhasil diupdate!');
        }
        
        // If approved, create new request for change (need admin approval)
        if ($requestBarang->status === 'approved') {
            // Calculate available stock = current stock + qty yang sudah diambil
            $availableStock = $stock->stock + $requestBarang->qty;
            
            // Validate qty terhadap available stock
            if ($validated['qty'] > $availableStock) {
                return back()->with('error', "Jumlah melebihi stok tersedia! Stok tersedia: {$availableStock} (stok saat ini: {$stock->stock} + qty request Anda: {$requestBarang->qty})")->withInput();
            }
            
            $tipeRequest = match($stock->kategori) {
                'barang_sewa' => 'pinjam_sewa',
                'habis_pakai' => 'pakai_habis_pakai',
                default => null,
            };
            
            RequestBarang::create([
                'user_id' => Auth::id(),
                'idbarang' => $validated['idbarang'],
                'qty' => $validated['qty'],
                'tipe_request' => $tipeRequest,
                'keperluan' => $validated['keperluan'],
                'catatan_user' => 'PERUBAHAN REQUEST #' . $requestBarang->id_request . ': ' . ($validated['catatan_user'] ?? ''),
                'tanggal_mulai_sewa' => $validated['tanggal_mulai_sewa'] ?? null,
                'tanggal_akhir_sewa' => $validated['tanggal_akhir_sewa'] ?? null,
                'status' => 'pending',
                'tanggal_request' => now(),
                'parent_request_id' => $requestBarang->id_request, // Link to parent request
            ]);
            
            return redirect()->route('user.pemakaian.index')
                ->with('success', 'Request perubahan berhasil dibuat dan menunggu persetujuan admin!');
        }
        
        return back()->with('error', 'Request ini tidak bisa diupdate!');
    }

    /**
     * Request cancel for approved requests
     */
    public function requestCancel($id)
    {
        $requestBarang = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($requestBarang->status !== 'approved') {
            return back()->with('error', 'Hanya request yang sudah approved yang bisa dibatalkan!');
        }
        
        // Create new cancel request with parent_request_id
        RequestBarang::create([
            'user_id' => Auth::id(),
            'idbarang' => $requestBarang->idbarang,
            'qty' => $requestBarang->qty,
            'tipe_request' => $requestBarang->tipe_request,
            'keperluan' => 'PEMBATALAN REQUEST #' . $requestBarang->id_request,
            'catatan_user' => 'User meminta pembatalan peminjaman/permintaan. Admin mohon return barang dan stok.',
            'tanggal_mulai_sewa' => $requestBarang->tanggal_mulai_sewa,
            'tanggal_akhir_sewa' => $requestBarang->tanggal_akhir_sewa,
            'status' => 'pending',
            'tanggal_request' => now(),
            'parent_request_id' => $requestBarang->id_request, // Link to parent request
        ]);
        
        return redirect()->route('user.pemakaian.index')
            ->with('success', 'Request pembatalan berhasil dibuat! Menunggu persetujuan admin untuk return barang.');
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
        
        return redirect()->route('user.pemakaian.index')->with('success', 'Permintaan berhasil dihapus!');
    }

    /**
     * Mark request as completed (move to history)
     */
    public function complete($id)
    {
        $request = RequestBarang::where('id_request', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        // Auto-reject semua pending change requests untuk request ini
        RequestBarang::where('parent_request_id', $id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'catatan_admin' => 'Request ditolak otomatis karena request asli sudah selesai.',
                'diproses_oleh' => 'System',
                'tanggal_diproses' => now(),
            ]);

        $request->update([
            'status' => 'completed',
        ]);

        return redirect()->route('user.pemakaian.index')
            ->with('success', 'Request berhasil ditandai selesai dan masuk ke history.');
    }
}
