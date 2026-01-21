<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusKondisiController extends Controller
{
    /**
     * Update status kondisi barang
     */
    public function update(Request $request, $idbarang)
    {
        $validated = $request->validate([
            'status_kondisi' => 'required|in:tersedia,digunakan,diperbaiki,rusak',
            'keterangan_kondisi' => 'nullable|string|max:500',
        ], [
            'status_kondisi.required' => 'Status kondisi harus dipilih',
            'status_kondisi.in' => 'Status kondisi tidak valid',
            'keterangan_kondisi.max' => 'Keterangan maksimal 500 karakter',
        ]);

        $barang = Stock::findOrFail($idbarang);
        $statusLama = $barang->status_kondisi;

        // Update status kondisi
        $barang->status_kondisi = $validated['status_kondisi'];
        $barang->keterangan_kondisi = $validated['keterangan_kondisi'] ?? null;
        $barang->tanggal_update_kondisi = now();
        $barang->save();

        return redirect()->back()->with('success', 'Status kondisi berhasil diperbarui!');
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
