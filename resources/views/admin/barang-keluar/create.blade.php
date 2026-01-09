@extends('layouts.admin')

@section('title', 'Tambah Barang Keluar')

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

            <!-- Filter Cascade -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Filter Kategori -->
                <div>
                    <label for="filter_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_kategori" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="barang_sewa">Barang Sewa</option>
                        <option value="habis_pakai">Habis Pakai</option>
                    </select>
                </div>

                <!-- Filter Jenis -->
                <div>
                    <label for="filter_jenis" class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Jenis <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_jenis" required disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">-- Pilih Jenis --</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Filter Merek -->
                <div>
                    <label for="filter_merek" class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Merek <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_merek" required disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">-- Pilih Merek --</option>
                    </select>
                </div>

                <!-- Filter Tipe -->
                <div>
                    <label for="filter_tipe" class="block text-sm font-medium text-gray-700 mb-2">
                        Filter Tipe <span class="text-red-500">*</span>
                    </label>
                    <select id="filter_tipe" required disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">-- Pilih Tipe --</option>
                    </select>
                </div>
            </div>

            <!-- Pilih Barang (Hidden - Auto filled) -->
            <input type="hidden" name="idbarang" id="idbarang">
            <input type="hidden" name="durasi_sewa" id="durasi_sewa">

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

            <!-- Penerima -->
            <div>
                <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">
                    Penerima / Peminjam <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penerima" id="penerima" required
                    value="{{ old('penerima') }}"
                    placeholder="Masukkan nama penerima/peminjam"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('penerima') border-red-500 @enderror">
                @error('penerima')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Tanggal Expire untuk Barang Sewa -->
            <div id="expire-info" class="hidden bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-900 mb-2 flex items-center gap-2">
                    <x-heroicon-o-calendar class="w-5 h-5" />
                    Informasi Peminjaman
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Peminjam:</span>
                        <span id="display_peminjam" class="font-medium text-gray-900 ml-2">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Tanggal Expire:</span>
                        <span id="display_expire" class="font-bold text-red-600 ml-2">-</span>
                    </div>
                </div>
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

// Cascade filter logic
const filters = {
    kategori: document.getElementById('filter_kategori'),
    jenis: document.getElementById('filter_jenis'),
    merek: document.getElementById('filter_merek'),
    tipe: document.getElementById('filter_tipe')
};

// Filter kategori change
filters.kategori.addEventListener('change', function() {
    const kategori = this.value;
    resetFilter(['jenis', 'merek', 'tipe']);
    
    if (kategori) {
        const jenisOptions = [...new Set(stocksData
            .filter(s => s.kategori === kategori && s.stock > 0)
            .map(s => s.jenis))];
        
        populateSelect(filters.jenis, jenisOptions);
        filters.jenis.disabled = false;
    }
});

// Filter jenis change
filters.jenis.addEventListener('change', function() {
    const kategori = filters.kategori.value;
    const jenis = this.value;
    resetFilter(['merek', 'tipe']);
    
    if (jenis) {
        const merekOptions = [...new Set(stocksData
            .filter(s => s.kategori === kategori && s.jenis === jenis && s.stock > 0)
            .map(s => s.merek))];
        
        populateSelect(filters.merek, merekOptions);
        filters.merek.disabled = false;
    }
});

// Filter merek change
filters.merek.addEventListener('change', function() {
    const kategori = filters.kategori.value;
    const jenis = filters.jenis.value;
    const merek = this.value;
    resetFilter(['tipe']);
    
    if (merek) {
        const tipeOptions = stocksData
            .filter(s => s.kategori === kategori && s.jenis === jenis && s.merek === merek && s.stock > 0)
            .map(s => ({ value: s.idbarang, text: s.tipe + ' (Stok: ' + s.stock + ')' }));
        
        populateSelectWithValue(filters.tipe, tipeOptions);
        filters.tipe.disabled = false;
    }
});

// Filter tipe change - final selection
filters.tipe.addEventListener('change', function() {
    const selectedId = this.value;
    
    if (selectedId) {
        const selectedStock = stocksData.find(s => s.idbarang == selectedId);
        
        if (selectedStock) {
            // Set hidden input
            document.getElementById('idbarang').value = selectedStock.idbarang;
            
            // Display selected item info
            document.getElementById('display_kodebarang').textContent = selectedStock.kodebarang;
            document.getElementById('display_namabarang').textContent = selectedStock.namabarang;
            document.getElementById('display_rack').textContent = 'Rak ' + selectedStock.rack.toUpperCase();
            document.getElementById('display_stock').textContent = selectedStock.stock;
            
            // Handle durasi sewa for barang sewa
            if (selectedStock.kategori === 'barang_sewa' && selectedStock.durasi_sewa) {
                document.getElementById('display_durasi_sewa').textContent = selectedStock.durasi_sewa + ' Tahun';
                document.getElementById('display_durasi_container').classList.remove('hidden');
                document.getElementById('durasi_sewa').value = selectedStock.durasi_sewa;
            } else {
                document.getElementById('display_durasi_container').classList.add('hidden');
                document.getElementById('durasi_sewa').value = '';
            }
            
            document.getElementById('selected-item-info').classList.remove('hidden');
            
            // Set max qty
            const qtyInput = document.getElementById('qty');
            qtyInput.max = selectedStock.stock;
            
            // Focus on qty
            setTimeout(() => qtyInput.focus(), 100);
        }
    } else {
        document.getElementById('selected-item-info').classList.add('hidden');
        document.getElementById('idbarang').value = '';
        document.getElementById('durasi_sewa').value = '';
    }
});

function populateSelect(selectElement, options) {
    selectElement.innerHTML = '<option value="">-- Pilih ' + selectElement.id.replace('filter_', '').charAt(0).toUpperCase() + selectElement.id.replace('filter_', '').slice(1) + ' --</option>';
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt;
        selectElement.appendChild(option);
    });
}

function populateSelectWithValue(selectElement, options) {
    selectElement.innerHTML = '<option value="">-- Pilih Tipe --</option>';
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        selectElement.appendChild(option);
    });
}

function resetFilter(filterNames) {
    filterNames.forEach(name => {
        const element = filters[name];
        element.innerHTML = '<option value="">-- Pilih ' + name.charAt(0).toUpperCase() + name.slice(1) + ' --</option>';
        element.disabled = true;
        element.value = '';
    });
    
    if (filterNames.includes('tipe')) {
        document.getElementById('selected-item-info').classList.add('hidden');
        document.getElementById('idbarang').value = '';
    }
}

// Validate qty on input
document.getElementById('qty').addEventListener('input', function() {
    const max = this.max;
    const stockInfo = document.getElementById('stock-info');
    
    if (max && parseInt(this.value) > parseInt(max)) {
        stockInfo.textContent = `Jumlah melebihi stok tersedia (${max})`;
        stockInfo.classList.remove('text-gray-500');
        stockInfo.classList.add('text-red-500');
    } else if (max) {
        stockInfo.textContent = `Stok tersedia: ${max}`;
        stockInfo.classList.remove('text-red-500');
        stockInfo.classList.add('text-gray-500');
    }
});

// Update expire info when penerima or tanggal changes
function updateExpireInfo() {
    const durasi = document.getElementById('durasi_sewa').value;
    const peminjam = document.getElementById('penerima').value;
    const tanggal = document.getElementById('tanggal').value;
    
    if (durasi && tanggal) {
        // Calculate expire date
        const tanggalKeluar = new Date(tanggal);
        const expireDate = new Date(tanggalKeluar);
        expireDate.setFullYear(expireDate.getFullYear() + parseInt(durasi));
        
        // Format expire date
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const expireFormatted = expireDate.toLocaleDateString('id-ID', options);
        
        // Show expire info
        document.getElementById('display_peminjam').textContent = peminjam || '-';
        document.getElementById('display_expire').textContent = expireFormatted;
        document.getElementById('expire-info').classList.remove('hidden');
    } else {
        document.getElementById('expire-info').classList.add('hidden');
    }
}

document.getElementById('penerima').addEventListener('input', updateExpireInfo);
document.getElementById('tanggal').addEventListener('change', updateExpireInfo);
</script>
@endsection
