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
                <h2 class="text-2xl font-bold text-gray-800">Ajukan Request Pemakaian</h2>
                <p class="text-sm text-gray-500 mt-1">Ajukan permintaan barang habis pakai atau peminjaman barang sewa</p>
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
            
            <!-- Pilih Tipe Request -->
            <div>
                <label for="tipe_request" class="block text-sm font-medium text-gray-700 mb-2">Tipe Request *</label>
                <select name="tipe_request" id="tipe_request" required onchange="toggleTipeRequest()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('tipe_request') border-red-500 @enderror">
                    <option value="">-- Pilih Tipe Request --</option>
                    <option value="permintaan" {{ old('tipe_request') == 'permintaan' ? 'selected' : '' }}>Permintaan (Barang Habis Pakai)</option>
                    <option value="peminjaman" {{ old('tipe_request') == 'peminjaman' ? 'selected' : '' }}>Peminjaman (Barang Sewa)</option>
                </select>
                @error('tipe_request')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang *</label>
                <select name="idbarang" id="idbarang" required onchange="updateStockInfo()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                    <option value="">-- Pilih tipe request terlebih dahulu --</option>
                    @if(old('idbarang'))
                        @foreach($barangs as $barang)
                            @if($barang->idbarang == old('idbarang'))
                                <option value="{{ $barang->idbarang }}" selected
                                        data-stock="{{ $barang->stock }}"
                                        data-nama="{{ $barang->namabarang }}"
                                        data-kode="{{ $barang->kodebarang }}"
                                        data-kategori="{{ $barang->kategori }}"
                                        data-durasi="{{ $barang->durasi_sewa }}">
                                    {{ $barang->kodebarang }} - {{ $barang->namabarang }} (Stok: {{ $barang->stock }})
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                @error('idbarang')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <!-- Hidden data untuk barang -->
                <div id="barangData" style="display: none;">
                    @foreach($barangs as $barang)
                    <div class="barang-item" 
                         data-id="{{ $barang->idbarang }}"
                         data-stock="{{ $barang->stock }}"
                         data-nama="{{ $barang->namabarang }}"
                         data-kode="{{ $barang->kodebarang }}"
                         data-kategori="{{ $barang->kategori }}"
                         data-durasi="{{ $barang->durasi_sewa }}">
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
                            <span class="text-sm text-gray-600">Kategori Barang:</span>
                            <span id="kategoriValue" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tanggal Peminjaman (hanya untuk peminjaman barang sewa) -->
            <div id="tanggalSection" class="hidden space-y-4">
                <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-lg">
                    <p class="text-sm text-purple-800 font-medium">
                        <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                        Tentukan periode peminjaman barang sewa
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam *</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                               value="{{ old('tanggal_pinjam') }}"
                               min="{{ date('Y-m-d') }}"
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

            <!-- Penerima -->
            <div>
                <label for="catatan_user" class="block text-sm font-medium text-gray-700 mb-2">Penerima *</label>
                <input type="text" name="catatan_user" id="catatan_user" required
                       placeholder="Nama penerima barang" value="{{ old('catatan_user') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                @error('catatan_user')
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
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                    <span class="font-medium">Ajukan Request</span>
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
function toggleTipeRequest() {
    const tipeRequestSelect = document.getElementById('tipe_request');
    const tipeRequest = tipeRequestSelect.value;
    const tanggalSection = document.getElementById('tanggalSection');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali');
    const barangSelect = document.getElementById('idbarang');
    
    console.log('Tipe request dipilih:', tipeRequest);
    
    // Reset select barang
    barangSelect.innerHTML = '<option value="">-- Pilih Barang --</option>';
    document.getElementById('stockInfo').classList.add('hidden');
    
    if (!tipeRequest) {
        barangSelect.innerHTML = '<option value="">-- Pilih tipe request terlebih dahulu --</option>';
        tanggalSection.classList.add('hidden');
        tanggalPinjam.required = false;
        tanggalKembali.required = false;
        return;
    }
    
    // Ambil data barang dari hidden div
    const barangItems = document.querySelectorAll('.barang-item');
    let filteredCount = 0;
    
    console.log('Total barang items:', barangItems.length);
    
    barangItems.forEach(item => {
        const kategori = item.getAttribute('data-kategori');
        const id = item.getAttribute('data-id');
        const kode = item.getAttribute('data-kode');
        const nama = item.getAttribute('data-nama');
        const stock = item.getAttribute('data-stock');
        const durasi = item.getAttribute('data-durasi');
        
        console.log('Barang:', kode, '| Kategori:', kategori);
        
        let shouldShow = false;
        
        if (tipeRequest === 'peminjaman' && kategori === 'barang_sewa') {
            shouldShow = true;
        } else if (tipeRequest === 'permintaan' && kategori === 'habis_pakai') {
            shouldShow = true;
        }
        
        if (shouldShow) {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = kode + ' - ' + nama + ' (Stok: ' + stock + ')';
            option.setAttribute('data-stock', stock);
            option.setAttribute('data-nama', nama);
            option.setAttribute('data-kode', kode);
            option.setAttribute('data-kategori', kategori);
            option.setAttribute('data-durasi', durasi);
            barangSelect.appendChild(option);
            filteredCount++;
            console.log('Barang ditambahkan:', kode);
        }
    });
    
    console.log('Filtered count:', filteredCount);
    
    // Update placeholder dan tanggal section
    if (tipeRequest === 'peminjaman') {
        if (filteredCount === 0) {
            barangSelect.innerHTML = '<option value="">-- Tidak ada barang sewa tersedia --</option>';
        } else {
            barangSelect.options[0].text = '-- Pilih barang sewa --';
        }
        tanggalSection.classList.remove('hidden');
        tanggalPinjam.required = true;
        tanggalKembali.required = true;
    } else if (tipeRequest === 'permintaan') {
        if (filteredCount === 0) {
            barangSelect.innerHTML = '<option value="">-- Tidak ada barang habis pakai tersedia --</option>';
        } else {
            barangSelect.options[0].text = '-- Pilih barang habis pakai --';
        }
        tanggalSection.classList.add('hidden');
        tanggalPinjam.required = false;
        tanggalKembali.required = false;
        tanggalPinjam.value = '';
        tanggalKembali.value = '';
    }
}

function updateStockInfo() {
    const select = document.getElementById('idbarang');
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stockInfo');
    const stockValue = document.getElementById('stockValue');
    const kategoriValue = document.getElementById('kategoriValue');
    const qtyInput = document.getElementById('qty');
    
    if (option.value) {
        const stock = option.getAttribute('data-stock');
        const kategori = option.getAttribute('data-kategori');
        
        stockInfo.classList.remove('hidden');
        stockValue.textContent = stock + ' unit';
        
        if (kategori === 'barang_sewa') {
            kategoriValue.textContent = 'Barang Sewa';
            kategoriValue.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
        } else {
            kategoriValue.textContent = 'Habis Pakai';
            kategoriValue.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800';
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

// Validasi qty saat user mengetik
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('qty');
    const form = document.querySelector('form');
    
    // Restore state jika ada old values (after validation error)
    const oldTipeRequest = '{{ old("tipe_request") }}';
    const oldIdBarang = '{{ old("idbarang") }}';
    
    if (oldTipeRequest) {
        console.log('Restoring old state:', oldTipeRequest, oldIdBarang);
        // Trigger toggle to populate barang options
        setTimeout(function() {
            toggleTipeRequest();
            
            // Select the old barang if exists
            if (oldIdBarang) {
                setTimeout(function() {
                    const barangSelect = document.getElementById('idbarang');
                    barangSelect.value = oldIdBarang;
                    updateStockInfo();
                }, 100);
            }
        }, 100);
    }
    
    qtyInput.addEventListener('input', function() {
        const max = this.getAttribute('max');
        if (max && parseInt(this.value) > parseInt(max)) {
            this.value = max;
            alert('Jumlah tidak boleh melebihi stok yang tersedia (' + max + ' unit)');
        }
    });
    
    form.addEventListener('submit', function(e) {
        const max = qtyInput.getAttribute('max');
        const value = qtyInput.value;
        
        if (max && parseInt(value) > parseInt(max)) {
            e.preventDefault();
            alert('Jumlah tidak boleh melebihi stok yang tersedia (' + max + ' unit)');
            qtyInput.focus();
            return false;
        }
    });
});
</script>
@endsection