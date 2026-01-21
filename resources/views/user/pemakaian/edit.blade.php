@extends('layouts.user')

@section('title', 'Edit Pemakaian')
@section('subtitle', 'Edit pemakaian barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Pemakaian Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Ubah data pemakaian barang Anda</p>
        </div>
        <a href="{{ route('user.pemakaian.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            <span class="font-medium">Kembali</span>
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-x-circle class="w-5 h-5" />
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('user.pemakaian.update', $pemakaian->idkeluar) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Info Barang -->
            <div class="bg-blue-50 border-l-4 border-[#14a2ba] p-4 rounded-lg space-y-2">
                <div>
                    <span class="font-semibold text-gray-700">Barang:</span>
                    <span class="text-gray-900">{{ $pemakaian->kodebarang_k }} - {{ $pemakaian->namabarang_k }}</span>
                    @if($pemakaian->kategori)
                        @if($pemakaian->kategori == 'barang_sewa')
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Aset Sewa</span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">Habis Pakai</span>
                        @endif
                    @endif
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Tanggal:</span>
                    <span class="text-gray-900">{{ $pemakaian->tanggal->format('d/m/Y H:i') }}</span>
                </div>
                @if($pemakaian->kategori == 'barang_sewa' && $pemakaian->durasi_sewa)
                <div>
                    <span class="font-semibold text-gray-700">Durasi Sewa:</span>
                    <span class="text-gray-900">{{ $pemakaian->durasi_sewa }} bulan</span>
                </div>
                @endif
            </div>

            <!-- Jumlah -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">Jumlah yang Digunakan *</label>
                <input type="number" name="qty" id="qty" min="1" required 
                       value="{{ old('qty', $pemakaian->qty) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                @error('qty')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Jumlah sebelumnya: {{ $pemakaian->qty }} unit</p>
            </div>

            <!-- Penerima -->
            <div>
                <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">Penerima / Keterangan *</label>
                <input type="text" name="penerima" id="penerima" required 
                       value="{{ old('penerima', $pemakaian->penerima) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                @error('penerima')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <x-heroicon-o-check class="w-5 h-5" />
                    <span class="font-medium">Update Pemakaian</span>
                </button>
                <a href="{{ route('user.pemakaian.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="font-medium">Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
