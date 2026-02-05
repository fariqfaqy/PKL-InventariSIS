<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RequestBarang;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->paginate(10)
            ->withQueryString();
        
        // Request perubahan (yang punya parent_request_id)
        $changeRequests = RequestBarang::with(['stock', 'parentRequest'])
            ->where('user_id', Auth::id())
            ->whereNotNull('parent_request_id')
            ->orderBy('tanggal_request', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('user.request-barang.index', compact('requests', 'changeRequests'));
    }

    /**
     * Show form to create new request
     * 
     * User hanya bisa request Material Umum (kategori: material_umum)
     * dengan 2 sub-kategori:
     * - barang_habis_pakai (tidak dikembalikan)
     * - barang_pinjam (harus dikembalikan)
     * 
     * Aset Sewa hanya bisa dikelola oleh Admin
     */
    public function create()
    {
        // User HANYA bisa request Material Umum (material_umum)
        // Barang lain (Aset Sewa, Aset Tetap) hanya untuk admin
        $stocks = Stock::where('kategori', 'material_umum')
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
        
        return view('user.request-barang.create', compact('stocks'));
    }

    /**
     * Store new request
     * 
     * User hanya bisa request Material Umum (habis_pakai)
     * Validasi ketat: Block Aset Sewa & Aset Tetap
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
        
        // VALIDASI KETAT: User hanya bisa request Material Umum (material_umum)
        if ($stock->kategori !== 'material_umum') {
            return back()->with('error', 'Anda hanya dapat request Material Umum! Aset Sewa dan Aset Tetap hanya dapat dikelola oleh Admin.')->withInput();
        }
        
        // Validate qty tidak melebihi stok
        if ($validated['qty'] > $stock->stock) {
            return back()->with('error', 'Jumlah permintaan melebihi stok yang tersedia!')->withInput();
        }
        
        // Determine tipe request berdasarkan sub_kategori
        // barang_habis_pakai -> pakai_habis_pakai (tidak dikembalikan)
        // barang_pinjam -> pinjam_material (harus dikembalikan)
        $tipeRequest = match($stock->sub_kategori) {
            'barang_habis_pakai' => 'pakai_habis_pakai',
            'barang_pinjam' => 'pinjam_material',
            default => null,
        };
        
        if (!$tipeRequest) {
            return back()->with('error', 'Sub-kategori barang tidak valid! Pastikan barang memiliki sub-kategori yang tepat.')->withInput();
        }
        
        // Validate rental dates untuk barang pinjam
        if ($tipeRequest === 'pinjam_material') {
            if (!$request->filled('tanggal_mulai_sewa') || !$request->filled('tanggal_akhir_sewa')) {
                return back()->with('error', 'Tanggal pinjam dan kembali harus diisi untuk barang pinjam!')->withInput();
            }
        }
        
        RequestBarang::create([
            'user_id' => Auth::id(),
            'idbarang' => $validated['idbarang'],
            'qty' => $validated['qty'],
            'tipe_request' => $tipeRequest,
            'request_type' => 'normal',
            'keperluan' => $validated['keperluan'],
            'penerima' => Auth::user()->name, // Auto-detect from logged in user
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
        if (!$requestBarang->canBeEdited()) {
            return redirect()->route('user.pemakaian.index')
                ->with('error', 'Request ini tidak bisa diedit!');
        }
        
        // Untuk approved request: cek apakah sudah ada pending change request dengan locking
        if ($requestBarang->status === 'approved') {
            DB::beginTransaction();
            try {
                $hasPendingChange = RequestBarang::where('parent_request_id', $id)
                    ->where('status', 'pending')
                    ->lockForUpdate() // Prevent race condition
                    ->exists();
                
                DB::commit();
                
                if ($hasPendingChange) {
                    return redirect()->route('user.pemakaian.index')
                        ->with('error', 'Masih ada request perubahan yang pending! Tunggu admin proses dulu.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user.pemakaian.index')
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
        
        // Calculate available stock
        // Jika approved: stok tersedia = stok current + qty yang sudah diambil
        // Jika pending: stok tersedia = stok current
        $availableStock = $requestBarang->status === 'approved' 
            ? $requestBarang->stock->stock + $requestBarang->qty
            : $requestBarang->stock->stock;
        
        // Get available stocks - User hanya bisa request Material Umum
        $stocks = Stock::where('kategori', 'material_umum')
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
            // Double-check dengan locking untuk prevent race condition
            DB::beginTransaction();
            try {
                $hasPendingChange = RequestBarang::where('parent_request_id', $id)
                    ->where('status', 'pending')
                    ->lockForUpdate() // Lock untuk prevent double submit
                    ->exists();
                
                if ($hasPendingChange) {
                    DB::rollBack();
                    return redirect()->route('user.pemakaian.index')
                        ->with('error', 'Masih ada request perubahan yang pending! Tunggu admin proses dulu.');
                }
            
            // Calculate available stock = current stock + qty yang sudah diambil
            $availableStock = $stock->stock + $requestBarang->qty;
            
            // Validate qty terhadap available stock
            if ($validated['qty'] > $availableStock) {
                return back()->with('error', "Jumlah melebihi stok tersedia! Stok tersedia: {$availableStock} (stok saat ini: {$stock->stock} + qty request Anda: {$requestBarang->qty})")->withInput();
            }
            
            // Determine tipe request based on kategori & sub_kategori
            // User HANYA bisa request Material Umum (material_umum)
            // Aset Sewa tidak bisa di-request oleh user
            if ($stock->kategori !== 'material_umum') {
                return back()->with('error', 'Anda hanya dapat request Material Umum!')->withInput();
            }
            
            $tipeRequest = match($stock->sub_kategori) {
                'barang_habis_pakai' => 'pakai_habis_pakai',
                'barang_pinjam' => 'pinjam_material',
                default => null,
            };
            
            if (!$tipeRequest) {
                return back()->with('error', 'Sub-kategori barang tidak valid!')->withInput();
            }
            
            RequestBarang::create([
                'user_id' => Auth::id(),
                'idbarang' => $validated['idbarang'],
                'qty' => $validated['qty'],
                'tipe_request' => $tipeRequest,
                'request_type' => 'change',
                'keperluan' => $validated['keperluan'],
                'catatan_user' => 'PERUBAHAN REQUEST #' . $requestBarang->id_request . ': ' . ($validated['catatan_user'] ?? ''),
                'tanggal_mulai_sewa' => $validated['tanggal_mulai_sewa'] ?? null,
                'tanggal_akhir_sewa' => $validated['tanggal_akhir_sewa'] ?? null,
                'status' => 'pending',
                'tanggal_request' => now(),
                'parent_request_id' => $requestBarang->id_request, // Link to parent request
            ]);
            
                DB::commit(); // Commit transaction
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('user.pemakaian.index')
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
            
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
        
        if (!$requestBarang->canBeCancelled()) {
            return back()->with('error', 'Hanya request yang sudah approved yang bisa dibatalkan!');
        }
        
        // Create new cancel request with parent_request_id
        RequestBarang::create([
            'user_id' => Auth::id(),
            'idbarang' => $requestBarang->idbarang,
            'qty' => $requestBarang->qty,
            'tipe_request' => $requestBarang->tipe_request,
            'request_type' => 'cancellation',
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
        DB::beginTransaction();
        try {
            // Use locking to prevent race condition with admin approval
            $request = RequestBarang::where('user_id', Auth::id())
                ->where('id_request', $id)
                ->lockForUpdate()
                ->firstOrFail();
            
            if (!$request->canBeDeleted()) {
                DB::rollBack();
                return back()->with('error', 'Hanya permintaan dengan status pending yang bisa dihapus!');
            }
            
            $request->delete();
            
            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Permintaan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Mark request as completed (move to history)
     * Used when user returns borrowed items (pinjam_material only)
     */
    public function complete($id)
    {
        $request = RequestBarang::with('stock')
            ->where('id_request', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Auto-reject semua pending change requests untuk request ini
            RequestBarang::where('parent_request_id', $id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'catatan_admin' => 'Request ditolak otomatis karena request asli sudah selesai.',
                    'diproses_oleh' => 'System',
                    'tanggal_diproses' => now(),
                ]);

            // Update OutgoingTransaction status ke 'selesai' dan set tanggal_selesai
            OutgoingTransaction::where('id_request', $id)
                ->update([
                    'status' => 'selesai',
                    'tanggal_selesai' => now(),
                ]);

            // Kembalikan stok HANYA untuk Barang Pinjam (pinjam_material)
            // Aset Sewa di-assign admin langsung, tidak melalui request user
            if ($request->tipe_request === 'pinjam_material') {
                Stock::where('idbarang', $request->idbarang)
                    ->increment('stock', $request->qty);
            }
            
            // Update status kondisi HANYA untuk Aset Sewa (aset_sewa) yang selesai
            if ($request->stock->kategori === 'aset_sewa') {
                Stock::where('idbarang', $request->idbarang)->update([
                    'status_kondisi' => 'digunakan',
                    'keterangan_kondisi' => 'Selesai digunakan oleh ' . $request->user->name,
                    'tanggal_update_kondisi' => now(),
                ]);
            }

            $request->update([
                'status' => 'completed',
            ]);

            DB::commit();
            return redirect()->route('user.pemakaian.index')
                ->with('success', 'Barang telah dikembalikan dan request masuk ke history.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menandai selesai: ' . $e->getMessage());
        }
    }

    /**
     * Request extension - create change request to extend rental period
     * User membuat request perpanjangan untuk barang pinjam
     */
    public function requestExtend(Request $request, $id)
    {
        $requestBarang = RequestBarang::with(['stock'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        // Validasi: harus tipe pinjam_material
        if ($requestBarang->tipe_request !== 'pinjam_material') {
            return back()->with('error', 'Hanya peminjaman material yang dapat diperpanjang!');
        }
        
        // Validasi: harus status approved
        if ($requestBarang->status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang sudah disetujui yang bisa diperpanjang!');
        }
        
        // Validasi: harus ada tanggal akhir sewa
        if (!$requestBarang->tanggal_akhir_sewa) {
            return back()->with('error', 'Peminjaman ini tidak memiliki tanggal kembali!');
        }
        
        $validated = $request->validate([
            'tanggal_akhir_sewa_baru' => 'required|date|after:' . $requestBarang->tanggal_akhir_sewa->format('Y-m-d'),
            'alasan_perpanjangan' => 'required|string|max:500',
        ], [
            'tanggal_akhir_sewa_baru.required' => 'Tanggal kembali baru harus diisi',
            'tanggal_akhir_sewa_baru.date' => 'Format tanggal tidak valid',
            'tanggal_akhir_sewa_baru.after' => 'Tanggal kembali baru harus setelah tanggal saat ini (' . $requestBarang->tanggal_akhir_sewa->format('d/m/Y') . ')',
            'alasan_perpanjangan.required' => 'Alasan perpanjangan harus diisi',
            'alasan_perpanjangan.max' => 'Alasan perpanjangan maksimal 500 karakter',
        ]);
        
        // Create new change request untuk perpanjangan
        RequestBarang::create([
            'user_id' => Auth::id(),
            'idbarang' => $requestBarang->idbarang,
            'qty' => $requestBarang->qty,
            'tipe_request' => $requestBarang->tipe_request,
            'request_type' => 'change', // Tipe: perubahan
            'keperluan' => 'PERPANJANGAN REQUEST #' . $requestBarang->id_request,
            'penerima' => $requestBarang->penerima,
            'catatan_user' => 'Request perpanjangan durasi peminjaman. Alasan: ' . $validated['alasan_perpanjangan'],
            'tanggal_mulai_sewa' => $requestBarang->tanggal_mulai_sewa,
            'tanggal_akhir_sewa' => $validated['tanggal_akhir_sewa_baru'], // Tanggal baru
            'status' => 'pending',
            'tanggal_request' => now(),
            'parent_request_id' => $requestBarang->id_request, // Link ke request asli
        ]);
        
        return redirect()->route('user.request-barang.show', $id)
            ->with('success', 'Request perpanjangan berhasil dibuat! Menunggu persetujuan admin untuk memperpanjang durasi peminjaman sampai ' . \Carbon\Carbon::parse($validated['tanggal_akhir_sewa_baru'])->format('d/m/Y') . '.');
    }
}
