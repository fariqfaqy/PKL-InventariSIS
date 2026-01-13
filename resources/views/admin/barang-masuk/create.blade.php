@extends('layouts.admin')

@section('title', 'Tambah Barang Masuk')

@section('content')
<style>
/* Prevent interaction on locked select fields while keeping them submittable */
.pointer-events-none {
    pointer-events: none;
    user-select: none;
}
</style>

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
        <form action="{{ route('admin.barang-masuk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Kode Barang -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Barang <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <!-- Dropdown Rak (2 digit) -->
                    <select name="rack_prefix" id="rack_prefix" required
                        class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kodebarang') border-red-500 @enderror"
                        onchange="updateKodeBarang()">
                        <option value="">Rak</option>
                        <option value="1A" {{ old('rack_prefix') == '1A' ? 'selected' : '' }}>1A</option>
                        <option value="1B" {{ old('rack_prefix') == '1B' ? 'selected' : '' }}>1B</option>
                        <option value="1C" {{ old('rack_prefix') == '1C' ? 'selected' : '' }}>1C</option>
                        <option value="2A" {{ old('rack_prefix') == '2A' ? 'selected' : '' }}>2A</option>
                        <option value="2B" {{ old('rack_prefix') == '2B' ? 'selected' : '' }}>2B</option>
                        <option value="2C" {{ old('rack_prefix') == '2C' ? 'selected' : '' }}>2C</option>
                    </select>
                    
                    <!-- Input 3 digit angka -->
                    <input type="text" name="kode_suffix" id="kode_suffix" required 
                        maxlength="3" 
                        pattern="[0-9]{3}"
                        value="{{ old('kode_suffix') }}"
                        placeholder="001"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kodebarang') border-red-500 @enderror"
                        oninput="updateKodeBarang()">
                    
                    <!-- Hidden input untuk full kode barang -->
                    <input type="hidden" name="kodebarang" id="kodebarang" value="{{ old('kodebarang') }}">
                </div>
                <p class="mt-1 text-xs text-gray-500">Format: [Rak 2 digit][Nomor 3 digit]. Contoh: 1A001, 2B050</p>
                @error('kodebarang')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                
                <!-- Info Alert: Barang Sudah Ada -->
                <div id="existingStockAlert" class="hidden mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mt-0.5" />
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">Barang Sudah Ada di Stok</h4>
                            <p class="text-sm text-blue-700 mb-2">Data barang otomatis terisi. Stok akan ditambahkan ke stok yang sudah ada.</p>
                            <div class="text-xs text-blue-600">
                                <span class="font-medium">Stok Saat Ini:</span> 
                                <span id="currentStockDisplay" class="font-bold">0</span> unit
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Nama Barang -->
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

            <!-- 3. Upload Gambar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Gambar Barang
                </label>
                
                <!-- Hidden file input -->
                <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                
                <!-- Custom Upload Button -->
                <div class="flex items-center gap-4">
                    <button type="button" id="uploadImageButton" onclick="document.getElementById('image').click()" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                        <span class="font-medium">Upload Image</span>
                    </button>
                    <span id="fileName" class="text-sm text-gray-500">Belum ada file dipilih</span>
                </div>
                
                <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, JPEG. Maksimal 2MB</p>
                @error('image')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                
                <!-- Image Preview -->
                <div id="imagePreview" class="hidden mt-3">
                    <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                </div>
            </div>

            <!-- 4. Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori" id="kategori" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('kategori') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="barang_sewa" {{ old('kategori') == 'barang_sewa' ? 'selected' : '' }}>Barang Sewa</option>
                    <option value="habis_pakai" {{ old('kategori') == 'habis_pakai' ? 'selected' : '' }}>Habis Pakai</option>
                    <option value="aset_tetap" {{ old('kategori') == 'aset_tetap' ? 'selected' : '' }}>Aset Tetap</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    <span class="font-medium">Catatan:</span> Barang Sewa dapat dipinjam dengan tanggal sewa. Aset Tetap tidak dapat dipinjam.
                </p>
                @error('kategori')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Stok -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                    Stok / Jumlah <span class="text-red-500">*</span>
                </label>
                <input type="number" name="qty" id="qty" min="1" required
                    value="{{ old('qty', 1) }}"
                    placeholder="Masukkan jumlah stok"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('qty') border-red-500 @enderror">
                @error('qty')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. Rak -->
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
                <p class="mt-1 text-xs text-gray-500">Rak akan otomatis terisi berdasarkan kode barang, tetapi bisa diubah.</p>
                @error('rack')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- 7. Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                    placeholder="Masukkan deskripsi barang (opsional)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" name="tanggal" value="{{ now()->format('Y-m-d H:i:s') }}">
            <input type="hidden" name="keterangan" value="Barang masuk">

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

<script>
// Update kode barang from dropdown and input
function updateKodeBarang() {
    const prefix = document.getElementById('rack_prefix').value;
    const suffix = document.getElementById('kode_suffix').value;
    const kodebarangInput = document.getElementById('kodebarang');
    const rackSelect = document.getElementById('rack');
    
    // Combine prefix and suffix
    if (prefix && suffix && suffix.length === 3) {
        const fullKode = prefix + suffix;
        kodebarangInput.value = fullKode;
        
        // Auto-fill rack based on prefix
        if (rackSelect) {
            rackSelect.value = prefix.toLowerCase();
        }
        
        // Check if stock exists via AJAX
        checkStockExists(fullKode);
    } else {
        kodebarangInput.value = '';
        if (rackSelect) {
            rackSelect.value = '';
        }
        hideExistingStockAlert();
    }
}

// Check if stock exists and auto-fill fields
function checkStockExists(kodebarang) {
    const url = `{{ route('admin.barang-masuk.check-stock') }}?kodebarang=${encodeURIComponent(kodebarang)}`;
    
    console.log('Checking stock for:', kodebarang);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Stock check response:', data);
        
        if (data.exists) {
            console.log('Stock exists! Auto-filling fields...');
            // Show alert and auto-fill fields
            showExistingStockAlert(data.data);
            autoFillFields(data.data);
        } else {
            console.log('Stock not found. Enabling fields for new entry...');
            // Hide alert and enable fields
            hideExistingStockAlert();
            enableFields();
        }
    })
    .catch(error => {
        console.error('Error checking stock:', error);
        // On error, enable fields untuk input manual
        hideExistingStockAlert();
        enableFields();
    });
}

// Show existing stock alert
function showExistingStockAlert(stockData) {
    const alert = document.getElementById('existingStockAlert');
    const stockDisplay = document.getElementById('currentStockDisplay');
    
    if (alert && stockDisplay) {
        alert.classList.remove('hidden');
        stockDisplay.textContent = stockData.current_stock;
    }
}

// Hide existing stock alert
function hideExistingStockAlert() {
    const alert = document.getElementById('existingStockAlert');
    if (alert) {
        alert.classList.add('hidden');
    }
}

// Auto-fill fields with existing stock data
function autoFillFields(stockData) {
    // Fill nama barang
    const namabarangInput = document.getElementById('namabarang');
    if (namabarangInput) {
        namabarangInput.value = stockData.namabarang;
        namabarangInput.readOnly = true;
        namabarangInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        namabarangInput.setAttribute('data-locked', 'true');
    }
    
    // Fill kategori - JANGAN disabled, gunakan pointer-events
    const kategoriSelect = document.getElementById('kategori');
    if (kategoriSelect) {
        kategoriSelect.value = stockData.kategori;
        kategoriSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        kategoriSelect.setAttribute('data-locked', 'true');
        // Tambahkan attribute readonly via tabindex untuk mencegah perubahan
        kategoriSelect.setAttribute('tabindex', '-1');
    }
    
    // Fill rack - JANGAN disabled, gunakan pointer-events
    const rackSelect = document.getElementById('rack');
    if (rackSelect) {
        rackSelect.value = stockData.rack;
        rackSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        rackSelect.setAttribute('data-locked', 'true');
        rackSelect.setAttribute('tabindex', '-1');
    }
    
    // Fill deskripsi
    const deskripsiTextarea = document.getElementById('deskripsi');
    if (deskripsiTextarea) {
        deskripsiTextarea.value = stockData.deskripsi || '';
        deskripsiTextarea.readOnly = true;
        deskripsiTextarea.classList.add('bg-gray-100', 'cursor-not-allowed');
        deskripsiTextarea.setAttribute('data-locked', 'true');
    }
    
    // Disable image upload
    const uploadButton = document.getElementById('uploadImageButton');
    if (uploadButton) {
        uploadButton.disabled = true;
        uploadButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

// Enable all fields (when creating new stock)
function enableFields() {
    // Enable nama barang
    const namabarangInput = document.getElementById('namabarang');
    if (namabarangInput && namabarangInput.getAttribute('data-locked') !== 'permanent') {
        namabarangInput.value = '';
        namabarangInput.readOnly = false;
        namabarangInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        namabarangInput.removeAttribute('data-locked');
    }
    
    // Enable kategori
    const kategoriSelect = document.getElementById('kategori');
    if (kategoriSelect && kategoriSelect.getAttribute('data-locked') !== 'permanent') {
        kategoriSelect.value = '';
        kategoriSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        kategoriSelect.removeAttribute('data-locked');
        kategoriSelect.removeAttribute('tabindex');
    }
    
    // Enable rack (but still auto-filled from prefix)
    const rackSelect = document.getElementById('rack');
    if (rackSelect && rackSelect.getAttribute('data-locked') !== 'permanent') {
        rackSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        rackSelect.removeAttribute('data-locked');
        rackSelect.removeAttribute('tabindex');
    }
    
    // Enable deskripsi
    const deskripsiTextarea = document.getElementById('deskripsi');
    if (deskripsiTextarea && deskripsiTextarea.getAttribute('data-locked') !== 'permanent') {
        deskripsiTextarea.value = '';
        deskripsiTextarea.readOnly = false;
        deskripsiTextarea.classList.remove('bg-gray-100', 'cursor-not-allowed');
        deskripsiTextarea.removeAttribute('data-locked');
    }
    
    // Enable image upload
    const uploadButton = document.getElementById('uploadImageButton');
    if (uploadButton) {
        uploadButton.disabled = false;
        uploadButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Auto-fill rack based on kode barang prefix
function autoFillRack() {
    const prefix = document.getElementById('rack_prefix').value;
    const rackSelect = document.getElementById('rack');
    
    if (prefix && rackSelect) {
        rackSelect.value = prefix.toLowerCase();
    }
}

// Preview uploaded image and update file name
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');
    
    if (file) {
        // Update file name display
        fileName.textContent = file.name;
        fileName.classList.remove('text-gray-500');
        fileName.classList.add('text-green-600', 'font-medium');
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        fileName.textContent = 'Belum ada file dipilih';
        fileName.classList.remove('text-green-600', 'font-medium');
        fileName.classList.add('text-gray-500');
        preview.classList.add('hidden');
    }
}

// On page load
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan semua field enabled saat pertama kali load
    enableFields();
    
    // Add event listeners to prevent changes on locked select fields
    const kategoriSelect = document.getElementById('kategori');
    const rackSelect = document.getElementById('rack');
    
    if (kategoriSelect) {
        kategoriSelect.addEventListener('mousedown', function(e) {
            if (this.getAttribute('data-locked') === 'true') {
                e.preventDefault();
                return false;
            }
        });
        
        kategoriSelect.addEventListener('keydown', function(e) {
            if (this.getAttribute('data-locked') === 'true') {
                e.preventDefault();
                return false;
            }
        });
    }
    
    if (rackSelect) {
        rackSelect.addEventListener('mousedown', function(e) {
            if (this.getAttribute('data-locked') === 'true') {
                e.preventDefault();
                return false;
            }
        });
        
        rackSelect.addEventListener('keydown', function(e) {
            if (this.getAttribute('data-locked') === 'true') {
                e.preventDefault();
                return false;
            }
        });
    }
    
    console.log('Barang Masuk form loaded and ready');
});
</script>
@endsection
