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

class PemakaianController extends Controller
{
    /**
     * Display a listing of user's pemakaian.
     */
    public function index()
    {
        // Ambil request biasa (bukan request perubahan, bukan completed, bukan cancelled)
        $requests = RequestBarang::with('stock')
            ->where('user_id', Auth::id())
            ->whereNull('parent_request_id')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('tanggal_request', 'desc')
            ->paginate(20);

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

        // Ambil barang keluar yang sudah disetujui dan request yang completed (untuk history)
        $pemakaian = OutgoingTransaction::with('stock')
            ->where('penginput', Auth::user()->name)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);
        
        // Ambil request completed dan cancelled untuk ditampilkan di history
        $completedRequests = RequestBarang::with('stock')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('tanggal_request', 'desc')
            ->get();

        return view('user.pemakaian.index', compact('requests', 'changeRequests', 'pemakaian', 'completedRequests'));
    }

    /**
     * Show the form for creating a new pemakaian.
     */
    public function create()
    {
        // Tampilkan hanya barang sewa dan habis pakai yang ada stoknya (exclude aset tetap)
        $barangs = Stock::whereIn('kategori', ['barang_sewa', 'habis_pakai'])
            ->where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();

        return view('user.pemakaian.create', compact('barangs'));
    }

    /**
     * Store a newly created pemakaian in storage.
     */
    public function store(Request $request)
    {
        // Debug log
        \Log::info('Pemakaian Store Request', $request->all());

        $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'qty' => 'required|integer|min:1',
            'keperluan' => 'required|string',
            'tipe_request' => 'required|in:peminjaman,permintaan',
            'tanggal_pinjam' => 'nullable|required_if:tipe_request,peminjaman|date|after_or_equal:today',
            'tanggal_kembali' => 'nullable|required_if:tipe_request,peminjaman|date|after:tanggal_pinjam',
            'catatan_user' => 'nullable|string',
        ], [
            'idbarang.required' => 'Barang harus dipilih',
            'idbarang.exists' => 'Barang yang dipilih tidak valid',
            'qty.required' => 'Jumlah harus diisi',
            'qty.min' => 'Jumlah minimal 1',
            'tipe_request.required' => 'Tipe request harus dipilih',
            'tipe_request.in' => 'Tipe request tidak valid',
            'tanggal_pinjam.required_if' => 'Tanggal pinjam wajib diisi untuk peminjaman barang sewa',
            'tanggal_kembali.required_if' => 'Tanggal kembali wajib diisi untuk peminjaman barang sewa',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam',
            'keperluan.required' => 'Keperluan wajib diisi',
        ]);

        DB::beginTransaction();
        try {
            $stock = Stock::findOrFail($request->idbarang);

            // Validasi tipe request sesuai dengan kategori barang
            if ($request->tipe_request == 'peminjaman' && $stock->kategori != 'barang_sewa') {
                \Log::warning('Tipe request tidak sesuai kategori', [
                    'tipe_request' => $request->tipe_request,
                    'kategori' => $stock->kategori
                ]);
                return back()->withInput()->with('error', 'Peminjaman hanya untuk barang sewa!');
            }
            
            if ($request->tipe_request == 'permintaan' && $stock->kategori != 'habis_pakai') {
                \Log::warning('Tipe request tidak sesuai kategori', [
                    'tipe_request' => $request->tipe_request,
                    'kategori' => $stock->kategori
                ]);
                return back()->withInput()->with('error', 'Permintaan hanya untuk barang habis pakai!');
            }

            // Validasi stok mencukupi
            if ($stock->stock < $request->qty) {
                \Log::warning('Stok tidak mencukupi', [
                    'requested_qty' => $request->qty,
                    'available_stock' => $stock->stock
                ]);
                return back()->withInput()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock . ' unit');
            }

            // Determine tipe_request untuk RequestBarang
            $tipeRequestBarang = $request->tipe_request == 'peminjaman' ? 'pinjam_sewa' : 'pakai_habis_pakai';

            // Create RequestBarang untuk approval admin
            $requestBarang = RequestBarang::create([
                'user_id' => Auth::id(),
                'idbarang' => $request->idbarang,
                'qty' => $request->qty,
                'tipe_request' => $tipeRequestBarang,
                'keperluan' => $request->keperluan,
                'catatan_user' => $request->catatan_user ?? null,
                'tanggal_mulai_sewa' => $request->tipe_request == 'peminjaman' ? $request->tanggal_pinjam : null,
                'tanggal_akhir_sewa' => $request->tipe_request == 'peminjaman' ? $request->tanggal_kembali : null,
                'status' => 'pending',
                'tanggal_request' => now(),
            ]);

            \Log::info('RequestBarang created successfully', ['id' => $requestBarang->id_request]);

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Request berhasil diajukan! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating request', [
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
     * Update the specified pemakaian in storage.
     * DISABLED: User tidak boleh edit transaksi yang sudah diapprove admin
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('user.pemakaian.index')
            ->with('error', 'Tidak dapat mengedit transaksi yang sudah diproses. Silakan ajukan request baru jika diperlukan.');
        
        /* COMMENTED OUT - User should not modify approved transactions
        $request->validate([
            'qty' => 'required|integer|min:1',
            'penerima' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pemakaian = OutgoingTransaction::where('penginput', Auth::user()->email)
                ->findOrFail($id);
            $stock = Stock::findOrFail($pemakaian->idbarang);

            $oldQty = $pemakaian->qty;
            $newQty = $request->qty;
            $diff = $newQty - $oldQty;

            // Jika qty baru > qty lama, cek stok
            if ($diff > 0 && $stock->stock < $diff) {
                return back()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock . ' unit');
            }

            // Update stok
            if ($diff > 0) {
                $stock->decrement('stock', $diff);
                
                // Kurangi stok di rak juga
                $rackAssignments = RackAssignment::where('user_id', Auth::id())
                    ->where('idbarang', $pemakaian->idbarang)
                    ->where('qty', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingQty = $diff;
                foreach ($rackAssignments as $rackAssignment) {
                    if ($remainingQty <= 0) break;

                    if ($rackAssignment->qty >= $remainingQty) {
                        $rackAssignment->decrement('qty', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        $remainingQty -= $rackAssignment->qty;
                        $rackAssignment->update(['qty' => 0]);
                    }
                }
            } else if ($diff < 0) {
                $stock->increment('stock', abs($diff));
                
                // Tambah kembali stok di rak
                $rackAssignment = RackAssignment::where('user_id', Auth::id())
                    ->where('idbarang', $pemakaian->idbarang)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($rackAssignment) {
                    $rackAssignment->increment('qty', abs($diff));
                } else {
                    // Jika tidak ada rack assignment, buat baru dengan rack default
                    RackAssignment::create([
                        'user_id' => Auth::id(),
                        'idbarang' => $pemakaian->idbarang,
                        'rack' => '1a',
                        'qty' => abs($diff),
                    ]);
                }
            }

            // Update pemakaian
            $pemakaian->update([
                'qty' => $newQty,
                'penerima' => $request->penerima,
            ]);

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Pemakaian berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate pemakaian: ' . $e->getMessage());
        }
        */
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
        
        /* COMMENTED OUT - User should not delete approved transactions
        DB::beginTransaction();
        try {
            $pemakaian = OutgoingTransaction::where('penginput', Auth::user()->email)
                ->findOrFail($id);
            $stock = Stock::findOrFail($pemakaian->idbarang);

            // Kembalikan stok
            $stock->increment('stock', $pemakaian->qty);

            // Kembalikan stok di rak juga
            $rackAssignment = RackAssignment::where('user_id', Auth::id())
                ->where('idbarang', $pemakaian->idbarang)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($rackAssignment) {
                $rackAssignment->increment('qty', $pemakaian->qty);
            } else {
                // Jika tidak ada rack assignment, buat baru dengan rack default
                RackAssignment::create([
                    'user_id' => Auth::id(),
                    'idbarang' => $pemakaian->idbarang,
                    'rack' => '1a',
                    'qty' => $pemakaian->qty,
                ]);
            }

            // Hapus pemakaian
            $pemakaian->delete();

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Pemakaian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pemakaian: ' . $e->getMessage());
        }
        */
    }

    /**
     * Mark the specified pemakaian as completed.
     */
    public function selesai($id)
    {
        DB::beginTransaction();
        try {
            $pemakaian = OutgoingTransaction::where('penginput', Auth::user()->email)
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
