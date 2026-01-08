<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\RackAssignment;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangRakController extends Controller
{
    public function index(Request $request)
    {
        $query = RackAssignment::with('stock')
            ->where('user_id', Auth::id());

        // Filter by rack
        if ($request->has('rack') && $request->rack != '') {
            $query->where('rack', $request->rack);
        }

        // Filter by search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('stock', function($q) use ($search) {
                $q->where('kodebarang', 'like', "%{$search}%")
                  ->orWhere('namabarang', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        $racks = ['1a', '1b', '1c', '2a', '2b', '2c'];

        return view('user.barang-rak.index', compact('assignments', 'racks'));
    }

    public function create()
    {
        $barangs = Stock::orderBy('namabarang')->get();
        $racks = ['1a', '1b', '1c', '2a', '2b', '2c'];
        
        return view('user.barang-rak.create', compact('barangs', 'racks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idbarang' => 'required|exists:stock,idbarang',
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Get stock info
        $stock = Stock::where('idbarang', $request->idbarang)->first();
        
        // Calculate total qty already in racks for this item by this user
        $totalInRacks = RackAssignment::where('user_id', Auth::id())
            ->where('idbarang', $request->idbarang)
            ->sum('qty');
        
        // Check if adding this qty would exceed available stock
        if (($totalInRacks + $request->qty) > $stock->stock) {
            return back()->withInput()->withErrors([
                'qty' => "Jumlah melebihi stok tersedia! Stok: {$stock->stock}, Sudah di rak: {$totalInRacks}, Tersisa: " . ($stock->stock - $totalInRacks)
            ]);
        }

        RackAssignment::create([
            'user_id' => Auth::id(),
            'idbarang' => $request->idbarang,
            'rack' => $request->rack,
            'qty' => $request->qty,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('user.barang-rak.index')->with('success', 'Barang berhasil ditambahkan ke rak!');
    }

    public function edit($id)
    {
        $assignment = RackAssignment::where('user_id', Auth::id())->findOrFail($id);
        $barangs = Stock::orderBy('namabarang')->get();
        $racks = ['1a', '1b', '1c', '2a', '2b', '2c'];
        
        // Calculate qty in other racks (excluding current assignment)
        $qtyInOtherRacks = RackAssignment::where('user_id', Auth::id())
            ->where('idbarang', $assignment->idbarang)
            ->where('id', '!=', $id)
            ->sum('qty');
        
        // Calculate available stock (total stock - qty in other racks)
        $availableStock = $assignment->stock->stock - $qtyInOtherRacks;
        
        return view('user.barang-rak.edit', compact('assignment', 'barangs', 'racks', 'qtyInOtherRacks', 'availableStock'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rack' => 'required|in:1a,1b,1c,2a,2b,2c',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $assignment = RackAssignment::where('user_id', Auth::id())->findOrFail($id);
        
        // Calculate qty in other racks (excluding current assignment)
        $qtyInOtherRacks = RackAssignment::where('user_id', Auth::id())
            ->where('idbarang', $assignment->idbarang)
            ->where('id', '!=', $id)
            ->sum('qty');
        
        // Get stock info
        $stock = $assignment->stock;
        
        // Check if new qty would exceed available stock
        if (($qtyInOtherRacks + $request->qty) > $stock->stock) {
            $availableStock = $stock->stock - $qtyInOtherRacks;
            return back()->withInput()->withErrors([
                'qty' => "Jumlah melebihi stok tersedia! Stok total: {$stock->stock}, Di rak lain: {$qtyInOtherRacks}, Tersedia untuk rak ini: {$availableStock}"
            ]);
        }
        
        $assignment->update([
            'rack' => $request->rack,
            'qty' => $request->qty,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('user.barang-rak.index')->with('success', 'Barang di rak berhasil diupdate!');
    }

    public function destroy($id)
    {
        $assignment = RackAssignment::where('user_id', Auth::id())->findOrFail($id);
        $assignment->delete();

        return redirect()->route('user.barang-rak.index')->with('success', 'Barang berhasil dihapus dari rak!');
    }

    /**
     * Check stock availability for an item
     */
    public function checkStock($idbarang)
    {
        $qtyInRacks = RackAssignment::where('user_id', Auth::id())
            ->where('idbarang', $idbarang)
            ->sum('qty');
        
        return response()->json([
            'qtyInRacks' => $qtyInRacks
        ]);
    }
}
