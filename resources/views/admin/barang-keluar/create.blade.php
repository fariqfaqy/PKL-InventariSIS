@extends('layouts.admin')

@section('title', 'Barang Keluar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.barang-keluar.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Barang Keluar</h2>
            <p class="text-sm text-gray-500 mt-1">Input data barang keluar baru</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.barang-keluar.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori" id="kategori" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kategori') border-red-500 @enderror"
                    onchange="toggleSubKategoriKeluar()">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="barang_sewa" {{ old('kategori') == 'barang_sewa' ? 'selected' : '' }}>Aset Sewa</option>
                    <option value="habis_pakai" {{ old('kategori') == 'habis_pakai' ? 'selected' : '' }}>Material Umum</option>
                </select>
                @error('kategori')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sub-Kategori untuk Material Umum -->
            <div id="subKategoriFieldKeluar" class="hidden">
                <label for="sub_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                    Sub-Kategori Material Umum <span class="text-red-500">*</span>
                </label>
                <select name="sub_kategori" id="sub_kategori_keluar"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('sub_kategori') border-red-500 @enderror">
                    <option value="">-- Pilih Sub-Kategori --</option>
                    <option value="barang_habis_pakai" {{ old('sub_kategori') == 'barang_habis_pakai' ? 'selected' : '' }}>Barang Habis Pakai</option>
                    <option value="barang_pinjam" {{ old('sub_kategori') == 'barang_pinjam' ? 'selected' : '' }}>Barang Pinjam</option>
                </select>
                @error('sub_kategori')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Barang yang Tersedia <span class="text-red-500">*</span>
                </label>
                <select name="idbarang" id="idbarang" required disabled
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori Terlebih Dahulu --</option>
                </select>
                @error('idbarang')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Hanya barang dengan stok tersedia yang ditampilkan</p>
            </div>

            <!-- Info Barang yang Dipilih -->
            <div id="selected-item-info" class="hidden bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                    Barang yang Dipilih
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Kode:</span>
                        <span id="display_kodebarang" class="font-medium text-gray-900 ml-2"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Nama:</span>
                        <span id="display_namabarang" class="font-medium text-gray-900 ml-2"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Rak:</span>
                        <span id="display_rack" class="font-medium text-gray-900 ml-2"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Stok Tersedia:</span>
                        <span id="display_stock" class="font-bold text-green-600 ml-2"></span>
                        <span class="text-gray-500">unit</span>
                    </div>
                    <div id="display_durasi_container" class="hidden">
                        <span class="text-gray-600">Durasi Sewa:</span>
                        <span id="display_durasi_sewa" class="font-medium text-purple-600 ml-2"></span>
                    </div>
                </div>
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
                <p id="stock-info" class="mt-1 text-xs text-gray-500"></p>
            </div>

            <!-- Pengguna -->
            <div>
                <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                    Pengguna <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penerima" id="penerima" required
                    value="{{ old('penerima') }}"
                    placeholder="Masukkan nama pengguna"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('penerima') border-red-500 @enderror">
                @error('penerima')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.barang-keluar.index') }}" 
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
// Stock data from backend
const stocksData = @json($stocks);

// Elements
const kategoriSelect = document.getElementById('kategori');
const subKategoriSelect = document.getElementById('sub_kategori_keluar');
const barangSelect = document.getElementById('idbarang');
const qtyInput = document.getElementById('qty');
const stockInfo = document.getElementById('stock-info');
const selectedItemInfo = document.getElementById('selected-item-info');

// Toggle sub-kategori field for Material Umum
function toggleSubKategoriKeluar() {
    const kategori = kategoriSelect.value;
    const subKategoriField = document.getElementById('subKategoriFieldKeluar');
    
    if (kategori === 'habis_pakai') {
        subKategoriField.classList.remove('hidden');
        subKategoriSelect.required = true;
        // Reset barang selection
        barangSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>';
        barangSelect.disabled = true;
    } else {
        subKategoriField.classList.add('hidden');
        subKategoriSelect.required = false;
        subKategoriSelect.value = '';
        // Enable barang selection for barang_sewa
        if (kategori === 'barang_sewa') {
            updateBarangOptions();
        }
    }
}

// Update barang options based on kategori/sub-kategori
function updateBarangOptions() {
    const kategori = kategoriSelect.value;
    const subKategori = subKategoriSelect.value;
    
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    
    let filterKategori = kategori;
    
    // Untuk Material Umum (habis_pakai), gunakan sub-kategori sebagai filter
    if (kategori === 'habis_pakai') {
        if (!subKategori) {
            barangSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>';
            barangSelect.disabled = true;
            return;
        }
        filterKategori = subKategori;
    }
    
    if (filterKategori) {
        // Filter stocks by kategori and only show items with stock > 0
        const availableStocks = stocksData.filter(s => s.kategori === filterKategori && s.stock > 0);
        
        if (availableStocks.length > 0) {
            availableStocks.forEach(stock => {
                const option = document.createElement('option');
                option.value = stock.idbarang;
                option.textContent = `${stock.kodebarang} - ${stock.namabarang} (Stok: ${stock.stock})`;
                option.dataset.stock = stock.stock;
                option.dataset.kodebarang = stock.kodebarang;
                option.dataset.namabarang = stock.namabarang;
                option.dataset.rack = stock.rack || '-';
                option.dataset.kategori = stock.kategori;
                option.dataset.durasiSewa = stock.durasi_sewa || '';
                barangSelect.appendChild(option);
            });
            barangSelect.disabled = false;
        } else {
            barangSelect.innerHTML = '<option value="">-- Tidak Ada Barang Tersedia --</option>';
            barangSelect.disabled = true;
        }
    } else {
        barangSelect.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
        barangSelect.disabled = true;
    }
    
    // Reset selection info
    selectedItemInfo.classList.add('hidden');
    qtyInput.value = '';
    qtyInput.max = '';
    stockInfo.textContent = '';
}

// Event listeners
kategoriSelect.addEventListener('change', updateBarangOptions);
subKategoriSelect.addEventListener('change', updateBarangOptions);

// When barang changes, show item info
barangSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (this.value) {
        const stock = selectedOption.dataset.stock;
        const kodebarang = selectedOption.dataset.kodebarang;
        const namabarang = selectedOption.dataset.namabarang;
        const rack = selectedOption.dataset.rack;
        const kategori = selectedOption.dataset.kategori;
        const durasiSewa = selectedOption.dataset.durasiSewa;
        
        // Display selected item info
        document.getElementById('display_kodebarang').textContent = kodebarang;
        document.getElementById('display_namabarang').textContent = namabarang;
        document.getElementById('display_rack').textContent = rack === '-' ? '-' : 'Rak ' + rack.toUpperCase();
        document.getElementById('display_stock').textContent = stock;
        
        // Show durasi sewa only for barang_sewa
        if (kategori === 'barang_sewa' && durasiSewa) {
            document.getElementById('display_durasi_sewa').textContent = durasiSewa + ' hari';
            document.getElementById('display_durasi_container').classList.remove('hidden');
        } else {
            document.getElementById('display_durasi_container').classList.add('hidden');
        }
        
        selectedItemInfo.classList.remove('hidden');
        
        // Set max qty
        qtyInput.max = stock;
        stockInfo.textContent = `Stok tersedia: ${stock}`;
        stockInfo.classList.remove('text-red-500');
        stockInfo.classList.add('text-gray-500');
        
        // Focus on qty
        setTimeout(() => qtyInput.focus(), 100);
    } else {
        selectedItemInfo.classList.add('hidden');
        qtyInput.value = '';
        qtyInput.max = '';
        stockInfo.textContent = '';
    }
});

// Validate qty on input
qtyInput.addEventListener('input', function() {
    const max = parseInt(this.max);
    const value = parseInt(this.value);
    
    if (max && value > max) {
        stockInfo.textContent = `Jumlah melebihi stok tersedia (${max})`;
        stockInfo.classList.remove('text-gray-500');
        stockInfo.classList.add('text-red-500');
    } else if (max) {
        stockInfo.textContent = `Stok tersedia: ${max}`;
        stockInfo.classList.remove('text-red-500');
        stockInfo.classList.add('text-gray-500');
    }
});
</script>
@endsection
