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
        <form action="{{ route('admin.barang-keluar.store') }}" method="POST" class="space-y-6" id="formBarangKeluar">
            @csrf

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori" id="kategori" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kategori') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="aset_sewa" {{ old('kategori') == 'aset_sewa' ? 'selected' : '' }}>Aset Sewa (Selesaikan Pemakaian)</option>
                    <option value="material_umum" {{ old('kategori') == 'material_umum' ? 'selected' : '' }}>Material Umum</option>
                    <option value="aset_tetap" {{ old('kategori') == 'aset_tetap' ? 'selected' : '' }}>Aset Tetap</option>
                </select>
                @error('kategori')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- ============== ASET SEWA SECTION ============== -->
            <div id="asetSewaSection" class="hidden space-y-6">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-computer-desktop class="w-5 h-5 text-purple-600" />
                        <h3 class="font-semibold text-purple-900">Selesaikan Pemakaian Aset Sewa</h3>
                    </div>
                    <p class="text-sm text-purple-700">Pilih aset sewa yang sedang digunakan untuk diselesaikan. Aset akan dikembalikan ke distributor/vendor dan tidak dapat digunakan kembali.</p>
                </div>

                <!-- Pilih Aset Sewa yang Sedang Digunakan -->
                <div>
                    <label for="idbarang_aset" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Aset Sewa yang Akan Diselesaikan <span class="text-red-500">*</span>
                    </label>
                    <select name="idbarang_aset_temp" id="idbarang_aset"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                        <option value="">-- Pilih Aset Sewa --</option>
                        @foreach($asetSewaAktif as $aset)
                            <option value="{{ $aset->idbarang }}" 
                                data-kodebarang="{{ $aset->kodebarang }}"
                                data-namabarang="{{ $aset->namabarang }}"
                                data-user="{{ $aset->user->name ?? '-' }}"
                                data-divisi="{{ $aset->user->division->nama_divisi ?? '-' }}"
                                data-tanggal-mulai="{{ $aset->tanggal_mulai_pakai ? \Carbon\Carbon::parse($aset->tanggal_mulai_pakai)->format('d M Y') : '-' }}"
                                data-tanggal-akhir="{{ $aset->tanggal_akhir_pakai ? \Carbon\Carbon::parse($aset->tanggal_akhir_pakai)->format('d M Y') : '-' }}"
                                data-durasi="{{ $aset->tanggal_mulai_pakai && $aset->tanggal_akhir_pakai ? \Carbon\Carbon::parse($aset->tanggal_mulai_pakai)->diffInDays(\Carbon\Carbon::parse($aset->tanggal_akhir_pakai)) + 1 : '-' }}"
                                data-sisa-hari="{{ $aset->tanggal_akhir_pakai ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($aset->tanggal_akhir_pakai)->startOfDay(), false) : '-' }}"
                                {{ old('idbarang') == $aset->idbarang ? 'selected' : '' }}>
                                {{ $aset->kodebarang }} - {{ $aset->namabarang }} ({{ $aset->user->name ?? 'Tidak Ada Pengguna' }})
                            </option>
                        @endforeach
                    </select>
                    @error('idbarang')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @if($asetSewaAktif->isEmpty())
                    <p class="mt-2 text-sm text-yellow-600 flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                        Tidak ada aset sewa yang sedang digunakan
                    </p>
                    @endif
                </div>

                <!-- Info Aset Sewa yang Dipilih -->
                <div id="asetSewaInfo" class="hidden bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-4">
                    <h3 class="font-semibold text-purple-900 mb-4 flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                        Informasi Aset Sewa
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Kode Barang:</span>
                            <span id="info_kodebarang" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Nama Barang:</span>
                            <span id="info_namabarang" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Pengguna:</span>
                            <span id="info_pengguna" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Divisi:</span>
                            <span id="info_divisi" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tanggal Mulai:</span>
                            <span id="info_tanggal_mulai" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tanggal Berakhir:</span>
                            <span id="info_tanggal_akhir" class="font-semibold text-gray-900 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Durasi Total:</span>
                            <span id="info_durasi" class="font-semibold text-indigo-600 ml-2"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Sisa Waktu:</span>
                            <span id="info_sisa_hari" class="font-semibold ml-2"></span>
                        </div>
                    </div>
                    
                    <!-- Warning -->
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700 flex items-start gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                            <span><strong>Perhatian:</strong> Setelah diselesaikan, aset ini akan dikembalikan ke distributor/vendor dan <strong>tidak dapat digunakan kembali</strong>.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============== MATERIAL UMUM SECTION ============== -->
            <div id="materialUmumSection" class="hidden space-y-6">
                <!-- Sub-Kategori untuk Material Umum -->
                <div>
                    <label for="sub_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub-Kategori Material Umum <span class="text-red-500">*</span>
                    </label>
                    <select name="sub_kategori" id="sub_kategori"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('sub_kategori') border-red-500 @enderror">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        <option value="barang_habis_pakai" {{ old('sub_kategori') == 'barang_habis_pakai' ? 'selected' : '' }}>Barang Habis Pakai</option>
                        <option value="barang_pinjam" {{ old('sub_kategori') == 'barang_pinjam' ? 'selected' : '' }}>Barang Pinjam</option>
                    </select>
                    @error('sub_kategori')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilih Barang Material Umum -->
                <div>
                    <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Barang <span class="text-red-500">*</span>
                    </label>
                    <select name="idbarang_temp" id="idbarang"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed @error('idbarang') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>
                    </select>
                    @error('idbarang')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Hanya barang dengan stok tersedia yang ditampilkan</p>
                </div>

                <!-- Info Barang yang Dipilih -->
                <div id="materialInfo" class="hidden bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4">
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
                    </div>
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="tanggal_temp" id="tanggal"
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
                    <input type="number" name="qty_temp" id="qty" min="1"
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
                    <select name="penerima_temp" id="penerima"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('user_id') border-red-500 @enderror">
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                data-name="{{ $user->name }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->division->nama_divisi ?? 'No Division' }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <!-- Hidden field untuk user_id yang akan di-submit -->
                    <input type="hidden" name="user_id_temp" id="user_id_hidden">
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan_temp" id="keterangan" rows="3"
                        placeholder="Masukkan keterangan tambahan (opsional)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ============== ASET TETAP SECTION ============== -->
            <div id="asetTetapSection" class="hidden space-y-6">
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-building-office class="w-5 h-5 text-teal-600" />
                        <h3 class="font-semibold text-teal-900">Pengeluaran Aset Tetap</h3>
                    </div>
                    <p class="text-sm text-teal-700">Aset tetap adalah barang inventaris permanen milik perusahaan yang digunakan dalam operasional jangka panjang.</p>
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal_tetap" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Keluar <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_temp" id="tanggal_tetap"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pilih Barang -->
                <div>
                    <label for="idbarang_tetap" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Aset Tetap <span class="text-red-500">*</span>
                    </label>
                    <select name="idbarang_tetap_temp" id="idbarang_tetap"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                        <option value="">-- Pilih Aset Tetap --</option>
                        @foreach($stocksAsetTetap as $stock)
                            <option value="{{ $stock->idbarang }}" 
                                data-stock="{{ $stock->stock }}"
                                data-kodebarang="{{ $stock->kodebarang }}"
                                data-namabarang="{{ $stock->namabarang }}"
                                {{ old('idbarang') == $stock->idbarang ? 'selected' : '' }}>
                                {{ $stock->kodebarang }} - {{ $stock->namabarang }} (Stok: {{ $stock->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('idbarang')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @if($stocksAsetTetap->isEmpty())
                    <p class="mt-2 text-sm text-yellow-600 flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                        Tidak ada aset tetap yang tersedia
                    </p>
                    @endif
                    <p id="stock-info-tetap" class="mt-1 text-xs text-gray-500"></p>
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="qty_tetap" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="qty_temp" id="qty_tetap" min="1"
                        value="{{ old('qty', 1) }}"
                        placeholder="Masukkan jumlah"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('qty') border-red-500 @enderror">
                    @error('qty')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pengguna -->
                <div>
                    <label for="penerima_tetap" class="block text-sm font-medium text-gray-700 mb-2">
                        Pengguna <span class="text-red-500">*</span>
                    </label>
                    <select name="penerima_temp" id="penerima_tetap"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('penerima') border-red-500 @enderror">
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->name }}" {{ old('penerima') == $user->name ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->division->nama_divisi ?? 'No Division' }}
                            </option>
                        @endforeach
                    </select>
                    @error('penerima')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan_tetap" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan_temp" id="keterangan_tetap" rows="3"
                        placeholder="Masukkan keterangan tambahan (opsional)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.barang-keluar.index') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" disabled
                    class="px-6 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="submitText">Simpan</span>
                </button>
            </div>
        </form>
    </div>

<script>
// Stock data from backend
const stocksMaterialUmum = @json($stocksMaterialUmum);
const stocksAsetTetap = @json($stocksAsetTetap);

console.log('=== Script Loaded ===');
console.log('Total Material Umum stocks:', stocksMaterialUmum.length);
console.log('Stock data:', stocksMaterialUmum);

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM Content Loaded ===');
    
    // Elements
    const kategoriSelect = document.getElementById('kategori');
    const subKategoriSelect = document.getElementById('sub_kategori');
    const barangSelect = document.getElementById('idbarang');
    const asetSewaSelect = document.getElementById('idbarang_aset');
    const asetTetapSelect = document.getElementById('idbarang_tetap');
    const penerimaSelect = document.getElementById('penerima');
    const penerimaTetapSelect = document.getElementById('penerima_tetap');
    const userIdHidden = document.getElementById('user_id_hidden');
    const tanggalInput = document.getElementById('tanggal');
    const tanggalTetapInput = document.getElementById('tanggal_tetap');
    const qtyInput = document.getElementById('qty');
    const qtyTetapInput = document.getElementById('qty_tetap');
    const keteranganTextarea = document.getElementById('keterangan');
    const keteranganTetapTextarea = document.getElementById('keterangan_tetap');
    const stockInfo = document.getElementById('stock-info');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    console.log('Elements found:', {
        kategori: !!kategoriSelect,
        subKategori: !!subKategoriSelect,
        barang: !!barangSelect
    });

    // Function to populate barang dropdown based on sub_kategori
    function populateBarangDropdown(subKategori) {
        console.log('=== populateBarangDropdown called ===');
        console.log('Sub-kategori:', subKategori);
        
        barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
        document.getElementById('materialInfo').classList.add('hidden');
        submitBtn.disabled = true;
        
        if (!subKategori) {
            barangSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>';
            return;
        }
        
        // Filter stocks by sub_kategori
        const filteredStocks = stocksMaterialUmum.filter(s => {
            console.log('Checking stock:', s.kodebarang, '- sub_kategori:', s.sub_kategori, 'vs', subKategori);
            return s.sub_kategori === subKategori && s.stock > 0;
        });
        
        console.log('Filtered stocks count:', filteredStocks.length);
        console.log('Filtered stocks:', filteredStocks);
        
        if (filteredStocks.length > 0) {
            filteredStocks.forEach(stock => {
                const option = document.createElement('option');
                option.value = stock.idbarang;
                option.textContent = `${stock.kodebarang} - ${stock.namabarang} (Stok: ${stock.stock})`;
                option.dataset.stock = stock.stock;
                option.dataset.kodebarang = stock.kodebarang;
                option.dataset.namabarang = stock.namabarang;
                option.dataset.rack = stock.rack || '-';
                barangSelect.appendChild(option);
                console.log('Appended option:', option.value, option.textContent);
            });
            console.log('Added', filteredStocks.length, 'options to dropdown');
            console.log('Dropdown now has', barangSelect.options.length, 'total options');
            console.log('Dropdown HTML:', barangSelect.innerHTML);
        } else {
            barangSelect.innerHTML = '<option value="">-- Tidak Ada Barang Tersedia --</option>';
            console.log('No stocks available');
        }
        
        // If there was an old value, try to restore it
        const oldValue = "{{ old('idbarang') }}";
        if (oldValue && barangSelect.querySelector(`option[value="${oldValue}"]`)) {
            barangSelect.value = oldValue;
            barangSelect.dispatchEvent(new Event('change'));
        }
    }


    // Toggle sections based on kategori
    kategoriSelect.addEventListener('change', function() {
        const kategori = this.value;
        const asetSewaSection = document.getElementById('asetSewaSection');
        const materialUmumSection = document.getElementById('materialUmumSection');
        const asetTetapSection = document.getElementById('asetTetapSection');
        
        // Reset all
        asetSewaSection.classList.add('hidden');
        materialUmumSection.classList.add('hidden');
        asetTetapSection.classList.add('hidden');
        document.getElementById('asetSewaInfo').classList.add('hidden');
        document.getElementById('materialInfo').classList.add('hidden');
        submitBtn.disabled = true;
        
        // Reset all name attributes to temp (so they won't be submitted)
        if (asetSewaSelect) asetSewaSelect.name = 'idbarang_aset_temp';
        barangSelect.name = 'idbarang_temp';
        if (asetTetapSelect) asetTetapSelect.name = 'idbarang_tetap_temp';
        if (penerimaSelect) penerimaSelect.name = 'penerima_temp';
        if (penerimaTetapSelect) penerimaTetapSelect.name = 'penerima_temp';
        if (userIdHidden) userIdHidden.name = 'user_id_temp';
        if (tanggalInput) tanggalInput.name = 'tanggal_temp';
        if (tanggalTetapInput) tanggalTetapInput.name = 'tanggal_temp';
        if (qtyInput) qtyInput.name = 'qty_temp';
        if (qtyTetapInput) qtyTetapInput.name = 'qty_temp';
        if (keteranganTextarea) keteranganTextarea.name = 'keterangan_temp';
        if (keteranganTetapTextarea) keteranganTetapTextarea.name = 'keterangan_temp';
        
        if (kategori === 'aset_sewa') {
            asetSewaSection.classList.remove('hidden');
            submitText.textContent = 'Selesaikan Pemakaian';
            // Change name to idbarang for submission (no other fields needed for aset_sewa)
            if (asetSewaSelect) asetSewaSelect.name = 'idbarang';
            // Reset material umum fields
            subKategoriSelect.value = '';
            barangSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>';
        } else if (kategori === 'material_umum') {
            materialUmumSection.classList.remove('hidden');
            submitText.textContent = 'Simpan';
            // Change name for submission - Material Umum fields
            barangSelect.name = 'idbarang';
            if (penerimaSelect) penerimaSelect.name = 'penerima_temp'; // Keep temp, use hidden field
            if (userIdHidden) userIdHidden.name = 'user_id'; // This will be submitted
            if (tanggalInput) tanggalInput.name = 'tanggal';
            if (qtyInput) qtyInput.name = 'qty';
            if (keteranganTextarea) keteranganTextarea.name = 'keterangan';
            // Reset aset sewa fields
            if (asetSewaSelect) asetSewaSelect.value = '';
        } else if (kategori === 'aset_tetap') {
            asetTetapSection.classList.remove('hidden');
            submitText.textContent = 'Simpan';
            submitBtn.disabled = false;
            // Change name for submission - Aset Tetap fields
            if (asetTetapSelect) asetTetapSelect.name = 'idbarang';
            if (penerimaTetapSelect) penerimaTetapSelect.name = 'penerima';
            if (tanggalTetapInput) tanggalTetapInput.name = 'tanggal';
            if (qtyTetapInput) qtyTetapInput.name = 'qty';
            if (keteranganTetapTextarea) keteranganTetapTextarea.name = 'keterangan';
            // Reset other fields
            if (asetSewaSelect) asetSewaSelect.value = '';
            subKategoriSelect.value = '';
            barangSelect.innerHTML = '<option value="">-- Pilih Sub-Kategori Terlebih Dahulu --</option>';
        }
    });

    // Aset Sewa selection change
    if (asetSewaSelect) {
    asetSewaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const infoDiv = document.getElementById('asetSewaInfo');
        
        if (this.value) {
            document.getElementById('info_kodebarang').textContent = selectedOption.dataset.kodebarang;
            document.getElementById('info_namabarang').textContent = selectedOption.dataset.namabarang;
            document.getElementById('info_pengguna').textContent = selectedOption.dataset.user;
            document.getElementById('info_divisi').textContent = selectedOption.dataset.divisi;
            document.getElementById('info_tanggal_mulai').textContent = selectedOption.dataset.tanggalMulai;
            document.getElementById('info_tanggal_akhir').textContent = selectedOption.dataset.tanggalAkhir;
            document.getElementById('info_durasi').textContent = selectedOption.dataset.durasi !== '-' ? selectedOption.dataset.durasi + ' hari' : '-';
            
            const sisaHari = parseInt(selectedOption.dataset.sisaHari);
            const sisaHariEl = document.getElementById('info_sisa_hari');
            if (!isNaN(sisaHari)) {
                if (sisaHari < 0) {
                    sisaHariEl.textContent = 'Lewat ' + Math.abs(sisaHari) + ' hari';
                    sisaHariEl.className = 'font-semibold ml-2 text-red-600';
                } else if (sisaHari === 0) {
                    sisaHariEl.textContent = 'Berakhir hari ini';
                    sisaHariEl.className = 'font-semibold ml-2 text-yellow-600';
                } else {
                    sisaHariEl.textContent = sisaHari + ' hari lagi';
                    sisaHariEl.className = 'font-semibold ml-2 text-green-600';
                }
            } else {
                sisaHariEl.textContent = '-';
                sisaHariEl.className = 'font-semibold ml-2 text-gray-600';
            }
            
            infoDiv.classList.remove('hidden');
            submitBtn.disabled = false;
        } else {
            infoDiv.classList.add('hidden');
            submitBtn.disabled = true;
        }
    });
    }

    // Sub-kategori change - filter barang
    subKategoriSelect.addEventListener('change', function() {
        console.log('=== Sub-kategori change event triggered ===');
        populateBarangDropdown(this.value);
    });

    // Barang selection change (Material Umum)
    barangSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('materialInfo');
    
    if (this.value) {
        document.getElementById('display_kodebarang').textContent = selectedOption.dataset.kodebarang;
        document.getElementById('display_namabarang').textContent = selectedOption.dataset.namabarang;
        document.getElementById('display_rack').textContent = selectedOption.dataset.rack !== '-' ? 'Rak ' + selectedOption.dataset.rack.toUpperCase() : '-';
        document.getElementById('display_stock').textContent = selectedOption.dataset.stock;
        
        qtyInput.max = selectedOption.dataset.stock;
        stockInfo.textContent = 'Stok tersedia: ' + selectedOption.dataset.stock;
        
        infoDiv.classList.remove('hidden');
        submitBtn.disabled = false;
    } else {
        infoDiv.classList.add('hidden');
        submitBtn.disabled = true;
        qtyInput.max = '';
        stockInfo.textContent = '';
    }
    });

    // Penerima selection change (Material Umum) - update hidden user_id field
    if (penerimaSelect && userIdHidden) {
    penerimaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            // Update hidden field dengan user_id
            userIdHidden.value = this.value; // value berisi user_id
            console.log('User ID selected:', this.value);
        } else {
            userIdHidden.value = '';
        }
    });
    
    // Initialize hidden field jika ada old value
    if (penerimaSelect.value) {
        userIdHidden.value = penerimaSelect.value;
    }
    }

    // Aset Tetap selection change
    if (asetTetapSelect) {
    asetTetapSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stockInfoTetap = document.getElementById('stock-info-tetap');
        const qtyTetap = document.getElementById('qty_tetap');
        
        if (this.value) {
            const stock = selectedOption.dataset.stock;
            stockInfoTetap.textContent = 'Stok tersedia: ' + stock;
            qtyTetap.max = stock;
            submitBtn.disabled = false;
        } else {
            stockInfoTetap.textContent = '';
            qtyTetap.max = '';
            submitBtn.disabled = true;
        }
    });
    }

    // Validate qty
    qtyInput.addEventListener('input', function() {
    const max = parseInt(this.max);
    const value = parseInt(this.value);
    
    if (max && value > max) {
        stockInfo.textContent = 'Jumlah melebihi stok tersedia (' + max + ')';
        stockInfo.classList.remove('text-gray-500');
        stockInfo.classList.add('text-red-500');
    } else if (max) {
        stockInfo.textContent = 'Stok tersedia: ' + max;
        stockInfo.classList.remove('text-red-500');
        stockInfo.classList.add('text-gray-500');
    }
    });

    // Validate qty for Aset Tetap
    if (qtyTetapInput) {
    qtyTetapInput.addEventListener('input', function() {
        const max = parseInt(this.max);
        const value = parseInt(this.value);
        const stockInfoTetap = document.getElementById('stock-info-tetap');
        
        if (max && value > max) {
            stockInfoTetap.textContent = 'Jumlah melebihi stok tersedia (' + max + ')';
            stockInfoTetap.classList.remove('text-gray-500');
            stockInfoTetap.classList.add('text-red-500');
        } else if (max) {
            stockInfoTetap.textContent = 'Stok tersedia: ' + max;
            stockInfoTetap.classList.remove('text-red-500');
            stockInfoTetap.classList.add('text-gray-500');
        }
    });
    }

    // Initialize on page load - with proper order
    console.log('=== Initializing form ==='); 
    console.log('Kategori initial value:', kategoriSelect.value);
    console.log('Sub-kategori initial value:', subKategoriSelect.value);
    
    // First trigger kategori if it has a value (this will set correct name attributes)
    if (kategoriSelect.value) {
        console.log('Triggering kategori change');
        kategoriSelect.dispatchEvent(new Event('change'));
    }
    
    // Then populate barang dropdown directly if sub-kategori has a value
    if (subKategoriSelect.value) {
        console.log('Directly populating barang dropdown for sub-kategori:', subKategoriSelect.value);
        // Use setTimeout to ensure kategori change is processed first
        setTimeout(() => {
            populateBarangDropdown(subKategoriSelect.value);
        }, 100);
    }
    
    // Add form submit listener for debugging
    const form = document.getElementById('formBarangKeluar');
    form.addEventListener('submit', function(e) {
        console.log('=== FORM SUBMIT EVENT ===');
        console.log('Kategori value:', kategoriSelect.value);
        console.log('Sub-kategori value:', subKategoriSelect.value);
        console.log('Barang (idbarang) value:', barangSelect.value);
        console.log('Barang name attribute:', barangSelect.name);
        console.log('Penerima value:', penerimaSelect ? penerimaSelect.value : 'N/A');
        console.log('Penerima name attribute:', penerimaSelect ? penerimaSelect.name : 'N/A');
        console.log('Aset Sewa name attribute:', asetSewaSelect ? asetSewaSelect.name : 'N/A');
        console.log('Aset Tetap name attribute:', asetTetapSelect ? asetTetapSelect.name : 'N/A');
        console.log('Penerima Tetap name attribute:', penerimaTetapSelect ? penerimaTetapSelect.name : 'N/A');
        console.log('Submit button disabled?', submitBtn.disabled);
        
        // Log all form data
        const formData = new FormData(form);
        console.log('=== FORM DATA ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        // Check if idbarang is in form data
        if (!formData.has('idbarang') || !formData.get('idbarang')) {
            console.error('ERROR: idbarang is missing or empty in form data!');
            console.log('Barang select element:', barangSelect);
            console.log('Barang select options:', Array.from(barangSelect.options).map(o => ({value: o.value, text: o.text, selected: o.selected})));
        }
    });
});
</script>
</div>
@endsection
