<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya log request yang authenticated dan bukan ajax/api stats
        if (Auth::check() && 
            !$request->is('admin/dashboard/stats') && 
            !$request->is('user/dashboard/stats') &&
            !$request->ajax()) {
            
            try {
                ActivityLog::create([
                    'date' => now(),
                    'usr' => Auth::user()->name,
                    'method' => $request->method(),
                    'endpoint' => $request->path(),
                    'status_code' => (string) $response->getStatusCode(),
                ]);
            } catch (\Exception $e) {
                // Silent fail - jangan ganggu request user jika logging gagal
            }
        }

        return $response;
    }
}

