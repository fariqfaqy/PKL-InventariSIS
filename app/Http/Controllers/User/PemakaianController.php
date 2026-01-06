<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
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
        $barangs = Stock::where('stock', '>', 0)->orderBy('namabarang')->get();
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
        ]);

        DB::beginTransaction();
        try {
            $stock = Stock::findOrFail($request->idbarang);

            // Validasi stok mencukupi
            if ($stock->stock < $request->qty) {
                return back()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $stock->stock . ' unit');
            }

            // Create transaksi keluar
            OutgoingTransaction::create([
                'idbarang' => $request->idbarang,
                'qty' => $request->qty,
                'penerima' => $request->penerima,
                'namabarang_k' => $stock->namabarang,
                'kodebarang_k' => $stock->kodebarang,
                'penginput' => Auth::user()->email,
            ]);

            // Update stok
            $stock->decrement('stock', $request->qty);

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Pemakaian barang berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pemakaian: ' . $e->getMessage());
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
            } else if ($diff < 0) {
                $stock->increment('stock', abs($diff));
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

            // Hapus pemakaian
            $pemakaian->delete();

            DB::commit();
            return redirect()->route('user.pemakaian.index')->with('success', 'Pemakaian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pemakaian: ' . $e->getMessage());
        }
    }
}
