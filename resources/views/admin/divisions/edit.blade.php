@extends('layouts.admin')

@section('title', 'Edit Divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.divisions.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Divisi</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui data divisi</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.divisions.update', $division->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Divisi -->
                <div>
                    <label for="nama_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Divisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_divisi" id="nama_divisi" required
                        value="{{ old('nama_divisi', $division->nama_divisi) }}"
                        placeholder="Contoh: Divisi IT"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('nama_divisi') border-red-500 @enderror">
                    @error('nama_divisi')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Divisi -->
                <div>
                    <label for="kode_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Divisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_divisi" id="kode_divisi" required
                        value="{{ old('kode_divisi', $division->kode_divisi) }}"
                        placeholder="Contoh: IT-001"
                        maxlength="5"
                        style="text-transform: uppercase;"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kode_divisi') border-red-500 @enderror">
                    @error('kode_divisi')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maksimal 5 karakter</p>
                </div>

                <!-- Kepala Divisi -->
                <div class="md:col-span-2">
                    <label for="kepala_divisi" class="block text-sm font-medium text-gray-700 mb-2">
                        Kepala Divisi
                    </label>
                    <input type="text" name="kepala_divisi" id="kepala_divisi"
                        value="{{ old('kepala_divisi', $division->kepala_divisi) }}"
                        placeholder="Nama kepala divisi"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kepala_divisi') border-red-500 @enderror">
                    @error('kepala_divisi')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                        placeholder="Deskripsi singkat tentang divisi ini"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $division->deskripsi) }}</textarea>
                    @error('deskripsi')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                            {{ old('is_active', $division->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 text-[#14a2ba] border-gray-300 rounded focus:ring-2 focus:ring-[#14a2ba]">
                        <span class="text-sm font-medium text-gray-700">
                            Aktifkan divisi
                        </span>
                    </label>
                    <p class="mt-1 ml-8 text-xs text-gray-500">Divisi yang aktif akan muncul di pilihan saat menambah pegawai</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.divisions.index') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
