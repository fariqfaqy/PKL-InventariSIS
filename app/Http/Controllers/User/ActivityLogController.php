<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // User hanya bisa melihat log aktivitasnya sendiri
        $query = ActivityLog::query()
            ->where('usr', Auth::user()->name)
            ->orderBy('date', 'desc');

        // Filter berdasarkan method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter berdasarkan endpoint
        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'like', '%' . $request->endpoint . '%');
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

        // Statistics
        $stats = [
            'today' => ActivityLog::where('usr', Auth::user()->name)->whereDate('date', today())->count(),
            'week' => ActivityLog::where('usr', Auth::user()->name)->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('user.activity-log.index', compact('logs', 'stats'));
    }
}
