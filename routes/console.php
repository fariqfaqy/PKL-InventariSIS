<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule auto-complete untuk peminjaman yang sudah expired
 * Jalan setiap hari jam 00:05 (5 menit setelah midnight)
 */
Schedule::command('rentals:auto-complete')
    ->dailyAt('00:05')
    ->appendOutputTo(storage_path('logs/auto-complete-rentals.log'));
