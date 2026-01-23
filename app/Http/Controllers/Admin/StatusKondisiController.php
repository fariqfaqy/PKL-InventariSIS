<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusKondisiController extends Controller
{
    /**
     * Update status kondisi barang (Aset Sewa only)
     * Logic:
     * - "digunakan" → barang assigned ke user, stock berkurang
     * - "rusak/diperbaiki" → barang ditarik dari user untuk maintenance, stock kembali
     */
    public function update(Request $request, $idbarang)
    {
        $validated = $request->validate([
            'status_kondisi' => 'required|in:digunakan,diperbaiki,rusak',
            'keterangan_kondisi' => 'nullable|string|max:500',
        ], [
            'status_kondisi.required' => 'Status kondisi harus dipilih',
            'status_kondisi.in' => 'Status kondisi tidak valid',
            'keterangan_kondisi.max' => 'Keterangan maksimal 500 karakter',
        ]);

        $barang = Stock::where('idbarang', $idbarang)
            ->where('kategori', 'barang_sewa') // Ensure it's Aset Sewa
            ->firstOrFail();

        $statusLama = $barang->status_kondisi;
        $statusBaru = $validated['status_kondisi'];

        // Skip if status tidak berubah
        if ($statusLama === $statusBaru) {
            return redirect()->back()->with('info', 'Status kondisi tidak berubah');
        }

        DB::beginTransaction();
        try {
            // Get active outgoing transaction for this item
            $activeTransaction = OutgoingTransaction::where('kodebarang_k', $barang->kodebarang)
                ->where('kategori', 'barang_sewa')
                ->whereIn('status', ['sedang_dipakai', 'ditarik'])
                ->latest()
                ->first();

            // CASE 1: Status berubah DARI digunakan KE rusak/diperbaiki
            // → Tarik barang dari user untuk maintenance (stock tetap 1, barang masih ada)
            if ($statusLama === 'digunakan' && in_array($statusBaru, ['rusak', 'diperbaiki'])) {
                if ($activeTransaction) {
                    // Mark transaction as "ditarik" (pulled back)
                    $activeTransaction->status = 'ditarik';
                    $activeTransaction->tanggal_selesai = now();
                    $activeTransaction->save();
                }

                // Stock TIDAK berubah (barang fisik masih ada, hanya kondisi berubah)
                
                Log::info("Aset sewa ditarik untuk maintenance", [
                    'kode' => $barang->kodebarang,
                    'status_baru' => $statusBaru,
                    'stock' => $barang->stock // Stock tetap
                ]);
            }
            
            // CASE 2: Status berubah DARI rusak/diperbaiki KE digunakan
            // → Re-assign barang ke user (jika ada user sebelumnya), stock tetap
            elseif (in_array($statusLama, ['rusak', 'diperbaiki']) && $statusBaru === 'digunakan') {
                // Cek apakah ada user yang sebelumnya assigned
                $lastTransaction = OutgoingTransaction::where('kodebarang_k', $barang->kodebarang)
                    ->where('kategori', 'barang_sewa')
                    ->where('status', 'ditarik')
                    ->latest()
                    ->first();

                if ($lastTransaction && $lastTransaction->penerima) {
                    // Re-assign ke user yang sama
                    OutgoingTransaction::create([
                        'kodebarang_k' => $barang->kodebarang,
                        'namabarang_k' => $barang->namabarang,
                        'qty' => 1,
                        'kategori' => 'barang_sewa',
                        'tipe_keluar' => 'peminjaman',
                        'tanggal' => now(),
                        'penerima' => $lastTransaction->penerima,
                        'penginput' => $lastTransaction->penginput,
                        'diproses_oleh' => Auth::user()->name,
                        'status' => 'sedang_dipakai',
                        'tanggal_kembali' => $lastTransaction->tanggal_kembali, // Keep same return date if any
                    ]);

                    // Stock TIDAK berubah (barang fisik masih ada)

                    Log::info("Aset sewa re-assigned setelah maintenance", [
                        'kode' => $barang->kodebarang,
                        'user' => $lastTransaction->penerima,
                        'stock_after' => $barang->stock
                    ]);
                } else {
                    // Tidak ada user sebelumnya, hanya update status (barang siap digunakan)
                    Log::info("Aset sewa ready for assignment setelah maintenance", [
                        'kode' => $barang->kodebarang,
                        'status_baru' => $statusBaru
                    ]);
                }
            }

            // Update status kondisi di stock
            $barang->status_kondisi = $statusBaru;
            $barang->keterangan_kondisi = $validated['keterangan_kondisi'] ?? null;
            $barang->tanggal_update_kondisi = now();
            $barang->save();

            DB::commit();

            return redirect()->back()->with('success', 'Status kondisi berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating status kondisi", [
                'error' => $e->getMessage(),
                'kode' => $barang->kodebarang
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui status kondisi: ' . $e->getMessage());
        }
    }

    /**
     * Modal form untuk update status kondisi
     */
    public function edit($idbarang)
    {
        $barang = Stock::findOrFail($idbarang);
        return view('admin.status-kondisi.edit', compact('barang'));
    }
}
