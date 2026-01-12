<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
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
        $pemakaian = OutgoingTransaction::with('stock')
            ->where('penginput', Auth::user()->email)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('user.pemakaian.index', compact('pemakaian'));
    }

    /**
     * Show the form for creating a new pemakaian.
     */
    public function create()
    {
        // Tampilkan semua barang yang ada stoknya
        $barangs = Stock::where('stock', '>', 0)
            ->orderBy('namabarang')
            ->get();

        return view('user.pemakaian.create', compact('barangs'));
    }

    /**
     * Store a newly created pemakaian in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'qty' => 'required|integer|min:1',
            'penerima' => 'required|string|max:255',
            'tipe_request' => 'required|in:peminjaman,permintaan',
            'tanggal_pinjam' => 'required_if:tipe_request,peminjaman|date|after_or_equal:today',
            'tanggal_kembali' => 'required_if:tipe_request,peminjaman|date|after:tanggal_pinjam',
        ], [
            'tanggal_pinjam.required_if' => 'Tanggal pinjam wajib diisi untuk peminjaman barang sewa',
            'tanggal_kembali.required_if' => 'Tanggal kembali wajib diisi untuk peminjaman barang sewa',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam',
        ]);

        DB::beginTransaction();
        try {
            $stock = Stock::findOrFail($request->idbarang);

            // Validasi tipe request sesuai dengan kategori barang
            if ($request->tipe_request == 'peminjaman' && $stock->kategori != 'barang_sewa') {
                return back()->withInput()->with('error', 'Peminjaman hanya untuk barang sewa!');
            }
            
            if ($request->tipe_request == 'permintaan' && $stock->kategori != 'habis_pakai') {
                return back()->withInput()->with('error', 'Permintaan hanya untuk barang habis pakai!');
            }

            // Validasi stok mencukupi
            if ($stock->stock < $request->qty) {
                return back()->withInput()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock . ' unit');
            }

            // Create transaksi keluar dengan status pending (menunggu approval admin)
            OutgoingTransaction::create([
                'idbarang' => $request->idbarang,
                'qty' => $request->qty,
                'penerima' => $request->penerima,
                'namabarang_k' => $stock->namabarang,
                'kodebarang_k' => $stock->kodebarang,
                'penginput' => Auth::user()->email,
                'kategori' => $stock->kategori,
                'durasi_sewa' => $stock->durasi_sewa,
                'tipe_request' => $request->tipe_request,
                'tanggal_pinjam' => $request->tipe_request == 'peminjaman' ? $request->tanggal_pinjam : null,
                'tanggal_kembali' => $request->tipe_request == 'peminjaman' ? $request->tanggal_kembali : null,
                'status_approval' => 'pending',
            ]);

            // TIDAK update stok dulu, tunggu admin approve

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Request pemakaian berhasil diajukan! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan request: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified pemakaian.
     */
    public function edit($id)
    {
        $pemakaian = OutgoingTransaction::where('penginput', Auth::user()->email)
            ->findOrFail($id);
        $barangs = Stock::orderBy('namabarang')->get();
        
        return view('user.pemakaian.edit', compact('pemakaian', 'barangs'));
    }

    /**
     * Update the specified pemakaian in storage.
     */
    public function update(Request $request, $id)
    {
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
    }

    /**
     * Remove the specified pemakaian from storage.
     */
    public function destroy($id)
    {
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
