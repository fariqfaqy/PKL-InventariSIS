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
        // Request biasa (bukan request perubahan dan bukan completed)
        $normalRequests = RequestBarang::with(['user', 'stock'])
            ->whereNull('parent_request_id')
            ->where('status', '!=', 'completed')
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Request perubahan (yang punya parent_request_id)
        $changeRequests = RequestBarang::with(['user', 'stock', 'parentRequest'])
            ->whereNotNull('parent_request_id')
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Request completed untuk history
        $completedRequests = RequestBarang::with(['user', 'stock'])
            ->where('status', 'completed')
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Barang keluar (approved outgoing transactions) untuk history
        $outgoingTransactions = OutgoingTransaction::with('stock')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // Count by status for stats
        $stats = [
            'pending' => RequestBarang::where('status', 'pending')->count(),
            'approved' => RequestBarang::where('status', 'approved')->count(),
            'processing' => RequestBarang::where('status', 'processing')->count(),
            'completed' => RequestBarang::where('status', 'completed')->count(),
        ];
        
        return view('admin.permintaan.index', compact('normalRequests', 'changeRequests', 'completedRequests', 'outgoingTransactions', 'stats'));
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
     * Approve request - Berikan barang ke user (barang keluar)
     */
    public function approve($id)
    {
        $permintaan = RequestBarang::with(['stock', 'parentRequest'])->findOrFail($id);
        
        if (!in_array($permintaan->status, ['pending', 'processing'])) {
            return back()->with('error', 'Hanya permintaan dengan status pending atau processing yang bisa disetujui!');
        }
        
        DB::beginTransaction();
        try {
            // Cek apakah ini request perubahan atau request biasa
            if ($permintaan->parent_request_id && $permintaan->parentRequest) {
                // INI REQUEST PERUBAHAN atau PEMBATALAN
                
                // Cek apakah ini request pembatalan (dari keperluan atau catatan)
                $isPembatalan = stripos($permintaan->keperluan, 'PEMBATALAN') !== false;
                
                if ($isPembatalan) {
                    // INI REQUEST PEMBATALAN - user mau return barang
                    // Tambah stok sejumlah qty yang dikembalikan
                    $permintaan->stock->increment('stock', $permintaan->qty);
                    
                    $message = "Request pembatalan disetujui! User mengembalikan {$permintaan->qty} unit. Stok bertambah {$permintaan->qty}.";
                    
                } else {
                    // INI REQUEST PERUBAHAN QTY - hitung selisih
                    $qtyLama = $permintaan->parentRequest->qty;
                    $qtyBaru = $permintaan->qty;
                    $selisih = $qtyBaru - $qtyLama;
                    
                    if ($selisih > 0) {
                        // User minta TAMBAHAN barang (qty naik)
                        // Cek stok cukup untuk tambahan
                        if ($permintaan->stock->stock < $selisih) {
                            throw new \Exception('Stok tidak mencukupi untuk tambahan! Stok tersedia: ' . $permintaan->stock->stock . ', dibutuhkan: ' . $selisih);
                        }
                        
                        // Kurangi stok sejumlah tambahan
                        $permintaan->stock->decrement('stock', $selisih);
                        
                        $message = "Request perubahan disetujui! Tambahan {$selisih} unit telah diberikan ke user. Stok berkurang {$selisih}.";
                        
                    } elseif ($selisih < 0) {
                        // User MENGEMBALIKAN barang (qty turun)
                        $jumlahKembali = abs($selisih);
                        
                        // Tambah stok sejumlah yang dikembalikan
                        $permintaan->stock->increment('stock', $jumlahKembali);
                        
                        $message = "Request perubahan disetujui! User mengembalikan {$jumlahKembali} unit. Stok bertambah {$jumlahKembali}.";
                        
                    } else {
                        // Qty sama, tidak ada perubahan stok
                        $message = "Request perubahan disetujui! Tidak ada perubahan qty.";
                    }
                }
                
                // Update status request perubahan/pembatalan
                $permintaan->update([
                    'status' => 'approved',
                    'diproses_oleh' => Auth::user()->name,
                    'tanggal_diproses' => now(),
                ]);
                
            } else {
                // INI REQUEST BIASA - logic seperti biasa
                
                // Check stock availability
                if ($permintaan->stock->stock < $permintaan->qty) {
                    throw new \Exception('Stok tidak mencukupi! Stok tersedia: ' . $permintaan->stock->stock);
                }
                
                // Convert tipe_request: pinjam_sewa -> peminjaman, pakai_habis_pakai -> permintaan
                $tipeKeluar = $permintaan->tipe_request === 'pinjam_sewa' ? 'peminjaman' : 'permintaan';
                
                // Create outgoing transaction (barang keluar)
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
                    'tipe' => $tipeKeluar,
                    'status_approval' => 'approved',
                ]);
                
                // Update stock (kurangi stok)
                $permintaan->stock->decrement('stock', $permintaan->qty);
                
                // Update request status
                $permintaan->update([
                    'status' => 'approved',
                    'diproses_oleh' => Auth::user()->name,
                    'tanggal_diproses' => now(),
                ]);
                
                $message = 'Permintaan disetujui dan barang telah diberikan ke user!';
            }
            
            DB::commit();
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Process request - Admin sedang mengecek/memverifikasi barang
     */
    public function process(Request $request, $id)
    {
        $permintaan = RequestBarang::with('stock')->findOrFail($id);
        
        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan dengan status pending yang bisa diproses!');
        }
        
        // Check stock availability
        $stock = Stock::findOrFail($permintaan->idbarang);
        if ($stock->stock < $permintaan->qty) {
            return back()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock);
        }
        
        // Update status jadi processing (admin sedang ngecek barang)
        $permintaan->update([
            'status' => 'processing',
            'diproses_oleh' => Auth::user()->name,
            'tanggal_diproses' => now(),
        ]);
        
        return back()->with('success', 'Permintaan sedang diproses. Silakan setujui untuk memberikan barang ke user.');
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

    /**
     * Mark request as completed (admin can mark approved requests as completed)
     */
    public function markComplete($id)
    {
        $permintaan = RequestBarang::findOrFail($id);
        
        if ($permintaan->status !== 'approved') {
            return back()->with('error', 'Hanya request yang sudah disetujui yang bisa ditandai selesai!');
        }
        
        $permintaan->update([
            'status' => 'completed',
        ]);
        
        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Request berhasil ditandai selesai dan masuk ke history!');
    }
}
