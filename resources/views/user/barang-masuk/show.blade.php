@extends('layouts.user')

@section('title', 'Barang Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.barang-masuk.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-800">Detail Barang Masuk</h2>
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-300">
                        <x-heroicon-o-eye class="w-3 h-3" />
                        Read Only
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap transaksi barang masuk</p>
            </div>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <!-- Gambar Barang -->
        @if($barangMasuk->stock && $barangMasuk->stock->image)
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/barang/' . $barangMasuk->stock->image) }}" 
                 alt="{{ $barangMasuk->namabarang_m }}" 
                 class="w-full max-w-md h-64 object-cover rounded-xl border-2 border-gray-200 shadow-lg">
        </div>
        @endif

        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-[#14a2ba]" />
            Informasi Transaksi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangMasuk->kodebarang_m }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangMasuk->namabarang_m }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Masuk</label>
                <div class="flex items-center gap-2 text-gray-800">
                    <x-heroicon-o-calendar class="w-5 h-5 text-[#14a2ba]" />
                    <span>{{ \Carbon\Carbon::parse($barangMasuk->tanggal)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah</label>
                <p class="text-2xl font-bold text-green-600">+{{ $barangMasuk->qty }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $barangMasuk->keterangan }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $barangMasuk->penginput }}</p>
            </div>
        </div>
    </div>

    @if($barangMasuk->stock)
    <!-- Stock Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-cube class="w-6 h-6 text-[#14a2ba]" />
            Informasi Stok Terkait
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangMasuk->stock->kodebarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangMasuk->stock->namabarang }}</p>
            </div>
            
            @if($barangMasuk->stock->kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                @if($barangMasuk->stock->kategori === 'aset_sewa')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <x-heroicon-o-computer-desktop class="w-4 h-4 mr-1" />
                        Aset Sewa
                    </span>
                @elseif($barangMasuk->stock->kategori === 'aset_tetap')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                        <x-heroicon-o-building-office class="w-4 h-4 mr-1" />
                        Aset Tetap
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        <x-heroicon-o-shopping-bag class="w-4 h-4 mr-1" />
                        Material Umum
                    </span>
                @endif
            </div>
            @endif

            @if($barangMasuk->stock->sub_kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Sub Kategori</label>
                @if($barangMasuk->stock->sub_kategori === 'barang_pinjam')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <x-heroicon-o-arrow-path-rounded-square class="w-4 h-4 mr-1" />
                        Barang Pinjam
                    </span>
                @elseif($barangMasuk->stock->sub_kategori === 'barang_habis_pakai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <x-heroicon-o-shopping-cart class="w-4 h-4 mr-1" />
                        Barang Habis Pakai
                    </span>
                @else
                    <p class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $barangMasuk->stock->sub_kategori)) }}</p>
                @endif
            </div>
            @endif

            @if($barangMasuk->stock->jenis)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jenis</label>
                <p class="text-gray-800">{{ $barangMasuk->stock->jenis }}</p>
            </div>
            @endif

            @if($barangMasuk->stock->merek)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Merek</label>
                <p class="text-gray-800">{{ $barangMasuk->stock->merek }}</p>
            </div>
            @endif

            @if($barangMasuk->stock->tipe)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipe</label>
                <p class="text-gray-800">{{ $barangMasuk->stock->tipe }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $barangMasuk->stock->stock > 10 ? 'bg-green-100 text-green-800' : ($barangMasuk->stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $barangMasuk->stock->stock }} unit
                </span>
            </div>
            
            @if($barangMasuk->stock->kategori != 'aset_sewa' && $barangMasuk->stock->kategori != 'aset_tetap')
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    Rak {{ strtoupper($barangMasuk->stock->rack) }}
                </span>
            </div>
            @endif

            @if($barangMasuk->stock->deskripsi)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $barangMasuk->stock->deskripsi }}</p>
            </div>
            @endif
        </div>
        
        <!-- Link ke Detail Stok -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('user.stok-barang.show', $barangMasuk->stock->idbarang) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                <x-heroicon-o-cube class="w-5 h-5" />
                <span class="font-medium">Lihat Detail Stok Lengkap</span>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
