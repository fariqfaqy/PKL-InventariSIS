@extends('layouts.user')

@section('title', 'Ajukan Request Pemakaian')
@section('subtitle', 'Ajukan permintaan atau peminjaman barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-[#14a2ba] to-[#0d7a8f] rounded-xl flex items-center justify-center shadow-lg">
                <x-heroicon-o-document-plus class="w-7 h-7 text-white" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Ajukan Request Material Umum</h2>
                <p class="text-sm text-gray-500 mt-1">Pegawai dapat request material umum atau barang pinjam</p>
            </div>
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

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <h4 class="font-semibold mb-2">Terdapat kesalahan dalam form:</h4>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('user.pemakaian.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Info Box Material Umum -->
            <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-cyan-500 mt-0.5 mr-3 shrink-0" />
                    <div>
                        <p class="text-sm font-semibold text-cyan-800">Kategori Material Umum</p>
                        <p class="text-sm text-cyan-700 mt-1">
                            <span class="font-semibold">Barang Habis Pakai:</span> Barang yang digunakan dan tidak dikembalikan (ATK, konsumable).<br>
                            <span class="font-semibold">Barang Pinjam:</span> Barang yang dipinjam sementara dan harus dikembalikan (alat kerja, equipment).
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Pilih Sub-Kategori Material Umum -->
            <div>
                <label for="sub_kategori" class="block text-sm font-medium text-gray-700 mb-2">Sub-Kategori Material Umum *</label>
                <select name="sub_kategori" id="sub_kategori" required onchange="updateBarangList()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('sub_kategori') border-red-500 @enderror">
                    <option value="">-- Pilih Sub-Kategori --</option>
                    <option value="barang_habis_pakai" {{ old('sub_kategori') == 'barang_habis_pakai' ? 'selected' : '' }}>Barang Habis Pakai</option>
                    <option value="barang_pinjam" {{ old('sub_kategori') == 'barang_pinjam' ? 'selected' : '' }}>Barang Pinjam</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Pilih apakah Anda membutuhkan barang habis pakai atau barang pinjam
                </p>
                @error('sub_kategori')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang *</label>
                <select name="idbarang" id="idbarang" required onchange="updateStockInfo()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                    <option value="">-- Pilih sub-kategori terlebih dahulu --</option>
                </select>
                @error('idbarang')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Hidden data untuk material umum -->
                <div id="barangHabisPakaiData" style="display: none;">
                    @foreach($barangHabisPakai as $barang)
                    <div class="barang-item" 
                         data-id="{{ $barang->idbarang }}"
                         data-stock="{{ $barang->stock }}"
                         data-nama="{{ $barang->namabarang }}"
                         data-kode="{{ $barang->kodebarang }}"
                         data-sub-kategori="barang_habis_pakai">
                    </div>
                    @endforeach
                </div>
                
                <!-- Hidden data untuk barang pinjam -->
                <div id="barangPinjamData" style="display: none;">
                    @foreach($barangPinjam as $barang)
                    <div class="barang-item" 
                         data-id="{{ $barang->idbarang }}"
                         data-stock="{{ $barang->stock }}"
                         data-nama="{{ $barang->namabarang }}"
                         data-kode="{{ $barang->kodebarang }}"
                         data-sub-kategori="barang_pinjam">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Info Stok -->
            <div id="stockInfo" class="hidden bg-blue-50 border-l-4 border-[#14a2ba] p-4 rounded-lg">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Stok Tersedia:</span>
                        <span id="stockValue" class="text-sm font-semibold text-gray-800">0 unit</span>
                    </div>
                    <div class="border-t border-blue-200 pt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Sub-Kategori:</span>
                            <span id="subKategoriValue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tanggal Peminjaman (hanya untuk barang_pinjam) -->
            <div id="tanggalSection" class="hidden space-y-4">
            <div id="tanggalSection" class="hidden space-y-4">
                <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-lg">
                    <p class="text-sm text-purple-800 font-medium">
                        <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                        Tentukan periode peminjaman barang. Barang harus dikembalikan sesuai tanggal yang ditentukan.
                    </p>
                </div>
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam *</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                               value="{{ old('tanggal_pinjam') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        @error('tanggal_pinjam')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="tanggal_kembali" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali *</label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                               value="{{ old('tanggal_kembali') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        @error('tanggal_kembali')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Jumlah -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                <input type="number" name="qty" id="qty" min="1" required 
                       placeholder="Masukkan jumlah barang" value="{{ old('qty') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500">Jumlah tidak boleh melebihi stok yang tersedia</p>
                @error('qty')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keperluan -->
            <div>
                <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">Keperluan *</label>
                <textarea name="keperluan" id="keperluan" rows="3" required 
                       placeholder="Jelaskan untuk apa barang ini dibutuhkan" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">{{ old('keperluan') }}</textarea>
                @error('keperluan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Tambahan (Optional) -->
            <div>
                <label for="catatan_user" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan_user" id="catatan_user" rows="2" 
                       placeholder="Tambahkan catatan jika ada informasi tambahan" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">{{ old('catatan_user') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Opsional - Tambahkan informasi tambahan jika diperlukan</p>
                @error('catatan_user')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex items-start">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" />
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Informasi</p>
                        <p class="text-sm text-yellow-700 mt-1">
                            Request Anda akan menunggu persetujuan dari admin. Stok akan dikurangi setelah admin menyetujui request.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" id="submitButton" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                    <span class="font-medium" id="submitText">Ajukan Request</span>
                </button>
                <a href="{{ route('user.pemakaian.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="font-medium">Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Update list barang berdasarkan sub-kategori yang dipilih
 */
function updateBarangList() {
    const subKategoriSelect = document.getElementById('sub_kategori');
    const subKategori = subKategoriSelect.value;
    const barangSelect = document.getElementById('idbarang');
    const tanggalSection = document.getElementById('tanggalSection');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali');
    
    // Reset select barang
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    document.getElementById('stockInfo').classList.add('hidden');
    
    if (!subKategori) {
        barangSelect.innerHTML = '<option value="">-- Pilih sub-kategori terlebih dahulu --</option>';
        tanggalSection.classList.add('hidden');
        tanggalPinjam.required = false;
        tanggalKembali.required = false;
        return;
    }
    
    // Pilih data barang berdasarkan sub-kategori
    const dataContainerId = subKategori === 'barang_habis_pakai' ? 'barangHabisPakaiData' : 'barangPinjamData';
    const barangItems = document.querySelectorAll(`#${dataContainerId} .barang-item`);
    
    let count = 0;
    barangItems.forEach(item => {
        const id = item.getAttribute('data-id');
        const kode = item.getAttribute('data-kode');
        const nama = item.getAttribute('data-nama');
        const stock = item.getAttribute('data-stock');
        
        const option = document.createElement('option');
        option.value = id;
        option.textContent = kode + ' - ' + nama + ' (Stok: ' + stock + ')';
        option.setAttribute('data-stock', stock);
        option.setAttribute('data-nama', nama);
        option.setAttribute('data-kode', kode);
        option.setAttribute('data-sub-kategori', subKategori);
        barangSelect.appendChild(option);
        count++;
    });
    
    // Update placeholder dan section tanggal
    if (count === 0) {
        const labelText = subKategori === 'barang_habis_pakai' ? 'Barang Habis Pakai' : 'Barang Pinjam';
        barangSelect.innerHTML = `<option value="">-- Tidak ada ${labelText} tersedia --</option>`;
    } else {
        const labelText = subKategori === 'barang_habis_pakai' ? 'Barang Habis Pakai' : 'Barang Pinjam';
        barangSelect.options[0].text = `-- Pilih ${labelText} --`;
    }
    
    // Show/hide tanggal section berdasarkan sub-kategori
    if (subKategori === 'barang_pinjam') {
        tanggalSection.classList.remove('hidden');
        tanggalPinjam.required = true;
        tanggalKembali.required = true;
    } else {
        tanggalSection.classList.add('hidden');
        tanggalPinjam.required = false;
        tanggalKembali.required = false;
        tanggalPinjam.value = '';
        tanggalKembali.value = '';
    }
}

/**
 * Update info stok saat barang dipilih
 */
function updateStockInfo() {
    const select = document.getElementById('idbarang');
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stockInfo');
    const stockValue = document.getElementById('stockValue');
    const subKategoriValue = document.getElementById('subKategoriValue');
    const qtyInput = document.getElementById('qty');
    
    if (option.value) {
        const stock = option.getAttribute('data-stock');
        const subKategori = option.getAttribute('data-sub-kategori');
        
        stockInfo.classList.remove('hidden');
        stockValue.textContent = stock + ' unit';
        
        // Set badge sub-kategori
        if (subKategori === 'barang_habis_pakai') {
            subKategoriValue.textContent = 'Barang Habis Pakai';
            subKategoriValue.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
        } else if (subKategori === 'barang_pinjam') {
            subKategoriValue.textContent = 'Barang Pinjam';
            subKategoriValue.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
        }
        
        // Set max qty
        qtyInput.setAttribute('max', stock);
        
        // Validate current value
        if (qtyInput.value && parseInt(qtyInput.value) > parseInt(stock)) {
            qtyInput.value = stock;
        }
    } else {
        stockInfo.classList.add('hidden');
        qtyInput.removeAttribute('max');
    }
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('qty');
    const form = document.querySelector('form');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali');
    
    // Update min tanggal kembali saat tanggal pinjam berubah
    tanggalPinjam.addEventListener('change', function() {
        if (this.value) {
            // Set min tanggal kembali = tanggal pinjam + 1 hari
            const pinjamDate = new Date(this.value);
            pinjamDate.setDate(pinjamDate.getDate() + 1);
            const minKembali = pinjamDate.toISOString().split('T')[0];
            tanggalKembali.setAttribute('min', minKembali);
            
            // Reset tanggal kembali jika kurang dari min
            if (tanggalKembali.value && tanggalKembali.value <= this.value) {
                tanggalKembali.value = '';
            }
        }
    });
    
    // Validasi qty saat user mengetik
    qtyInput.addEventListener('input', function() {
        const max = this.getAttribute('max');
        if (max && parseInt(this.value) > parseInt(max)) {
            this.value = max;
            alert('Jumlah tidak boleh melebihi stok yang tersedia (' + max + ' unit)');
        }
    });
    
    // Validasi sebelum submit
    form.addEventListener('submit', function(e) {
        const max = qtyInput.getAttribute('max');
        const value = qtyInput.value;
        
        if (max && parseInt(value) > parseInt(max)) {
            e.preventDefault();
            alert('Jumlah tidak boleh melebihi stok yang tersedia (' + max + ' unit)');
            qtyInput.focus();
            return false;
        }
        
        // Disable submit button to prevent double submission
        const submitButton = document.getElementById('submitButton');
        const submitText = document.getElementById('submitText');
        submitButton.disabled = true;
        submitText.textContent = 'Mengirim...';
    });
    
    // Restore state jika ada old values (after validation error)
    const oldSubKategori = '{{ old("sub_kategori") }}';
    const oldIdBarang = '{{ old("idbarang") }}';
    
    if (oldSubKategori) {
        setTimeout(function() {
            updateBarangList();
            
            if (oldIdBarang) {
                setTimeout(function() {
                    const barangSelect = document.getElementById('idbarang');
                    barangSelect.value = oldIdBarang;
                    updateStockInfo();
                }, 100);
            }
        }, 100);
    }
});
</script>
@endsection