<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StokBarangController extends Controller
{
    /**
     * Display a listing of stock (read-only for user).
     */
    public function index(Request $request)
    {
        $query = Stock::query();

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_equipment', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('rack', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan rak
        if ($request->has('rack') && $request->rack != '') {
            $query->where('rack', $request->rack);
        }

        // Filter berdasarkan status stok
        if ($request->has('status') && $request->status != '') {
            switch ($request->status) {
                case 'kritis':
                    $query->where('stock', '<=', 4);
                    break;
                case 'menengah':
                    $query->whereBetween('stock', [5, 9]);
                    break;
                case 'aman':
                    $query->where('stock', '>=', 10);
                    break;
            }
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(15);
        $racks = Stock::distinct()->pluck('rack');

        return view('user.stok-barang.index', compact('stocks', 'racks'));
    }

    /**
     * Display the specified stock detail.
     */
    public function show($id)
    {
        $stock = Stock::findOrFail($id);
        return view('user.stok-barang.show', compact('stock'));
    }
}
