@extends('layouts.admin')

@section('title', 'Detail Stok Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.stok-barang.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Stok Barang</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap stok barang</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.stok-barang.edit', $stock->idbarang) }}" 
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                <x-heroicon-o-pencil class="w-5 h-5 inline" /> Edit
            </a>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- QR Code Section -->
            <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                <div class="mb-4 text-center">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">QR Code Produk</h4>
                    <p class="text-xs text-gray-500">Scan untuk lihat detail</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    {!! QrCode::size(200)->generate(route('admin.stok-barang.show', $stock->idbarang)) !!}
                </div>
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded">{{ $stock->kodebarang }}</p>
                </div>
            </div>

            <!-- Info Section -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Barang</h3>
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
                    <span class="text-2xl font-bold 
                        @if($stock->stock > 10) text-green-600
                        @elseif($stock->stock > 0) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $stock->stock }}
                    </span>
                    <span class="text-sm text-gray-500">unit</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi Rak</label>
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Rak {{ strtoupper($stock->rack) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $stock->penginput }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Input</label>
                <p class="text-gray-800">{{ $stock->created_at->format('d M Y H:i') }}</p>
            </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Incoming Transactions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-arrow-down-circle class="w-6 h-6 text-green-600" />
                Riwayat Barang Masuk
            </h3>
            @if($stock->incomingTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($stock->incomingTransactions->take(5) as $transaction)
                <div class="border-l-4 border-green-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">+{{ $transaction->qty }} unit</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $transaction->penginput }}</span>
                    </div>
                    @if($transaction->keterangan)
                    <p class="text-sm text-gray-600 mt-1">{{ $transaction->keterangan }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @if($stock->incomingTransactions->count() > 5)
            <p class="text-sm text-gray-500 mt-4 text-center">
                Dan {{ $stock->incomingTransactions->count() - 5 }} transaksi lainnya
            </p>
            @endif
            @else
            <p class="text-gray-500 text-center py-4">Belum ada transaksi masuk</p>
            @endif
        </div>

        <!-- Outgoing Transactions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-arrow-up-circle class="w-6 h-6 text-red-600" />
                Riwayat Barang Keluar
            </h3>
            @if($stock->outgoingTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($stock->outgoingTransactions->take(5) as $transaction)
                <div class="border-l-4 border-red-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">-{{ $transaction->qty }} unit</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $transaction->penerima }}</span>
                    </div>
                    @if($transaction->keterangan)
                    <p class="text-sm text-gray-600 mt-1">{{ $transaction->keterangan }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @if($stock->outgoingTransactions->count() > 5)
            <p class="text-sm text-gray-500 mt-4 text-center">
                Dan {{ $stock->outgoingTransactions->count() - 5 }} transaksi lainnya
            </p>
            @endif
            @else
            <p class="text-gray-500 text-center py-4">Belum ada transaksi keluar</p>
            @endif
        </div>
    </div>
</div>
@endsection
