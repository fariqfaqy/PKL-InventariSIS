<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = $this->getStats();
        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Get dashboard statistics as JSON for AJAX requests.
     */
    public function getStats()
    {
        $totalBarang = Stock::sum('stock');
        $totalUser = User::count();
        $barangMasuk = IncomingTransaction::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->sum('qty');
        $barangKeluar = OutgoingTransaction::whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->sum('qty');

        return [
            'totalBarang' => $totalBarang ?? 0,
            'totalUser' => $totalUser ?? 0,
            'barangMasuk' => $barangMasuk ?? 0,
            'barangKeluar' => $barangKeluar ?? 0,
        ];
    }

    /**
     * Return stats as JSON for AJAX calls.
     */
    public function statsApi()
    {
        return response()->json($this->getStats());
    }
}
