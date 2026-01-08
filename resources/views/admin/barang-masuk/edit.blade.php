@extends('layouts.admin')

@section('title', 'Edit Barang Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.barang-masuk.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Barang Masuk</h2>
            <p class="text-sm text-gray-500 mt-1">Ubah data transaksi barang masuk</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.barang-masuk.update', $barangMasuk->idmasuk) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Barang -->
                <div>
                    <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Barang <span class="text-red-500">*</span>
                    </label>
                    <select name="idbarang" id="idbarang" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->idbarang }}" {{ $barangMasuk->idbarang == $stock->idbarang ? 'selected' : '' }}>
                                {{ $stock->kodebarang }} - {{ $stock->namabarang }}
                            </option>
                        @endforeach
                    </select>
                    @error('idbarang')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="tanggal" id="tanggal" required
                        value="{{ \Carbon\Carbon::parse($barangMasuk->tanggal)->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="qty" id="qty" min="1" required
                        value="{{ old('qty', $barangMasuk->qty) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                        placeholder="Masukkan jumlah barang">
                    @error('qty')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        <strong>Catatan:</strong> Jumlah sebelumnya: {{ $barangMasuk->qty }}. 
                        Stok akan disesuaikan otomatis dengan perubahan.
                    </p>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                        placeholder="Masukkan keterangan barang masuk">{{ old('keterangan', $barangMasuk->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.barang-masuk.index') }}" 
                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-[#14a2ba] text-white rounded-lg hover:bg-[#0d7a8f] transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
