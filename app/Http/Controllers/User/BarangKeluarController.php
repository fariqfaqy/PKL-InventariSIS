<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of all outgoing transactions (read-only for user).
     */
    public function index(Request $request)
    {
        $query = OutgoingTransaction::with('stock');

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kodebarang_k', 'like', "%{$search}%")
                  ->orWhere('namabarang_k', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $barangKeluar = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('user.barang-keluar.index', compact('barangKeluar'));
    }

    /**
     * Display the specified outgoing transaction detail.
     */
    public function show($id)
    {
        $barangKeluar = OutgoingTransaction::with('stock')->findOrFail($id);
        return view('user.barang-keluar.show', compact('barangKeluar'));
    }
}
