@extends('layouts.admin')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.barang-masuk.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Barang Masuk</h2>
            <p class="text-sm text-gray-500 mt-1">Input data barang masuk baru</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.barang-masuk.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Kode Barang -->
            <div>
                <label for="kodebarang" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Barang <span class="text-red-500">*</span>
                </label>
                <input type="text" name="kodebarang" id="kodebarang" required
                    value="{{ old('kodebarang') }}"
                    placeholder="Masukkan kode barang"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kodebarang') border-red-500 @enderror">
                @error('kodebarang')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Barang -->
            <div>
                <label for="namabarang" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <input type="text" name="namabarang" id="namabarang" required
                    value="{{ old('namabarang') }}"
                    placeholder="Masukkan nama barang"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('namabarang') border-red-500 @enderror">
                @error('namabarang')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal -->
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="tanggal" id="tanggal" required
                    value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('tanggal') border-red-500 @enderror">
                @error('tanggal')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah <span class="text-red-500">*</span>
                </label>
                <input type="number" name="qty" id="qty" min="1" required
                    value="{{ old('qty') }}"
                    placeholder="Masukkan jumlah barang"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('qty') border-red-500 @enderror">
                @error('qty')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea name="keterangan" id="keterangan" rows="4" required
                    placeholder="Masukkan keterangan barang masuk"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.barang-masuk.index') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
