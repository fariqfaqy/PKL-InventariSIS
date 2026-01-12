@extends('layouts.user')

@section('title', 'Ajukan Request Pemakaian')
@section('subtitle', 'Ajukan permintaan atau peminjaman barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📤 Ajukan Request Pemakaian</h2>
            <p class="text-sm text-gray-500 mt-1">Ajukan permintaan barang habis pakai atau peminjaman barang sewa</p>
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

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('user.pemakaian.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Pilih Tipe Request -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Request *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#14a2ba] transition-colors">
                        <input type="radio" name="tipe_request" value="permintaan" required class="peer sr-only" onchange="toggleTipeRequest()">
                        <div class="peer-checked:border-[#14a2ba] peer-checked:bg-[#14a2ba]/5 absolute inset-0 rounded-lg border-2"></div>
                        <div class="relative flex items-center gap-3 w-full">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-document-text class="w-6 h-6 text-blue-600" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">Permintaan</p>
                                <p class="text-xs text-gray-500">Barang Habis Pakai</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#14a2ba] transition-colors">
                        <input type="radio" name="tipe_request" value="peminjaman" required class="peer sr-only" onchange="toggleTipeRequest()">
                        <div class="peer-checked:border-[#14a2ba] peer-checked:bg-[#14a2ba]/5 absolute inset-0 rounded-lg border-2"></div>
                        <div class="relative flex items-center gap-3 w-full">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <x-heroicon-o-arrow-path class="w-6 h-6 text-purple-600" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">Peminjaman</p>
                                <p class="text-xs text-gray-500">Barang Sewa</p>
                            </div>
                        </div>
                    </label>
                </div>
                @error('tipe_request')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang *</label>
                <select name="idbarang" id="idbarang" required onchange="updateStockInfo()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                    <option value="{{ $barang->idbarang }}" 
                            data-stock="{{ $barang->stock }}"
                            data-nama="{{ $barang->namabarang }}"
                            data-kode="{{ $barang->kodebarang }}"
                            data-kategori="{{ $barang->kategori }}"
                            data-durasi="{{ $barang->durasi_sewa }}">
                        {{ $barang->kodebarang }} - {{ $barang->namabarang }} (Stok: {{ $barang->stock }})
                    </option>
                    @endforeach
                </select>
                @error('idbarang')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                @error('qty')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penerima -->
            <div>
                <label for="penerima" class="block text-sm font-medium text-gray-700 mb-2">Penerima / Keterangan *</label>
                <input type="text" name="penerima" id="penerima" required 
                       placeholder="Nama penerima atau keterangan penggunaan" 
                       value="{{ old('penerima') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                @error('penerima')
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
    const tipeRequest = document.querySelector('input[name="tipe_request"]:checked')?.value;
    const tanggalSection = document.getElementById('tanggalSection');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali');
    
    if (tipeRequest === 'peminjaman') {
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

function updateStockInfo() {
    const select = document.getElementById('idbarang');
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stockInfo');
    const stockValue = document.getElementById('stockValue');
    const kategoriValue = document.getElementById('kategoriValue');
    
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
        document.getElementById('qty').setAttribute('max', stock);
    } else {
        stockInfo.classList.add('hidden');
    }
}
</script>
@endsection