@extends('layouts.user')

@section('title', 'Detail Stok Barang')
@section('subtitle', 'Informasi lengkap stok barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('user.stok-barang.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Stok Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap stok barang</p>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-information-circle class="w-5 h-5 text-[#14a2ba]" />
            Informasi Barang
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $stock->kodebarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $stock->namabarang }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-800">{{ $stock->deskripsi ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold {{ $stock->stock >= 10 ? 'text-green-600' : ($stock->stock >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $stock->stock }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock->stock >= 10 ? 'bg-green-100 text-green-800' : ($stock->stock >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $stock->stock >= 10 ? 'Aman' : ($stock->stock >= 5 ? 'Menengah' : 'Kritis') }}
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi Rak</label>
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-blue-100 text-blue-800">
                    <x-heroicon-o-map-pin class="w-4 h-4 mr-1" />
                    Rak {{ strtoupper($stock->rack) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Satuan</label>
                <p class="text-gray-800">{{ $stock->satuan ?? 'Unit' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $stock->penginput ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                <p class="text-gray-800">{{ $stock->created_at ? $stock->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                <p class="text-gray-800">{{ $stock->updated_at ? $stock->updated_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div class="flex gap-3">
        <a href="{{ route('user.pemakaian.create', ['barang' => $stock->idbarang]) }}" 
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
            <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
            <span class="font-medium">Gunakan Barang Ini</span>
        </a>
        <a href="{{ route('user.stok-barang.index') }}" 
            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            <span class="font-medium">Kembali</span>
        </a>
    </div>
</div>
@endsection
