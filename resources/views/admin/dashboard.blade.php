@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-br from-[#14a2ba] via-[#12929e] to-[#0d7a8f] rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h3 class="text-3xl font-bold mb-3 tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h3>
                <p class="text-white/90 flex items-center gap-2 text-lg mb-2">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                    Sistem Informasi Inventaris PLN Indonesia Power
                </p>
                <p class="text-white/80 text-sm flex items-center gap-2">
                    <x-heroicon-o-calendar class="w-4 h-4" />
                    {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <div class="hidden lg:block">
                <x-heroicon-o-clipboard-document-list class="w-32 h-32 opacity-10" />
            </div>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Barang -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#14a2ba] group">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Total Stok</p>
                    <h4 id="totalBarang" class="text-3xl font-bold text-[#14a2ba] mt-2">{{ $stats['totalBarang'] }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['totalJenisBarang'] }} jenis barang</p>
                </div>
                <div class="bg-[#14a2ba]/10 p-3 rounded-lg group-hover:bg-[#14a2ba]/20 transition-colors duration-300">
                    <x-heroicon-o-cube class="w-8 h-8 text-[#14a2ba] group-hover:scale-110 transition-transform duration-300" />
                </div>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <x-heroicon-o-clock class="w-4 h-4 text-blue-500" />
                <span class="text-blue-500 font-medium">Real-time</span>
            </div>
        </div>

        <!-- Total User -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#16b8d1] group">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Total User</p>
                    <h4 id="totalUser" class="text-3xl font-bold text-[#16b8d1] mt-2">{{ $stats['totalUser'] }}</h4>
                    <p class="text-xs text-gray-500 mt-1">Pengguna aktif</p>
                </div>
                <div class="bg-[#16b8d1]/10 p-3 rounded-lg group-hover:bg-[#16b8d1]/20 transition-colors duration-300">
                    <x-heroicon-o-users class="w-8 h-8 text-[#16b8d1] group-hover:scale-110 transition-transform duration-300" />
                </div>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <span class="text-blue-500 font-medium">{{ \App\Models\User::where('role', 'admin')->count() }}</span>
                <span class="text-gray-500">admin,</span>
                <span class="text-blue-500 font-medium">{{ \App\Models\User::where('role', 'user')->count() }}</span>
                <span class="text-gray-500">user</span>
            </div>
        </div>

        <!-- Barang Masuk -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-green-500 group">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Barang Masuk</p>
                    <h4 id="barangMasuk" class="text-3xl font-bold text-green-600 mt-2">{{ $stats['barangMasuk'] }}</h4>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg group-hover:bg-green-200 transition-colors duration-300">
                    <x-heroicon-o-arrow-down-tray class="w-8 h-8 text-green-600 group-hover:scale-110 transition-transform duration-300" />
                </div>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                <span class="text-green-600 font-medium">{{ $stats['barangMasukHariIni'] }}</span>
                <span class="text-gray-500">hari ini</span>
            </div>
        </div>

        <!-- Barang Keluar -->
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-orange-500 group">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase">Barang Keluar</p>
                    <h4 id="barangKeluar" class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['barangKeluar'] }}</h4>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg group-hover:bg-orange-200 transition-colors duration-300">
                    <x-heroicon-o-arrow-up-tray class="w-8 h-8 text-orange-600 group-hover:scale-110 transition-transform duration-300" />
                </div>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                <span class="text-orange-600 font-medium">{{ $stats['barangKeluarHariIni'] }}</span>
                <span class="text-gray-500">hari ini</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Barang Sewa -->
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium uppercase mb-1">Barang Sewa</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $categoryStats['barang_sewa'] }}</p>
                </div>
                <x-heroicon-o-computer-desktop class="w-10 h-10 text-purple-500 opacity-50" />
            </div>
        </div>

        <!-- Material Umum -->
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-pink-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium uppercase mb-1">Material Umum</p>
                    <p class="text-2xl font-bold text-pink-600">{{ $categoryStats['habis_pakai'] }}</p>
                </div>
                <x-heroicon-o-shopping-bag class="w-10 h-10 text-pink-500 opacity-50" />
            </div>
        </div>

        <!-- Aset Tetap -->
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-teal-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium uppercase mb-1">Aset Tetap</p>
                    <p class="text-2xl font-bold text-teal-600">{{ $categoryStats['aset_tetap'] }}</p>
                </div>
                <x-heroicon-o-building-office class="w-10 h-10 text-teal-500 opacity-50" />
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium uppercase mb-1">Stok Rendah</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['lowStock'] }}</p>
                </div>
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-yellow-500 opacity-50" />
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-[#14a2ba]" />
                    Tren Transaksi 7 Hari Terakhir
                </h3>
                <p class="text-sm text-gray-500 mt-1">Perbandingan barang masuk dan keluar</p>
            </div>
            <div class="h-80">
                <canvas id="transactionChart"></canvas>
            </div>
            
            <!-- Chart Summary Stats -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-green-600" />
                            <p class="text-xs font-medium text-gray-600 uppercase">Total Masuk (7 Hari)</p>
                        </div>
                        <p class="text-2xl font-bold text-green-600" id="chart-total-incoming">{{ array_sum($chartData['incoming']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(array_sum($chartData['incoming']) / 7, 1) }} item/hari</p>
                    </div>
                    
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <x-heroicon-o-arrow-trending-down class="w-5 h-5 text-orange-600" />
                            <p class="text-xs font-medium text-gray-600 uppercase">Total Keluar (7 Hari)</p>
                        </div>
                        <p class="text-2xl font-bold text-orange-600" id="chart-total-outgoing">{{ array_sum($chartData['outgoing']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(array_sum($chartData['outgoing']) / 7, 1) }} item/hari</p>
                    </div>
                    
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <x-heroicon-o-scale class="w-5 h-5 text-blue-600" />
                            <p class="text-xs font-medium text-gray-600 uppercase">Selisih Bersih</p>
                        </div>
                        @php
                            $netFlow = array_sum($chartData['incoming']) - array_sum($chartData['outgoing']);
                        @endphp
                        <p class="text-2xl font-bold {{ $netFlow >= 0 ? 'text-green-600' : 'text-red-600' }}" id="chart-net-flow">
                            {{ $netFlow >= 0 ? '+' : '' }}{{ $netFlow }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($netFlow > 0)
                                <span class="text-green-600 font-medium">↑ Stok bertambah</span>
                            @elseif($netFlow < 0)
                                <span class="text-red-600 font-medium">↓ Stok berkurang</span>
                            @else
                                <span class="text-gray-600">→ Stabil</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Charts -->
        <div class="space-y-6">
            <!-- Category Distribution -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-squares-plus class="w-6 h-6 text-purple-600" />
                        Distribusi Kategori
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Perbandingan jenis barang</p>
                </div>
                <div class="h-56">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <span class="text-gray-700">Aset Sewa</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $categoryStats['barang_sewa'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-pink-500 rounded-full"></div>
                            <span class="text-gray-700">Material Umum</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $categoryStats['habis_pakai'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                            <span class="text-gray-700">Aset Tetap</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $categoryStats['aset_tetap'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Stock Status Distribution -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-chart-pie class="w-6 h-6 text-blue-600" />
                        Status Stok
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Kondisi stok barang</p>
                </div>
                <div class="h-56">
                    <canvas id="stockStatusChart"></canvas>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Normal (>10)</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $stockDistribution['normal'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-gray-700">Rendah (1-10)</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $stockDistribution['low'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-gray-700">Habis (0)</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $stockDistribution['out'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-presentation-chart-line class="w-6 h-6 text-[#14a2ba]" />
                Perbandingan Bulanan
            </h3>
            <p class="text-sm text-gray-500 mt-1">Perbandingan transaksi bulan ini dengan bulan lalu</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Current Month -->
            <div class="space-y-4 p-4 bg-gradient-to-br from-blue-50 to-white rounded-lg">
                <div class="flex items-center justify-between pb-3 border-b border-blue-200">
                    <h4 class="font-bold text-gray-800 text-lg">{{ now()->format('F Y') }}</h4>
                    <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">Bulan Ini</span>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 flex items-center gap-1">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-green-500" />
                                Barang Masuk
                            </span>
                            <span class="text-lg font-bold text-green-600">{{ $monthlyComparison['current']['incoming'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ $monthlyComparison['current']['incoming'] > 0 ? min(($monthlyComparison['current']['incoming'] / max($monthlyComparison['current']['incoming'] + $monthlyComparison['current']['outgoing'], 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 flex items-center gap-1">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-orange-500" />
                                Barang Keluar
                            </span>
                            <span class="text-lg font-bold text-orange-600">{{ $monthlyComparison['current']['outgoing'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-3 rounded-full transition-all duration-500" style="width: {{ $monthlyComparison['current']['outgoing'] > 0 ? min(($monthlyComparison['current']['outgoing'] / max($monthlyComparison['current']['incoming'] + $monthlyComparison['current']['outgoing'], 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <span class="text-sm font-medium text-gray-700">Net Flow</span>
                            <span class="text-lg font-bold {{ ($monthlyComparison['current']['incoming'] - $monthlyComparison['current']['outgoing']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyComparison['current']['incoming'] - $monthlyComparison['current']['outgoing'] >= 0 ? '+' : '' }}{{ $monthlyComparison['current']['incoming'] - $monthlyComparison['current']['outgoing'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Last Month -->
            <div class="space-y-4 p-4 bg-gradient-to-br from-gray-50 to-white rounded-lg">
                <div class="flex items-center justify-between pb-3 border-b border-gray-300">
                    <h4 class="font-bold text-gray-800 text-lg">{{ now()->subMonth()->format('F Y') }}</h4>
                    <span class="px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Bulan Lalu</span>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 flex items-center gap-1">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-green-500" />
                                Barang Masuk
                            </span>
                            <span class="text-lg font-bold text-green-600">{{ $monthlyComparison['last']['incoming'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-300 to-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $monthlyComparison['last']['incoming'] > 0 ? min(($monthlyComparison['last']['incoming'] / max($monthlyComparison['last']['incoming'] + $monthlyComparison['last']['outgoing'], 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 flex items-center gap-1">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-orange-500" />
                                Barang Keluar
                            </span>
                            <span class="text-lg font-bold text-orange-600">{{ $monthlyComparison['last']['outgoing'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-300 to-orange-500 h-3 rounded-full transition-all duration-500" style="width: {{ $monthlyComparison['last']['outgoing'] > 0 ? min(($monthlyComparison['last']['outgoing'] / max($monthlyComparison['last']['incoming'] + $monthlyComparison['last']['outgoing'], 1)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <span class="text-sm font-medium text-gray-700">Net Flow</span>
                            <span class="text-lg font-bold {{ ($monthlyComparison['last']['incoming'] - $monthlyComparison['last']['outgoing']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyComparison['last']['incoming'] - $monthlyComparison['last']['outgoing'] >= 0 ? '+' : '' }}{{ $monthlyComparison['last']['incoming'] - $monthlyComparison['last']['outgoing'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-br from-[#14a2ba] via-[#12929e] to-[#0d7a8f] rounded-xl shadow-xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
        <div class="relative">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                    <x-heroicon-o-rocket-launch class="w-7 h-7" />
                    Quick Actions
                </h3>
                <p class="text-white/80 text-sm mt-1">Akses cepat ke fitur utama</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('admin.barang-masuk.create') }}" class="group p-5 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-xl transition-all duration-300 text-white text-center border border-white/20 hover:border-white/40 hover:shadow-lg">
                    <x-heroicon-o-arrow-down-circle class="w-12 h-12 mx-auto mb-3 group-hover:translate-y-1 transition-transform" />
                    <p class="text-sm font-semibold">Input Masuk</p>
                    <p class="text-xs text-white/70 mt-1">Tambah barang</p>
                </a>
                <a href="{{ route('admin.barang-keluar.create') }}" class="group p-5 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-xl transition-all duration-300 text-white text-center border border-white/20 hover:border-white/40 hover:shadow-lg">
                    <x-heroicon-o-arrow-up-circle class="w-12 h-12 mx-auto mb-3 group-hover:-translate-y-1 transition-transform" />
                    <p class="text-sm font-semibold">Output Keluar</p>
                    <p class="text-xs text-white/70 mt-1">Catat keluar</p>
                </a>
                <a href="{{ route('admin.users.create') }}" class="group p-5 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-xl transition-all duration-300 text-white text-center border border-white/20 hover:border-white/40 hover:shadow-lg">
                    <x-heroicon-o-user-plus class="w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform" />
                    <p class="text-sm font-semibold">Tambah User</p>
                    <p class="text-xs text-white/70 mt-1">Buat pengguna</p>
                </a>
                <a href="{{ route('admin.stok-barang.index') }}" class="group p-5 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-xl transition-all duration-300 text-white text-center border border-white/20 hover:border-white/40 hover:shadow-lg">
                    <x-heroicon-o-cube class="w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform" />
                    <p class="text-sm font-semibold">Lihat Stok</p>
                    <p class="text-xs text-white/70 mt-1">Inventaris</p>
                </a>
                <a href="{{ route('admin.activity-log.index') }}" class="group p-5 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-xl transition-all duration-300 text-white text-center border border-white/20 hover:border-white/40 hover:shadow-lg">
                    <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-3 group-hover:scale-110 transition-transform" />
                    <p class="text-sm font-semibold">Activity Log</p>
                    <p class="text-xs text-white/70 mt-1">Riwayat</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Incoming Transactions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-50 to-white p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-green-600" />
                        Barang Masuk Terbaru
                    </h3>
                    <a href="{{ route('admin.barang-masuk.index') }}" class="text-sm text-[#14a2ba] hover:text-[#0d7a8f] font-semibold hover:underline flex items-center gap-1">
                        Lihat Semua
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentIncoming as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->stock->namabarang ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    +{{ $item->qty }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Belum ada transaksi masuk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Outgoing Transactions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-orange-50 to-white p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-orange-600" />
                        Barang Keluar Terbaru
                    </h3>
                    <a href="{{ route('admin.barang-keluar.index') }}" class="text-sm text-[#14a2ba] hover:text-[#0d7a8f] font-semibold hover:underline flex items-center gap-1">
                        Lihat Semua
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentOutgoing as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->stock->namabarang ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-semibold">
                                    -{{ $item->qty }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->penerima ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Belum ada transaksi keluar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Status Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-50 to-white p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-yellow-600" />
                    Stok Rendah (≤10)
                </h3>
                <p class="text-xs text-gray-500 mt-1">Barang yang perlu segera diisi ulang</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($lowStockItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->namabarang }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                    {{ $item->stock }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="flex items-center gap-1 text-yellow-600">
                                    <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                                    Rendah
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Tidak ada barang dengan stok rendah
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Stock Items -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-white p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-star class="w-6 h-6 text-blue-600" />
                    Top 5 Stok Terbanyak
                </h3>
                <p class="text-xs text-gray-500 mt-1">Barang dengan stok paling banyak</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($topItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->namabarang }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 {{ $item->kategori == 'barang_sewa' ? 'bg-purple-100 text-purple-800' : ($item->kategori == 'aset_tetap' ? 'bg-teal-100 text-teal-800' : 'bg-pink-100 text-pink-800') }} rounded-full text-xs font-semibold">
                                    {{ $item->kategori == 'barang_sewa' ? 'Sewa' : ($item->kategori == 'aset_tetap' ? 'Aset Tetap' : 'Material Umum') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                    {{ $item->stock }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-sm">
                                Tidak ada data stok
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transaction Chart
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: {!! json_encode($chartData['incoming']) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Barang Keluar',
                    data: {!! json_encode($chartData['outgoing']) !!},
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Category Pie Chart
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: ['Aset Sewa', 'Material Umum', 'Aset Tetap'],
            datasets: [{
                data: [
                    {{ $categoryStats['barang_sewa'] }},
                    {{ $categoryStats['habis_pakai'] }},
                    {{ $categoryStats['aset_tetap'] }}
                ],
                backgroundColor: [
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(20, 184, 166, 0.8)'
                ],
                borderColor: [
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                    'rgb(20, 184, 166)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Stock Status Pie Chart
    const ctxStock = document.getElementById('stockStatusChart').getContext('2d');
    new Chart(ctxStock, {
        type: 'doughnut',
        data: {
            labels: ['Normal (>10)', 'Rendah (1-10)', 'Habis (0)'],
            datasets: [{
                data: [
                    {{ $stockDistribution['normal'] }},
                    {{ $stockDistribution['low'] }},
                    {{ $stockDistribution['out'] }}
                ],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(234, 179, 8)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Real-time Dashboard Stats Update
    function updateDashboardStats() {
        fetch('{{ route('admin.dashboard.stats') }}')
            .then(response => response.json())
            .then(data => {
                // Update stats dengan animasi
                animateValue('totalBarang', parseInt(document.getElementById('totalBarang').textContent), data.totalBarang, 500);
                animateValue('totalUser', parseInt(document.getElementById('totalUser').textContent), data.totalUser, 500);
                animateValue('barangMasuk', parseInt(document.getElementById('barangMasuk').textContent), data.barangMasuk, 500);
                animateValue('barangKeluar', parseInt(document.getElementById('barangKeluar').textContent), data.barangKeluar, 500);
            })
            .catch(error => {
                console.error('Error fetching dashboard stats:', error);
            });
    }

    // Animasi angka saat update
    function animateValue(elementId, start, end, duration) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const range = end - start;
        const increment = range / (duration / 16); // 60fps
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                element.textContent = end;
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current);
            }
        }, 16);
    }

    // Update stats setiap 30 detik
    setInterval(updateDashboardStats, 30000);

    // Update stats pertama kali setelah 2 detik
    setTimeout(updateDashboardStats, 2000);
</script>
@endpush
@endsection
