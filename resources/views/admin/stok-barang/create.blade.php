@extends('layouts.admin')

@section('title', 'Tambah Stok Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.stok-barang.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Stok Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Input data stok barang baru</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.stok-barang.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Barang -->
                <div>
                    <label for="kodebarang" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Barang <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="rack_prefix" id="rack_prefix" required
                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kodebarang') border-red-500 @enderror"
                            onchange="updateKodeBarang()">
                            <option value="">Rak</option>
                            <option value="1A" {{ old('rack_prefix') == '1A' ? 'selected' : '' }}>1A</option>
                            <option value="1B" {{ old('rack_prefix') == '1B' ? 'selected' : '' }}>1B</option>
                            <option value="1C" {{ old('rack_prefix') == '1C' ? 'selected' : '' }}>1C</option>
                            <option value="2A" {{ old('rack_prefix') == '2A' ? 'selected' : '' }}>2A</option>
                            <option value="2B" {{ old('rack_prefix') == '2B' ? 'selected' : '' }}>2B</option>
                            <option value="2C" {{ old('rack_prefix') == '2C' ? 'selected' : '' }}>2C</option>
                        </select>
                        <input type="text" name="kode_suffix" id="kode_suffix" required maxlength="3" pattern="[0-9]{3}"
                            value="{{ old('kode_suffix') }}"
                            placeholder="001"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kodebarang') border-red-500 @enderror"
                            oninput="updateKodeBarang()">
                        <input type="hidden" name="kodebarang" id="kodebarang" value="{{ old('kodebarang') }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Format: [Rak][3 digit angka]. Contoh: 1A001</p>
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
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                    placeholder="Masukkan deskripsi barang (opsional)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="kategori" id="kategori" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kategori') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="barang_sewa" {{ old('kategori') == 'barang_sewa' ? 'selected' : '' }}>Barang Sewa (3 Tahun)</option>
                        <option value="habis_pakai" {{ old('kategori') == 'habis_pakai' ? 'selected' : '' }}>Habis Pakai</option>
                    </select>
                    @error('kategori')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis -->
                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="jenis" id="jenis" required
                        value="{{ old('jenis') }}"
                        placeholder="Contoh: Laptop, Mouse, Keyboard"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('jenis') border-red-500 @enderror">
                    @error('jenis')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Merek -->
                <div>
                    <label for="merek" class="block text-sm font-medium text-gray-700 mb-2">
                        Merek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="merek" id="merek" required
                        value="{{ old('merek') }}"
                        placeholder="Contoh: Apple, Dell, Logitech"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('merek') border-red-500 @enderror">
                    @error('merek')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe -->
                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tipe" id="tipe" required
                        value="{{ old('tipe') }}"
                        placeholder="Contoh: MacBook Pro M3 14 inch"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('tipe') border-red-500 @enderror">
                    @error('tipe')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stok Awal -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Awal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock" id="stock" min="0" required
                        value="{{ old('stock', 0) }}"
                        placeholder="Masukkan stok awal"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('stock') border-red-500 @enderror">
                    @error('stock')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rak -->
                <div>
                    <label for="rack" class="block text-sm font-medium text-gray-700 mb-2">
                        Rak <span class="text-red-500">*</span>
                    </label>
                    <select name="rack" id="rack" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('rack') border-red-500 @enderror">
                        <option value="">-- Pilih Rak --</option>
                        <option value="1a" {{ old('rack') == '1a' ? 'selected' : '' }}>Rak 1A</option>
                        <option value="1b" {{ old('rack') == '1b' ? 'selected' : '' }}>Rak 1B</option>
                        <option value="1c" {{ old('rack') == '1c' ? 'selected' : '' }}>Rak 1C</option>
                        <option value="2a" {{ old('rack') == '2a' ? 'selected' : '' }}>Rak 2A</option>
                        <option value="2b" {{ old('rack') == '2b' ? 'selected' : '' }}>Rak 2B</option>
                        <option value="2c" {{ old('rack') == '2c' ? 'selected' : '' }}>Rak 2C</option>
                    </select>
                    @error('rack')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.stok-barang.index') }}" 
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

<script>
function updateKodeBarang() {
    const prefix = document.getElementById('rack_prefix').value;
    const suffix = document.getElementById('kode_suffix').value;
    const kodebarang = document.getElementById('kodebarang');
    
    if (prefix && suffix) {
        kodebarang.value = prefix + suffix;
    } else {
        kodebarang.value = '';
    }
    
    // Auto-sync rack selection
    if (prefix) {
        const rackSelect = document.getElementById('rack');
        rackSelect.value = prefix.toLowerCase();
    }
}

// Auto-format 3 digit number
document.getElementById('kode_suffix').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    updateKodeBarang();
});
</script>
@endsection
