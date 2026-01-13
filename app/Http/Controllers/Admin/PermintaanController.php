<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestBarang;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    /**
     * Display a listing of requests
     */
    public function index(Request $request)
    {
        $query = RequestBarang::with(['user', 'stock']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by tipe request
        if ($request->filled('tipe_request')) {
            $query->where('tipe_request', $request->tipe_request);
        }
        
        $permintaan = $query->orderBy('tanggal_request', 'desc')->paginate(15);
        
        // Count by status for stats
        $stats = [
            'pending' => RequestBarang::where('status', 'pending')->count(),
            'approved' => RequestBarang::where('status', 'approved')->count(),
            'processing' => RequestBarang::where('status', 'processing')->count(),
            'completed' => RequestBarang::where('status', 'completed')->count(),
        ];
        
        return view('admin.permintaan.index', compact('permintaan', 'stats'));
    }

    /**
     * Display the specified request
     */
    public function show($id)
    {
        $permintaan = RequestBarang::with(['user', 'stock'])->findOrFail($id);
        return view('admin.permintaan.show', compact('permintaan'));
    }

    /**
     * Approve request
     */
    public function approve($id)
    {
        $permintaan = RequestBarang::findOrFail($id);
        
        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang bisa disetujui!');
        }
        
        // Check stock availability
        $stock = Stock::findOrFail($permintaan->idbarang);
        if ($stock->stock < $permintaan->qty) {
            return back()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock);
        }
        
        $permintaan->update([
            'status' => 'approved',
            'diproses_oleh' => Auth::user()->name,
            'tanggal_diproses' => now(),
        ]);
        
        return back()->with('success', 'Permintaan berhasil disetujui!');
    }

    /**
     * Process request (create outgoing transaction)
     */
    public function process(Request $request, $id)
    {
        $permintaan = RequestBarang::with('stock')->findOrFail($id);
        
        if ($permintaan->status !== 'approved') {
            return back()->with('error', 'Hanya permintaan yang sudah disetujui yang bisa diproses!');
        }
        
        DB::beginTransaction();
        try {
            // Check stock availability again
            if ($permintaan->stock->stock < $permintaan->qty) {
                throw new \Exception('Stok tidak mencukupi!');
            }
            
            // Create outgoing transaction
            OutgoingTransaction::create([
                'idbarang' => $permintaan->idbarang,
                'tanggal' => now(),
                'penerima' => $permintaan->user->name,
                'qty' => $permintaan->qty,
                'namabarang_k' => $permintaan->stock->namabarang,
                'kodebarang_k' => $permintaan->stock->kodebarang,
                'penginput' => Auth::user()->name,
                'kategori' => $permintaan->stock->kategori,
                'tanggal_mulai_sewa' => $permintaan->tanggal_mulai_sewa,
                'tanggal_akhir_sewa' => $permintaan->tanggal_akhir_sewa,
                'tipe_request' => $permintaan->tipe_request,
                'status_approval' => 'approved', // Mark as approved since admin processed it
            ]);
            
            // Update stock
            $permintaan->stock->decrement('stock', $permintaan->qty);
            
            // Update request status
            $permintaan->update([
                'status' => 'processing',
                'diproses_oleh' => Auth::user()->name,
                'tanggal_diproses' => now(),
            ]);
            
            DB::commit();
            return redirect()->route('admin.permintaan.index')->with('success', 'Permintaan berhasil diproses dan barang keluar telah dicatat!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Reject request
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan_admin' => 'required|string',
        ]);
        
        $permintaan = RequestBarang::findOrFail($id);
        
        if (!in_array($permintaan->status, ['pending', 'approved'])) {
            return back()->with('error', 'Permintaan ini tidak bisa ditolak!');
        }
        
        $permintaan->update([
            'status' => 'rejected',
            'catatan_admin' => $validated['catatan_admin'],
            'diproses_oleh' => Auth::user()->name,
            'tanggal_diproses' => now(),
        ]);
        
        return back()->with('success', 'Permintaan berhasil ditolak!');
    }

    /**
     * Complete request
     */
    public function complete($id)
    {
        $permintaan = RequestBarang::findOrFail($id);
        
        if ($permintaan->status !== 'processing') {
            return back()->with('error', 'Hanya permintaan yang sedang diproses yang bisa diselesaikan!');
        }
        
        $permintaan->update([
            'status' => 'completed',
        ]);
        
        return back()->with('success', 'Permintaan berhasil diselesaikan!');
    }

    /**
     * Update status manually
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,processing,rejected,completed',
            'catatan_admin' => 'nullable|string',
        ]);

        $permintaan = RequestBarang::with('stock')->findOrFail($id);
        $oldStatus = $permintaan->status;
        $newStatus = $validated['status'];

        // Validasi: Jika status diubah ke 'approved', cek stok
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            if ($permintaan->stock->stock < $permintaan->qty) {
                return back()->with('error', 'Tidak dapat menyetujui! Stok tidak mencukupi. Stok tersedia: ' . $permintaan->stock->stock);
            }
        }

        // Validasi: Jangan ubah status jika sudah processing/completed (stok sudah berkurang)
        if (in_array($oldStatus, ['processing', 'completed']) && $newStatus !== $oldStatus) {
            return back()->with('error', 'Tidak dapat mengubah status permintaan yang sudah diproses karena stok sudah berkurang. Hubungi developer jika perlu rollback.');
        }

        // Update status
        $permintaan->update([
            'status' => $newStatus,
            'catatan_admin' => $validated['catatan_admin'],
            'diproses_oleh' => Auth::user()->name,
            'tanggal_diproses' => now(),
        ]);

        return back()->with('success', "Status berhasil diubah dari {$oldStatus} ke {$newStatus}!");
    }
}
