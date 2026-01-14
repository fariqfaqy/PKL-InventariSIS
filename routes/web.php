<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BarangMasukController;
use App\Http\Controllers\Admin\BarangKeluarController;
use App\Http\Controllers\Admin\StokBarangController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\ActivityLogController as UserActivityLogController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'statsApi'])->name('dashboard.stats');
    Route::resource('admins', AdminController::class);
    
    // Kelola Barang
    // Export routes MUST be defined BEFORE resource routes
    Route::get('stok-barang/export-pdf', [StokBarangController::class, 'exportPdf'])->name('stok-barang.export-pdf');
    Route::resource('stok-barang', StokBarangController::class)->except(['create', 'store']);
    
    Route::get('barang-masuk/export-pdf', [BarangMasukController::class, 'exportPdf'])->name('barang-masuk.export-pdf');
    Route::get('barang-masuk/check-stock', [BarangMasukController::class, 'checkStock'])->name('barang-masuk.check-stock');
    Route::resource('barang-masuk', BarangMasukController::class);
    
    Route::get('barang-keluar/export-pdf', [BarangKeluarController::class, 'exportPdf'])->name('barang-keluar.export-pdf');
    Route::resource('barang-keluar', BarangKeluarController::class);
    
    // Kelola User
    Route::get('users-export-pdf', [AdminUserController::class, 'exportPdf'])->name('users.export-pdf');
    Route::resource('users', AdminUserController::class);
    
    // Activity Log
    Route::get('activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('activity-log/export', [AdminActivityLogController::class, 'export'])->name('activity-log.export');
    Route::get('activity-log/export-pdf', [AdminActivityLogController::class, 'exportPdf'])->name('activity-log.export-pdf');
    
    // Kelola Permintaan
    Route::get('permintaan', [\App\Http\Controllers\Admin\PermintaanController::class, 'index'])->name('permintaan.index');
    Route::get('permintaan/{id}', [\App\Http\Controllers\Admin\PermintaanController::class, 'show'])->name('permintaan.show');
    Route::post('permintaan/{id}/approve', [\App\Http\Controllers\Admin\PermintaanController::class, 'approve'])->name('permintaan.approve');
    Route::post('permintaan/{id}/process', [\App\Http\Controllers\Admin\PermintaanController::class, 'process'])->name('permintaan.process');
    Route::post('permintaan/{id}/reject', [\App\Http\Controllers\Admin\PermintaanController::class, 'reject'])->name('permintaan.reject');
    Route::post('permintaan/{id}/complete', [\App\Http\Controllers\Admin\PermintaanController::class, 'complete'])->name('permintaan.complete');
    Route::post('permintaan/{id}/update-status', [\App\Http\Controllers\Admin\PermintaanController::class, 'updateStatus'])->name('permintaan.update-status');
    Route::patch('permintaan/{id}/mark-complete', [\App\Http\Controllers\Admin\PermintaanController::class, 'markComplete'])->name('permintaan.mark-complete');
    
    // Add more admin routes here
    // Route::resource('reports', ReportController::class);
});

// User Routes
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');
    
    // Stok Barang (Read Only untuk User)
    Route::get('/stok-barang', [\App\Http\Controllers\User\StokBarangController::class, 'index'])->name('stok-barang.index');
    Route::get('/stok-barang/{id}', [\App\Http\Controllers\User\StokBarangController::class, 'show'])->name('stok-barang.show');
    
    // Barang Masuk (Read Only untuk User)
    Route::get('/barang-masuk', [\App\Http\Controllers\User\BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::get('/barang-masuk/{id}', [\App\Http\Controllers\User\BarangMasukController::class, 'show'])->name('barang-masuk.show');
    
    // Barang Keluar (Read Only untuk User - Lihat Semua)
    Route::get('/barang-keluar', [\App\Http\Controllers\User\BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::get('/barang-keluar/{id}', [\App\Http\Controllers\User\BarangKeluarController::class, 'show'])->name('barang-keluar.show');
    
    // Pemakaian Barang (CRUD Barang Keluar - Hanya Pemakaian Sendiri)
    Route::resource('pemakaian', \App\Http\Controllers\User\PemakaianController::class);
    Route::post('/pemakaian/{id}/selesai', [\App\Http\Controllers\User\PemakaianController::class, 'selesai'])->name('pemakaian.selesai');
    
    // Barang di Rak (Read Only - User hanya lihat, admin yang input via barang masuk)
    Route::get('/barang-rak', [\App\Http\Controllers\User\BarangRakController::class, 'index'])->name('barang-rak.index');
    
    // Request Barang - Semua ada di Pemakaian Saya
    Route::get('/request-barang', function() { return redirect()->route('user.pemakaian.index'); })->name('request-barang.index');
    Route::get('/request-barang/create', [\App\Http\Controllers\User\RequestBarangController::class, 'create'])->name('request-barang.create');
    Route::post('/request-barang', [\App\Http\Controllers\User\RequestBarangController::class, 'store'])->name('request-barang.store');
    Route::get('/request-barang/{id}', [\App\Http\Controllers\User\RequestBarangController::class, 'show'])->name('request-barang.show');
    Route::get('/request-barang/{id}/edit', [\App\Http\Controllers\User\RequestBarangController::class, 'edit'])->name('request-barang.edit');
    Route::put('/request-barang/{id}', [\App\Http\Controllers\User\RequestBarangController::class, 'update'])->name('request-barang.update');
    Route::delete('/request-barang/{id}', [\App\Http\Controllers\User\RequestBarangController::class, 'destroy'])->name('request-barang.destroy');
    Route::post('/request-barang/{id}/request-cancel', [\App\Http\Controllers\User\RequestBarangController::class, 'requestCancel'])->name('request-barang.request-cancel');
    Route::patch('/request-barang/{id}/complete', [\App\Http\Controllers\User\RequestBarangController::class, 'complete'])->name('request-barang.complete');
    
    // Activity Log (User hanya lihat log sendiri)
    Route::get('/activity-log', [UserActivityLogController::class, 'index'])->name('activity-log.index');
});
