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
        // Request biasa (bukan request perubahan, bukan completed, bukan cancelled)
        // Eager load changeRequests untuk prevent N+1 query
        $normalRequests = RequestBarang::with([
                'user', 
                'stock',
                'changeRequests' => function($query) {
                    $query->where('status', 'pending')->latest();
                }
            ])
            ->whereNull('parent_request_id')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Request perubahan (yang punya parent_request_id)
        // Exclude yang parent-nya sudah completed/cancelled
        $changeRequests = RequestBarang::with(['user', 'stock', 'parentRequest'])
            ->whereNotNull('parent_request_id')
            ->whereHas('parentRequest', function($query) {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            })
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // History data dengan sub-tabs
        // 1. Sedang Dipakai - sudah disetujui tapi belum selesai
        $sedangDipakai = OutgoingTransaction::with(['stock', 'user'])
            ->whereNotNull('diproses_oleh')
            ->where('status', 'sedang_dipakai')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // 2. Selesai - sudah selesai
        $selesai = OutgoingTransaction::with(['stock', 'user'])
            ->where('status', 'selesai')
            ->orderBy('tanggal_selesai', 'desc')
            ->get();
        
        // 3. Ditolak/Dibatalkan - request yang rejected atau cancelled
        $ditolakDibatalkan = RequestBarang::with(['user', 'stock'])
            ->whereIn('status', ['rejected', 'cancelled'])
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // Count by status for stats
        $stats = [
            'pending' => RequestBarang::where('status', 'pending')->count(),
            'approved' => RequestBarang::where('status', 'approved')->count(),
            'processing' => RequestBarang::where('status', 'processing')->count(),
            'completed' => RequestBarang::where('status', 'completed')->count(),
            'cancelled' => RequestBarang::where('status', 'cancelled')->count(),
            'rejected' => RequestBarang::where('status', 'rejected')->count(),
        ];
        
        return view('admin.permintaan.index', compact('normalRequests', 'changeRequests', 'sedangDipakai', 'selesai', 'ditolakDibatalkan', 'stats'));
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
        
        if (!$permintaan->canBeApproved()) {
            return back()->with('error', 'Hanya permintaan dengan status pending atau processing yang bisa disetujui!');
        }
        
        DB::beginTransaction();
        try {
            // Cek apakah ini request perubahan atau request biasa
            if ($permintaan->parent_request_id && $permintaan->parentRequest) {
                // INI REQUEST PERUBAHAN atau PEMBATALAN
                
                // Cek apakah ini request pembatalan menggunakan helper method
                if ($permintaan->isCancellationRequest()) {
                    // INI REQUEST PEMBATALAN - user mau return barang
                    // Tambah stok sejumlah qty yang dikembalikan (direct query untuk atomic operation)
                    Stock::where('idbarang', $permintaan->idbarang)
                        ->increment('stock', $permintaan->qty);
                    
                    // Hapus entry barang keluar dari parent request
                    $parentKeluarEntry = OutgoingTransaction::where('id_request', $permintaan->parent_request_id)->first();
                    if ($parentKeluarEntry) {
                        $parentKeluarEntry->delete();
                    }
                    
                    // **IMPORTANT: Set parent request status ke 'cancelled'**
                    $permintaan->parentRequest->update([
                        'status' => 'cancelled',
                    ]);
                    
                    $message = "Request pembatalan disetujui! User mengembalikan {$permintaan->qty} unit. Stok bertambah {$permintaan->qty}. Entry barang keluar dihapus. Request asli dibatalkan.";
                    
                } else {
                    // INI REQUEST PERUBAHAN QTY - hitung selisih
                    $qtyLama = $permintaan->parentRequest->qty;
                    $qtyBaru = $permintaan->qty;
                    $selisih = $qtyBaru - $qtyLama;
                    
                    if ($selisih > 0) {
                        // User minta TAMBAHAN barang (qty naik)
                        // Cek stok cukup untuk tambahan
                        $currentStock = Stock::where('idbarang', $permintaan->idbarang)->value('stock');
                        if ($currentStock < $selisih) {
                            throw new \Exception('Stok tidak mencukupi untuk tambahan! Stok tersedia: ' . $currentStock . ', dibutuhkan: ' . $selisih);
                        }
                        
                        // Kurangi stok sejumlah tambahan (direct query untuk atomic operation)
                        Stock::where('idbarang', $permintaan->idbarang)
                            ->decrement('stock', $selisih);
                        
                        $message = "Request perubahan disetujui! Tambahan {$selisih} unit telah diberikan ke user. Stok berkurang {$selisih}.";
                        
                    } elseif ($selisih < 0) {
                        // User MENGEMBALIKAN barang (qty turun)
                        $jumlahKembali = abs($selisih);
                        
                        // Tambah stok sejumlah yang dikembalikan (direct query untuk atomic operation)
                        Stock::where('idbarang', $permintaan->idbarang)
                            ->increment('stock', $jumlahKembali);
                        
                        $message = "Request perubahan disetujui! User mengembalikan {$jumlahKembali} unit. Stok bertambah {$jumlahKembali}.";
                        
                    } else {
                        // Qty sama, tidak ada perubahan stok
                        $message = "Request perubahan disetujui! Tidak ada perubahan qty.";
                    }
                    
                    // Update entry barang keluar dari parent request dengan qty baru
                    $parentKeluarEntry = OutgoingTransaction::where('id_request', $permintaan->parent_request_id)->first();
                    if ($parentKeluarEntry) {
                        $parentKeluarEntry->update([
                            'qty' => $qtyBaru,
                            'tanggal_mulai_sewa' => $permintaan->tanggal_mulai_sewa,
                            'tanggal_akhir_sewa' => $permintaan->tanggal_akhir_sewa,
                        ]);
                    }
                    
                    // Update parent request dengan data baru
                    $permintaan->parentRequest->update([
                        'qty' => $qtyBaru,
                        'tanggal_mulai_sewa' => $permintaan->tanggal_mulai_sewa,
                        'tanggal_akhir_sewa' => $permintaan->tanggal_akhir_sewa,
                    ]);
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
                
                // Tentukan status: aset sewa = sedang_dipakai, material umum = langsung selesai
                $status = ($tipeKeluar === 'peminjaman') ? 'sedang_dipakai' : 'selesai';
                $tanggalSelesai = ($tipeKeluar === 'permintaan') ? now() : null;
                
                // Update stock (kurangi stok) - HARUS DILAKUKAN SEBELUM create OutgoingTransaction
                // untuk memastikan stok berkurang dulu sebelum transaksi dicatat
                Stock::where('idbarang', $permintaan->idbarang)
                    ->decrement('stock', $permintaan->qty);
                
                // Update status kondisi jika aset sewa
                if ($permintaan->stock->kategori === 'barang_sewa' && $tipeKeluar === 'peminjaman') {
                    Stock::where('idbarang', $permintaan->idbarang)->update([
                        'status_kondisi' => 'digunakan',
                        'keterangan_kondisi' => 'Sedang dipinjam oleh ' . ($permintaan->penerima ?? $permintaan->user->name),
                        'tanggal_update_kondisi' => now(),
                    ]);
                }
                
                // Create outgoing transaction (barang keluar) dengan link ke request
                OutgoingTransaction::create([
                    'id_request' => $permintaan->id_request,
                    'idbarang' => $permintaan->idbarang,
                    'tanggal' => now(),
                    'penerima' => $permintaan->penerima ?? $permintaan->user->name,
                    'qty' => $permintaan->qty,
                    'namabarang_k' => $permintaan->stock->namabarang,
                    'kodebarang_k' => $permintaan->stock->kodebarang,
                    'penginput' => $permintaan->user->name,
                    'diproses_oleh' => Auth::user()->name,
                    'kategori' => $permintaan->stock->kategori,
                    'tanggal_mulai_sewa' => $permintaan->tanggal_mulai_sewa,
                    'tanggal_akhir_sewa' => $permintaan->tanggal_akhir_sewa,
                    'tipe_request' => $tipeKeluar,
                    'status' => $status,
                    'tanggal_selesai' => $tanggalSelesai,
                    'status_approval' => 'approved',
                ]);
                
                
                // Update request status:
                // - Aset SEWA: tetap 'approved' karena user perlu tandai selesai nanti
                // - Material UMUM: langsung 'completed' karena sudah selesai (barang habis)
                $finalStatus = ($tipeKeluar === 'peminjaman') ? 'approved' : 'completed';
                
                $permintaan->update([
                    'status' => $finalStatus,
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
        
        if (!$permintaan->canBeRejected()) {
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
        
        // Update status kondisi barang sewa menjadi tersedia
        if ($permintaan->stock->kategori === 'barang_sewa') {
            Stock::where('idbarang', $permintaan->idbarang)->update([
                'status_kondisi' => 'tersedia',
                'keterangan_kondisi' => null,
                'tanggal_update_kondisi' => now(),
            ]);
        }
        
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

        // ❌ PREVENT MANUAL APPROVAL - Harus pakai tombol approve untuk memastikan stok berkurang
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            return back()->with('error', 'Tidak bisa manual ubah ke "Approved"! Gunakan tombol APPROVE untuk memastikan stok dikurangi dengan benar.');
        }

        // Validasi: Jangan ubah status jika sudah processing/completed (stok sudah berkurang)
        if (in_array($oldStatus, ['processing', 'completed', 'approved']) && $newStatus !== $oldStatus) {
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
        
        $permintaan->update([
            'status' => 'completed',
        ]);
        
        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Request berhasil ditandai selesai dan masuk ke history!');
    }
}
