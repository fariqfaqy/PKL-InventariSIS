@extends('layouts.admin')

@section('title', 'Detail Barang Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.barang-masuk.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Barang Masuk</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap transaksi barang masuk</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.barang-masuk.edit', $barangMasuk->idmasuk) }}" 
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-pencil class="w-5 h-5" /> Edit
            </a>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
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
                <p class="text-gray-800">{{ \Carbon\Carbon::parse($barangMasuk->tanggal)->format('d/m/Y H:i') }}</p>
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
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $barangMasuk->stock->stock > 10 ? 'bg-green-100 text-green-800' : ($barangMasuk->stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $barangMasuk->stock->stock }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    {{ strtoupper($barangMasuk->stock->rack) }}
                </span>
            </div>
            @if($barangMasuk->stock->kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                @if($barangMasuk->stock->kategori === 'barang_sewa')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        Barang Sewa
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        Habis Pakai
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.barang-masuk.index') }}" 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Kembali
        </a>
        <form action="{{ route('admin.barang-masuk.destroy', $barangMasuk->idmasuk) }}" method="POST" 
            onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikurangi sebanyak {{ $barangMasuk->qty }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-trash class="w-5 h-5" /> Hapus Transaksi
            </button>
        </form>
    </div>
</div>
@endsection
