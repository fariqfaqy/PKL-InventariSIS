<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->orderBy('date', 'desc');

        // Filter berdasarkan user
        if ($request->filled('usr')) {
            $query->where('usr', 'ILIKE', "%{$request->usr}%");
        }

        // Filter berdasarkan method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter berdasarkan endpoint
        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'ILIKE', "%{$request->endpoint}%");
        }

        // Filter berdasarkan status code
        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Statistics - hanya hitung sekali
        $stats = [
            'success' => ActivityLog::where('status_code', 'like', '2%')->count(),
            'client_error' => ActivityLog::where('status_code', 'like', '4%')->count(),
            'server_error' => ActivityLog::where('status_code', 'like', '5%')->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'stats'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::query()->orderBy('date', 'desc');

        // Apply same filters
        if ($request->filled('usr')) {
            $query->where('usr', 'ILIKE', "%{$request->usr}%");
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'ILIKE', "%{$request->endpoint}%");
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'activity-log-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['ID', 'Tanggal', 'User', 'Method', 'Endpoint', 'Status Code']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->idlog,
                    $log->date->format('Y-m-d H:i:s'),
                    $log->usr,
                    $log->method,
                    $log->endpoint,
                    $log->status_code,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export activity logs to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = ActivityLog::query()->orderBy('date', 'desc');

        // Apply same filters
        if ($request->filled('usr')) {
            $query->where('usr', 'ILIKE', "%{$request->usr}%");
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'ILIKE', "%{$request->endpoint}%");
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $logs = $query->get();
        
        $pdf = Pdf::loadView('admin.pdf.activity-log', compact('logs'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Activity_Log_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
