<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\RackAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $q->where('kodebarang', 'like', "%{$search}%")
                  ->orWhere('namabarang', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan rak (dari rack_assignments user)
        if ($request->has('rack') && $request->rack != '') {
            $query->whereHas('rackAssignments', function($q) use ($request) {
                $q->where('user_id', Auth::id())
                  ->where('rack', $request->rack);
            });
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
        
        // Get racks from user's rack assignments
        $racks = RackAssignment::where('user_id', Auth::id())
            ->distinct()
            ->pluck('rack')
            ->sort();

        // Get rack assignments for each stock
        $stockIds = $stocks->pluck('idbarang');
        $rackAssignments = RackAssignment::where('user_id', Auth::id())
            ->whereIn('idbarang', $stockIds)
            ->get()
            ->groupBy('idbarang');

        return view('user.stok-barang.index', compact('stocks', 'racks', 'rackAssignments'));
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
