@extends('layouts.user')

@section('title', 'Catat Pemakaian Barang')
@section('subtitle', 'Catat pemakaian barang divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📤 Catat Pemakaian Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Catat pemakaian barang yang Anda gunakan</p>
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
                    <div>
                        <span class="text-sm text-gray-600">Stok Tersedia:</span>
                        <span id="stockValue" class="ml-2 text-lg font-bold text-[#14a2ba]">0 unit</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Kategori Barang:</span>
                        <span id="kategoriValue" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                    </div>
                    <div id="durasiInfo" class="hidden">
                        <span class="text-sm text-gray-600">Durasi Sewa:</span>
                        <span id="durasiValue" class="ml-2 font-semibold text-gray-800">-</span>
                    </div>
                </div>
            </div>

            <!-- Jumlah -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">Jumlah yang Digunakan *</label>
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

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <x-heroicon-o-check class="w-5 h-5" />
                    <span class="font-medium">Simpan Pemakaian</span>
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
function updateStockInfo() {
    const select = document.getElementById('idbarang');
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stockInfo');
    const stockValue = document.getElementById('stockValue');
    const kategoriValue = document.getElementById('kategoriValue');
    const durasiInfo = document.getElementById('durasiInfo');
    const durasiValue = document.getElementById('durasiValue');
    
    if (option.value) {
        const stock = option.dataset.stock;
        const kategori = option.dataset.kategori;
        const durasi = option.dataset.durasi;
        
        stockValue.textContent = stock + ' unit';
        
        // Update kategori
        if (kategori === 'barang_sewa') {
            kategoriValue.textContent = '🔄 Barang Sewa';
            kategoriValue.className = 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
            
            // Show durasi sewa
            if (durasi && durasi !== 'null') {
                durasiInfo.classList.remove('hidden');
                durasiValue.textContent = durasi + ' bulan';
            } else {
                durasiInfo.classList.add('hidden');
            }
        } else {
            kategoriValue.textContent = '📦 Habis Pakai';
            kategoriValue.className = 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800';
            durasiInfo.classList.add('hidden');
        }
        
        stockInfo.classList.remove('hidden');
        
        // Update max value for qty input
        document.getElementById('qty').max = stock;
    } else {
        stockInfo.classList.add('hidden');
    }
}
</script>
@endsection
