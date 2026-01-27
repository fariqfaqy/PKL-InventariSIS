<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = $this->getStats();
        
        // Get additional data for dashboard
        $recentIncoming = IncomingTransaction::with('stock')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentOutgoing = OutgoingTransaction::with('stock')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentActivities = ActivityLog::orderBy('date', 'desc')
            ->limit(8)
            ->get();
            
        // Low stock items
        $lowStockItems = Stock::where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
            
        // Top items by stock
        $topItems = Stock::orderBy('stock', 'desc')
            ->limit(5)
            ->get();
            
        // Chart data - Last 7 days transactions
        $chartData = $this->getChartData();
        
        // Category stats
        $categoryStats = [
            'aset_sewa' => Stock::where('kategori', 'aset_sewa')->sum('stock'),
            'material_umum' => Stock::where('kategori', 'material_umum')->sum('stock'),
            'aset_tetap' => Stock::where('kategori', 'aset_tetap')->sum('stock'),
        ];
        
        // Stock status distribution
        $stockDistribution = [
            'normal' => Stock::where('stock', '>', 10)->count(),
            'low' => Stock::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'out' => Stock::where('stock', 0)->count(),
        ];
        
        // Monthly comparison
        $currentMonth = now();
        $lastMonth = now()->subMonth();
        
        $monthlyComparison = [
            'current' => [
                'incoming' => IncomingTransaction::whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)->sum('qty'),
                'outgoing' => OutgoingTransaction::whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)->sum('qty'),
            ],
            'last' => [
                'incoming' => IncomingTransaction::whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year)->sum('qty'),
                'outgoing' => OutgoingTransaction::whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year)->sum('qty'),
            ]
        ];
        
        return view('admin.dashboard', compact(
            'stats', 
            'recentIncoming', 
            'recentOutgoing', 
            'recentActivities',
            'lowStockItems',
            'topItems',
            'chartData',
            'categoryStats',
            'stockDistribution',
            'monthlyComparison'
        ));
    }

    /**
     * Get dashboard statistics as JSON for AJAX requests.
     */
    public function getStats()
    {
        $totalBarang = Stock::sum('stock');
        $totalUser = User::count();
        $totalJenisBarang = Stock::count();
        
        $barangMasuk = IncomingTransaction::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->sum('qty');
        $barangKeluar = OutgoingTransaction::whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->sum('qty');
                                          
        $barangMasukHariIni = IncomingTransaction::whereDate('created_at', today())->sum('qty');
        $barangKeluarHariIni = OutgoingTransaction::whereDate('created_at', today())->sum('qty');
        
        $lowStock = Stock::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $outOfStock = Stock::where('stock', 0)->count();

        return [
            'totalBarang' => $totalBarang ?? 0,
            'totalUser' => $totalUser ?? 0,
            'totalJenisBarang' => $totalJenisBarang ?? 0,
            'barangMasuk' => $barangMasuk ?? 0,
            'barangKeluar' => $barangKeluar ?? 0,
            'barangMasukHariIni' => $barangMasukHariIni ?? 0,
            'barangKeluarHariIni' => $barangKeluarHariIni ?? 0,
            'lowStock' => $lowStock ?? 0,
            'outOfStock' => $outOfStock ?? 0,
        ];
    }
    
    /**
     * Get chart data for last 7 days
     */
    private function getChartData()
    {
        $days = [];
        $incoming = [];
        $outgoing = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('d M');
            
            $incoming[] = IncomingTransaction::whereDate('created_at', $date)->sum('qty');
            $outgoing[] = OutgoingTransaction::whereDate('created_at', $date)->sum('qty');
        }
        
        return [
            'labels' => $days,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
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
