<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share notification counts untuk user sidebar
        view()->composer('layouts.user', function ($view) {
            if (auth()->check() && auth()->user()->role === 'user') {
                // Hitung notifikasi untuk badge sidebar
                // Tab "Request Saya" - request yang pending atau approved
                $requestCount = \App\Models\RequestBarang::where('user_id', auth()->id())
                    ->whereNull('parent_request_id')
                    ->whereIn('status', ['pending', 'approved'])
                    ->count();

                // Tab "Request Perubahan" - request perubahan yang pending
                $changeRequestCount = \App\Models\RequestBarang::where('user_id', auth()->id())
                    ->whereNotNull('parent_request_id')
                    ->where('status', 'pending')
                    ->whereHas('parentRequest', function($query) {
                        $query->whereNotIn('status', ['completed', 'cancelled']);
                    })
                    ->count();

                // History tidak dihitung sebagai notifikasi
                // User hanya perlu notif untuk pending/approved requests, bukan history
                
                // Total notifikasi untuk badge sidebar (hanya actionable items)
                $userNotificationCount = $requestCount + $changeRequestCount;

                $view->with('userNotificationCount', $userNotificationCount);
            }
        });
    }
}
