@extends('layouts.user')

@section('title', 'Detail Pemakaian Barang')
@section('subtitle', 'Informasi lengkap pemakaian barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.barang-keluar.index', ['kategori' => $barangKeluar->kategori]) }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pemakaian Barang</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap pemakaian barang Anda</p>
            </div>
        </div>
    </div>

    @if($barangKeluar->stock && $barangKeluar->kategori === 'aset_sewa')
    <!-- Aset Sewa Info Card -->
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-md p-6 border-2 border-purple-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-computer-desktop class="w-6 h-6 text-purple-600" />
                Informasi Pemakaian Aset Sewa
            </h3>
            @if($barangKeluar->status === 'sedang_dipakai')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5 animate-spin" />
                    Sedang Digunakan
                </span>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Pengguna</label>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold text-lg">{{ auth()->user()->name }}</p>
                        @if(auth()->user()->division)
                        <p class="text-sm text-gray-600">{{ auth()->user()->division->nama_divisi }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Status Pemakaian</label>
                @if($barangKeluar->status === 'sedang_dipakai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-blue-100 text-blue-800">
                        <x-heroicon-o-arrow-path class="w-5 h-5 mr-2 animate-spin" />
                        Sedang Digunakan
                    </span>
                @elseif($barangKeluar->status === 'selesai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-green-100 text-green-800">
                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                        Selesai
                    </span>
                @elseif($barangKeluar->status === 'ditarik')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-red-100 text-red-800">
                        <x-heroicon-o-x-circle class="w-5 h-5 mr-2" />
                        Ditarik
                    </span>
                @endif
            </div>
            
            <!-- Rental Period -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Periode Pemakaian</label>
                <div class="space-y-2">
                    @if($barangKeluar->tanggal_mulai_pakai)
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                        <span class="text-gray-600">Mulai:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($barangKeluar->tanggal_akhir_pakai)
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                        <span class="text-gray-600">Berakhir:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Duration Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-3">Durasi</label>
                @if($barangKeluar->tanggal_mulai_pakai && $barangKeluar->tanggal_akhir_pakai)
                    @php
                        $startDate = \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->startOfDay();
                        $endDate = \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->startOfDay();
                        $totalDays = $startDate->diffInDays($endDate) + 1;
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                        <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                        {{ $totalDays }} hari
                    </span>
                @else
                    <span class="text-sm text-gray-400">-</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($barangKeluar->stock && $barangKeluar->stock->sub_kategori === 'barang_pinjam' && $barangKeluar->tanggal_mulai_pakai && $barangKeluar->tanggal_akhir_pakai)
    <!-- Barang Pinjam Info Card -->
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-md p-6 border-2 border-blue-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-blue-600" />
                Informasi Peminjaman Barang
            </h3>
            @if($barangKeluar->status === 'sedang_dipakai')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                    Sedang Dipinjam
                </span>
            @elseif($barangKeluar->status === 'selesai')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1.5" />
                    Sudah Dikembalikan
                </span>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Peminjam</label>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                        {{ substr($barangKeluar->penerima ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold text-lg">{{ $barangKeluar->penerima }}</p>
                        @if(auth()->user()->division)
                        <p class="text-sm text-gray-600">{{ auth()->user()->division->nama_divisi }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Status Peminjaman</label>
                @if($barangKeluar->status === 'sedang_dipakai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-blue-100 text-blue-800">
                        <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                        Sedang Dipinjam
                    </span>
                @elseif($barangKeluar->status === 'selesai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-green-100 text-green-800">
                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                        Sudah Dikembalikan
                    </span>
                @elseif($barangKeluar->status === 'ditarik')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-red-100 text-red-800">
                        <x-heroicon-o-x-circle class="w-5 h-5 mr-2" />
                        Ditarik
                    </span>
                @endif
            </div>
            
            <!-- Rental Period -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Periode Peminjaman</label>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-blue-500" />
                        <span class="text-gray-600">Tanggal Pinjam:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-purple-500" />
                        <span class="text-gray-600">Tanggal Kembali:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Durasi & Progress -->
            <div class="bg-white rounded-lg p-4 shadow-sm md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-3">Durasi Peminjaman</label>
                @php
                    $now = \Carbon\Carbon::now()->startOfDay();
                    $startDate = \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->startOfDay();
                    $endDate = \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->startOfDay();
                    $totalDays = $startDate->diffInDays($endDate) + 1;
                    $daysLeft = $now->diffInDays($endDate, false);
                    $daysPassed = $startDate->diffInDays($now);
                    $progress = $totalDays > 0 ? min(100, max(0, ($daysPassed / $totalDays) * 100)) : 0;
                    $isExpired = $daysLeft < 0;
                @endphp
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-blue-600">{{ $totalDays }} hari</span>
                            <span class="text-sm text-gray-500">total durasi</span>
                        </div>
                        @if($barangKeluar->status === 'sedang_dipakai')
                            @if($isExpired)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" />
                                    Telat {{ abs(floor($daysLeft)) }} hari
                                </span>
                            @elseif($daysLeft <= 3)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                    <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                    {{ ceil($daysLeft) }} hari lagi
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                    {{ ceil($daysLeft) }} hari lagi
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                Selesai
                            </span>
                        @endif
                    </div>
                    
                    @if($barangKeluar->status === 'sedang_dipakai')
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full transition-all duration-300
                            @if($isExpired) bg-red-500
                            @elseif($progress < 50) bg-green-500
                            @elseif($progress < 90) bg-yellow-500
                            @else bg-orange-500
                            @endif" 
                            style="width: {{ min(100, $progress) }}%">
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 text-center">
                        @if($isExpired)
                            ⚠️ Peminjaman sudah melewati batas waktu
                        @else
                            {{ ceil($daysLeft) }} hari lagi sampai batas pengembalian
                        @endif
                    </p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($barangKeluar->status === 'sedang_dipakai')
        <!-- Reminder Note -->
        <div class="mt-4 p-4 bg-white rounded-lg border-l-4 {{ $isExpired ? 'border-red-500 bg-red-50' : ($daysLeft <= 3 ? 'border-yellow-500 bg-yellow-50' : 'border-blue-500') }}">
            <div class="flex items-start gap-3">
                @if($isExpired)
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                @elseif($daysLeft <= 3)
                    <x-heroicon-o-bell class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                @else
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                @endif
                <div>
                    <p class="text-sm font-semibold {{ $isExpired ? 'text-red-800' : ($daysLeft <= 3 ? 'text-yellow-800' : 'text-blue-800') }}">
                        @if($isExpired)
                            Perhatian! Batas Pengembalian Sudah Terlewat
                        @elseif($daysLeft <= 3)
                            Segera Kembalikan Barang
                        @else
                            Jangan Lupa Kembalikan Barang
                        @endif
                    </p>
                    <p class="text-xs {{ $isExpired ? 'text-red-700' : ($daysLeft <= 3 ? 'text-yellow-700' : 'text-blue-700') }} mt-1">
                        @if($isExpired)
                            Harap segera mengembalikan barang ini. Keterlambatan pengembalian dapat mempengaruhi peminjaman Anda di masa depan.
                        @elseif($daysLeft <= 3)
                            Batas pengembalian tinggal {{ ceil($daysLeft) }} hari lagi. Pastikan barang dalam kondisi baik saat dikembalikan.
                        @else
                            Pastikan mengembalikan barang sebelum tanggal {{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d F Y') }} dalam kondisi baik.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-[#14a2ba]" />
            Informasi Transaksi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->kodebarang_k }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->namabarang_k }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Keluar</label>
                <p class="text-gray-800">{{ \Carbon\Carbon::parse($barangKeluar->tanggal)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah</label>
                <p class="text-2xl font-bold text-red-600">-{{ $barangKeluar->qty }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penerima</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->penerima }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $barangKeluar->penginput }}</p>
            </div>
            @if($barangKeluar->durasi_sewa)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Durasi Sewa</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->durasi_sewa }} Tahun</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Expire</label>
                <p class="text-red-600 font-bold">
                    {{ \Carbon\Carbon::parse($barangKeluar->tanggal)->addYears($barangKeluar->durasi_sewa)->format('d/m/Y') }}
                </p>
            </div>
            @endif
        </div>
    </div>

    @if($barangKeluar->stock)
    <!-- Stock Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-cube class="w-6 h-6 text-[#14a2ba]" />
            Informasi Stok Terkait
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->stock->kodebarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->stock->namabarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $barangKeluar->stock->stock > 10 ? 'bg-green-100 text-green-800' : ($barangKeluar->stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $barangKeluar->stock->stock }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    {{ strtoupper($barangKeluar->stock->rack) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    {{ strtoupper($barangKeluar->stock->rack) }}
                </span>
            </div>
            @if($barangKeluar->stock->kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                @if($barangKeluar->stock->kategori === 'aset_sewa')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <x-heroicon-o-computer-desktop class="w-4 h-4 mr-1.5" />
                        Aset Sewa
                    </span>
                @elseif($barangKeluar->stock->kategori === 'aset_tetap')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                        <x-heroicon-o-building-office class="w-4 h-4 mr-1.5" />
                        Aset Tetap
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        <x-heroicon-o-shopping-bag class="w-4 h-4 mr-1.5" />
                        Material Umum
                    </span>
                @endif
            </div>
            @endif
            @if($barangKeluar->stock->kategori === 'material_umum' && $barangKeluar->stock->sub_kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Sub-Kategori</label>
                @if($barangKeluar->stock->sub_kategori === 'barang_habis_pakai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <x-heroicon-o-archive-box class="w-4 h-4 mr-1.5" />
                        Barang Habis Pakai
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                        Barang Pinjam
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($barangKeluar->stock && $barangKeluar->stock->sub_kategori === 'barang_pinjam' && $barangKeluar->tanggal_mulai_pakai && $barangKeluar->tanggal_akhir_pakai)
    <!-- Barang Pinjam Detail Card -->
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-md p-6 border-2 border-blue-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-blue-600" />
                Detail Peminjaman Barang
            </h3>
            @if($barangKeluar->status === 'sedang_dipakai')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                    <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                    Sedang Dipinjam
                </span>
            @elseif($barangKeluar->status === 'selesai')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1.5" />
                    Sudah Dikembalikan
                </span>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Peminjam Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Peminjam</label>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                        {{ substr($barangKeluar->penerima ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold text-lg">{{ $barangKeluar->penerima }}</p>
                        @if(auth()->user()->division)
                        <p class="text-sm text-gray-600">{{ auth()->user()->division->nama_divisi }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Periode Peminjaman -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Periode Peminjaman</label>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-blue-500" />
                        <span class="text-gray-600">Tanggal Pinjam:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-purple-500" />
                        <span class="text-gray-600">Tanggal Kembali:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Durasi & Progress -->
            <div class="bg-white rounded-lg p-4 shadow-sm md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-3">Durasi Peminjaman</label>
                @php
                    $now = \Carbon\Carbon::now()->startOfDay();
                    $startDate = \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->startOfDay();
                    $endDate = \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->startOfDay();
                    $totalDays = $startDate->diffInDays($endDate) + 1;
                    $daysLeft = $now->diffInDays($endDate, false);
                    $daysPassed = $startDate->diffInDays($now);
                    $progress = $totalDays > 0 ? min(100, max(0, ($daysPassed / $totalDays) * 100)) : 0;
                    $isExpired = $daysLeft < 0;
                @endphp
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-blue-600">{{ $totalDays }} hari</span>
                            <span class="text-sm text-gray-500">total durasi</span>
                        </div>
                        @if($barangKeluar->status === 'sedang_dipakai')
                            @if($isExpired)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" />
                                    Telat {{ abs(floor($daysLeft)) }} hari
                                </span>
                            @elseif($daysLeft <= 3)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                    <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                    {{ ceil($daysLeft) }} hari lagi
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                    {{ ceil($daysLeft) }} hari lagi
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                Selesai
                            </span>
                        @endif
                    </div>
                    
                    @if($barangKeluar->status === 'sedang_dipakai')
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full transition-all duration-300
                            @if($isExpired) bg-red-500
                            @elseif($progress < 50) bg-green-500
                            @elseif($progress < 90) bg-yellow-500
                            @else bg-orange-500
                            @endif" 
                            style="width: {{ min(100, $progress) }}%">
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 text-center">
                        @if($isExpired)
                            ⚠️ Peminjaman sudah melewati batas waktu pengembalian
                        @else
                            {{ ceil($daysLeft) }} hari lagi sampai batas pengembalian ({{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d F Y') }})
                        @endif
                    </p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($barangKeluar->status === 'sedang_dipakai')
        <!-- Reminder/Alert -->
        <div class="mt-4 p-4 bg-white rounded-lg border-l-4 {{ $isExpired ? 'border-red-500 bg-red-50' : ($daysLeft <= 3 ? 'border-yellow-500 bg-yellow-50' : 'border-blue-500') }}">
            <div class="flex items-start gap-3">
                @if($isExpired)
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                @elseif($daysLeft <= 3)
                    <x-heroicon-o-bell class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                @else
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                @endif
                <div>
                    <p class="text-sm font-semibold {{ $isExpired ? 'text-red-800' : ($daysLeft <= 3 ? 'text-yellow-800' : 'text-blue-800') }}">
                        @if($isExpired)
                            ⚠️ Perhatian! Batas Pengembalian Sudah Terlewat
                        @elseif($daysLeft <= 3)
                            🔔 Segera Kembalikan Barang
                        @else
                            ℹ️ Jangan Lupa Kembalikan Barang
                        @endif
                    </p>
                    <p class="text-xs {{ $isExpired ? 'text-red-700' : ($daysLeft <= 3 ? 'text-yellow-700' : 'text-blue-700') }} mt-1">
                        @if($isExpired)
                            Barang sudah melewati batas waktu pengembalian {{ abs(floor($daysLeft)) }} hari. Harap segera mengembalikan barang ini.
                        @elseif($daysLeft <= 3)
                            Batas pengembalian tinggal {{ ceil($daysLeft) }} hari lagi. Pastikan barang dalam kondisi baik saat dikembalikan.
                        @else
                            Pastikan mengembalikan barang sebelum tanggal {{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d F Y') }} dalam kondisi baik.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Stock Info -->
    @if($barangKeluar->stock)
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-cube class="w-6 h-6 text-[#14a2ba]" />
            Informasi Stok
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->stock->namabarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $barangKeluar->stock->stock > 10 ? 'bg-green-100 text-green-800' : ($barangKeluar->stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $barangKeluar->stock->stock }} unit
                </span>
            </div>
            @if($barangKeluar->stock->rack)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    {{ strtoupper($barangKeluar->stock->rack) }}
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('user.barang-keluar.index', ['kategori' => $barangKeluar->kategori]) }}" 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Kembali
        </a>
    </div>
</div>
@endsection
