<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BarangMasukController;
use App\Http\Controllers\Admin\BarangKeluarController;
use App\Http\Controllers\Admin\StokBarangController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\UserController;

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
    Route::resource('admins', AdminController::class);
    
    // Kelola Barang
    Route::resource('stok-barang', StokBarangController::class)->except(['create', 'store']);
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::resource('barang-keluar', BarangKeluarController::class);
    
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
    
    // Barang di Rak (CRUD - Kelola Barang di Rak)
    Route::get('/barang-rak/check-stock/{idbarang}', [\App\Http\Controllers\User\BarangRakController::class, 'checkStock'])->name('barang-rak.check-stock');
    Route::resource('barang-rak', \App\Http\Controllers\User\BarangRakController::class);
});
