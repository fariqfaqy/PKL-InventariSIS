<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
use App\Models\RequestBarang;
use App\Models\RackAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PemakaianController extends Controller
{
    /**
     * Items per page for pagination
     */
    private const ITEMS_PER_PAGE = 20;

    /**
     * Display a listing of user's pemakaian.
     */
    public function index()
    {
        // Ambil request AKTIF (pending & approved) - untuk tab "Request Saya"
        // Exclude: completed, cancelled, rejected (masuk History)
        // Eager load change requests count untuk prevent N+1 query
        $requests = RequestBarang::with([
                'stock',
                'changeRequests' => function($query) {
                    // Eager load pending change requests untuk cek cancellation
                    $query->where('status', 'pending')->latest();
                }
            ])
            ->withCount([
                'changeRequests as pending_changes_count' => function($query) {
                    $query->where('status', 'pending');
                },
                'changeRequests as approved_changes_count' => function($query) {
                    $query->where('status', 'approved');
                }
            ])
            ->where('user_id', Auth::id())
            ->whereNull('parent_request_id')
            ->whereNotIn('status', ['completed', 'cancelled', 'rejected']) // Tambah rejected
            ->orderBy('tanggal_request', 'desc')
            ->paginate(self::ITEMS_PER_PAGE);

        // Ambil outgoing transactions untuk requests yang approved (untuk mendapatkan data penerima)
        $approvedRequestIds = $requests->where('status', 'approved')->pluck('id_request');
        $outgoingTransactions = OutgoingTransaction::whereIn('id_request', $approvedRequestIds)
            ->get()
            ->keyBy('id_request');

        // Ambil request perubahan (yang punya parent_request_id)
        // Exclude yang parent-nya sudah completed/cancelled
        $changeRequests = RequestBarang::with(['stock', 'parentRequest'])
            ->where('user_id', Auth::id())
            ->whereNotNull('parent_request_id')
            ->whereHas('parentRequest', function($query) {
                $query->whereNotIn('status', ['completed', 'cancelled']);
            })
            ->orderBy('tanggal_request', 'desc')
            ->get();

        // Ambil history pemakaian berdasarkan status
        // 1. Sedang dipakai - aset sewa yang aktif (gunakan user_id, konsisten dengan admin)
        $sedangDipakai = OutgoingTransaction::with('stock')
            ->where('user_id', Auth::id()) // Filter by user_id
            ->where('status', 'sedang_dipakai')
            ->where('kategori', 'aset_sewa') // Hanya aset sewa untuk history ini
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // 2. Selesai - aset sewa yang sudah dikembalikan
        $selesai = OutgoingTransaction::with('stock')
            ->where('user_id', Auth::id()) // Filter by user_id
            ->where('status', 'selesai')
            ->where('kategori', 'aset_sewa') // Hanya aset sewa
            ->orderBy('tanggal_selesai', 'desc')
            ->get();
        
        // 3. Ditolak/Dibatalkan - request yang rejected atau cancelled
        $ditolakDibatalkan = RequestBarang::with('stock')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['rejected', 'cancelled'])
            ->orderBy('tanggal_request', 'desc')
            ->get();
        
        // 4. Aset Sewa yang di-assign admin ke user ini
        // Sudah tercakup dalam $sedangDipakai dan $selesai (karena pakai user_id filter)
        // Tidak perlu query terpisah lagi

        // Hitung notifikasi untuk badge (hanya yang butuh action/attention)
        // - Pending requests: perlu menunggu admin approve
        // - Approved requests: barang siap diambil/dipakai
        // - Pending change requests: menunggu admin approve perubahan
        // - Sedang dipakai: reminder barang yang masih dipinjam (TIDAK dihitung sebagai notif)
        // - Selesai: history (TIDAK dihitung sebagai notif)
        $requestNotifCount = $requests->where('status', 'pending')->count() + 
                            $requests->where('status', 'approved')->count();
        $changeRequestNotifCount = $changeRequests->where('status', 'pending')->count();
        $historyNotifCount = 0; // History tidak dihitung sebagai notif
        
        // Total notifikasi untuk badge sidebar (hanya yang perlu action)
        $userNotificationCount = $requestNotifCount + $changeRequestNotifCount;

        return view('user.pemakaian.index', compact(
            'requests', 
            'changeRequests', 
            'sedangDipakai', 
            'selesai', 
            'ditolakDibatalkan',
            'outgoingTransactions',
            'requestNotifCount',
            'changeRequestNotifCount',
            'historyNotifCount',
            'userNotificationCount'
        ));
    }

    /**
     * Show the form for creating a new pemakaian.
     * 
     * Pegawai hanya bisa request Material Umum (kategori: habis_pakai)
     * dengan 2 sub-kategori:
     * - barang_habis_pakai (permintaan - tidak dikembalikan)
     * - barang_pinjam (peminjaman - harus dikembalikan)
     */
    public function create()
    {
        // Pegawai hanya bisa request Material Umum (material_umum)
        // Pisahkan berdasarkan sub_kategori
        $barangHabisPakai = Stock::where('kategori', 'material_umum')
            ->where('sub_kategori', 'barang_habis_pakai')
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();
            
        $barangPinjam = Stock::where('kategori', 'material_umum')
            ->where('sub_kategori', 'barang_pinjam')
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();

        return view('user.pemakaian.create', compact('barangHabisPakai', 'barangPinjam'));
    }

    /**
     * Store a newly created pemakaian in storage.
     * 
     * Validasi ketat:
     * - Pegawai hanya bisa request Material Umum (kategori: habis_pakai)
     * - Sub-kategori: barang_habis_pakai atau barang_pinjam
     * - Barang pinjam butuh tanggal pinjam & kembali
     * - Material umum tidak perlu tanggal
     */
    public function store(Request $request)
    {
        // Debug log
        Log::info('Pemakaian Store Request', $request->all());

        $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'sub_kategori' => 'required|in:barang_habis_pakai,barang_pinjam',
            'qty' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'tanggal_pinjam' => 'nullable|required_if:sub_kategori,barang_pinjam|date',
            'tanggal_kembali' => 'nullable|required_if:sub_kategori,barang_pinjam|date|after:tanggal_pinjam',
            'catatan_user' => 'nullable|string',
        ], [
            'idbarang.required' => 'Barang harus dipilih',
            'idbarang.exists' => 'Barang yang dipilih tidak valid',
            'sub_kategori.required' => 'Sub-kategori harus dipilih',
            'sub_kategori.in' => 'Sub-kategori tidak valid',
            'qty.required' => 'Jumlah harus diisi',
            'qty.min' => 'Jumlah minimal 1',
            'tanggal_pinjam.required_if' => 'Tanggal pinjam wajib diisi untuk barang pinjam',
            'tanggal_kembali.required_if' => 'Tanggal kembali wajib diisi untuk barang pinjam',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam',
            'keperluan.required' => 'Keperluan wajib diisi',
        ]);

        DB::beginTransaction();
        try {
            $stock = Stock::findOrFail($request->idbarang);

            // VALIDASI KETAT: Pegawai hanya bisa request Material Umum (material_umum)
            if ($stock->kategori !== 'material_umum') {
                Log::warning('Kategori barang tidak diizinkan untuk pegawai', [
                    'kategori' => $stock->kategori,
                    'user' => Auth::user()->name
                ]);
                return back()->withInput()->with('error', 'Pegawai hanya dapat request Material Umum!');
            }

            // Validasi sub_kategori harus sesuai dengan barang yang dipilih
            if ($stock->sub_kategori !== $request->sub_kategori) {
                Log::warning('Sub-kategori request tidak sesuai dengan barang', [
                    'request_sub_kategori' => $request->sub_kategori,
                    'stock_sub_kategori' => $stock->sub_kategori
                ]);
                return back()->withInput()->with('error', 'Sub-kategori barang tidak sesuai!');
            }

            // Validasi stok mencukupi
            if ($stock->stock < $request->qty) {
                Log::warning('Stok tidak mencukupi', [
                    'requested_qty' => $request->qty,
                    'available_stock' => $stock->stock
                ]);
                return back()->withInput()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock . ' unit');
            }

            // Tentukan tipe_request untuk RequestBarang
            // barang_habis_pakai -> pakai_habis_pakai (permintaan)
            // barang_pinjam -> pinjam_material (peminjaman)
            $tipeRequestBarang = $request->sub_kategori === 'barang_habis_pakai' 
                ? 'pakai_habis_pakai' 
                : 'pinjam_material';

            // Create RequestBarang untuk approval admin
            $requestBarang = RequestBarang::create([
                'user_id' => Auth::id(),
                'idbarang' => $request->idbarang,
                'qty' => $request->qty,
                'tipe_request' => $tipeRequestBarang,
                'keperluan' => $request->keperluan,
                'penerima' => Auth::user()->name, // Auto-detect from logged in user
                'catatan_user' => $request->catatan_user ?? null,
                'tanggal_mulai_sewa' => $request->sub_kategori === 'barang_pinjam' ? $request->tanggal_pinjam : null,
                'tanggal_akhir_sewa' => $request->sub_kategori === 'barang_pinjam' ? $request->tanggal_kembali : null,
                'status' => 'pending',
                'tanggal_request' => now(),
            ]);

            Log::info('RequestBarang created successfully', [
                'id' => $requestBarang->id_request,
                'sub_kategori' => $request->sub_kategori,
                'tipe_request' => $tipeRequestBarang
            ]);

            DB::commit();
            
            $message = $request->sub_kategori === 'barang_habis_pakai' 
                ? 'Permintaan material umum berhasil diajukan! Menunggu persetujuan admin.' 
                : 'Peminjaman barang berhasil diajukan! Menunggu persetujuan admin.';
                
            return redirect()->route('user.pemakaian.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Gagal mengajukan request: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified pemakaian.
     * DISABLED: User tidak boleh edit transaksi yang sudah diapprove admin
     * Hanya bisa batalkan RequestBarang yang masih pending
     */
    public function edit($id)
    {
        return redirect()->route('user.pemakaian.index')
            ->with('error', 'Tidak dapat mengedit transaksi yang sudah diproses. Silakan ajukan request baru jika diperlukan.');
    }

    /**
     * Display the specified request detail
     */
    public function show($id)
    {
        $request = RequestBarang::with([
                'stock',
                'user',
                'changeRequests' => function($query) {
                    $query->latest();
                }
            ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Get related outgoing transaction if exists
        $outgoingTransaction = null;
        if ($request->status === 'approved') {
            $outgoingTransaction = OutgoingTransaction::where('id_request', $id)->first();
        }

        return view('user.pemakaian.show', compact('request', 'outgoingTransaction'));
    }

    /**
     * Update the specified pemakaian in storage.
     * DISABLED: User tidak boleh edit transaksi yang sudah diapprove admin
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('user.pemakaian.index')
            ->with('error', 'Tidak dapat mengedit transaksi yang sudah diproses. Silakan ajukan request baru jika diperlukan.');
    }

    /**
     * Remove the specified pemakaian from storage.
     * DISABLED: User tidak boleh hapus transaksi yang sudah diapprove admin
     * Hanya bisa batalkan RequestBarang yang masih pending
     */
    public function destroy($id)
    {
        return redirect()->route('user.pemakaian.index')
            ->with('error', 'Tidak dapat menghapus transaksi yang sudah diproses. Hanya admin yang dapat membatalkan transaksi.');
    }

    /**
     * Mark the specified pemakaian as completed.
     */
    public function selesai($id)
    {
        DB::beginTransaction();
        try {
            $pemakaian = OutgoingTransaction::where('penginput', Auth::user()->name)
                ->where('status', 'sedang_dipakai')
                ->findOrFail($id);

            // Update status dan tanggal selesai
            // Stok TIDAK dikembalikan, tetap berkurang dan barang tetap di tabel keluar
            $pemakaian->update([
                'status' => 'selesai',
                'tanggal_selesai' => now(),
            ]);

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Pemakaian barang telah selesai!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan pemakaian: ' . $e->getMessage());
        }
    }
}
