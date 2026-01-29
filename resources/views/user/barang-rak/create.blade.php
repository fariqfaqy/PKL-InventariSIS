@extends('layouts.user')

@section('title', 'Tambah Barang ke Rak')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 text-sm text-gray-600 mb-2">
            <a href="{{ route('user.barang-rak.index') }}" class="hover:text-[#14a2ba] transition-colors">Barang di Rak</a>
            <x-heroicon-o-chevron-right class="w-4 h-4" />
            <span class="text-gray-800 font-medium">Tambah Barang</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Tambah Barang ke Rak</h2>
        <p class="text-gray-600 mt-1">Tempatkan barang dari stok ke rak penyimpanan</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('user.barang-rak.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Barang <span class="text-red-500">*</span>
                </label>
                <select name="idbarang" id="idbarang" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror" onchange="updateBarangInfo()">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" 
                                data-nama="{{ $barang->namabarang }}"
                                data-stok="{{ $barang->stock }}"
                                data-kategori="{{ $barang->kategori }}"
                                {{ old('idbarang') == $barang->idbarang ? 'selected' : '' }}>
                            {{ $barang->kodebarang }} - {{ $barang->namabarang }} (Stok: {{ $barang->stock }})
                        </option>
                    @endforeach
                </select>
                @error('idbarang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Barang -->
            <div id="barang-info" class="hidden bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h4 class="font-semibold text-blue-900 mb-2">Informasi Barang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                            <div>
                                <span class="text-blue-700">Nama:</span>
                                <span class="font-medium text-blue-900" id="info-nama">-</span>
                            </div>
                            <div>
                                <span class="text-blue-700">Kategori:</span>
                                <span id="info-kategori">-</span>
                            </div>
                            <div class="md:col-span-1">
                                <span class="text-blue-700">Stok Total:</span>
                                <span class="font-bold text-blue-900" id="info-stok">-</span>
                            </div>
                            <div class="md:col-span-3 pt-2 border-t border-blue-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-700">Sudah di rak:</span>
                                    <span class="font-semibold text-orange-600" id="info-in-racks">-</span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-blue-700">Tersedia untuk ditambah:</span>
                                    <span class="font-bold text-green-600 text-lg" id="info-available">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pilih Rak -->
            <div>
                <label for="rack" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Rak <span class="text-red-500">*</span>
                </label>
                <select name="rack" id="rack" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('rack') border-red-500 @enderror">
                    <option value="">-- Pilih Rak --</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack }}" {{ old('rack') == $rack ? 'selected' : '' }}>Rak {{ strtoupper($rack) }}</option>
                    @endforeach
                </select>
                @error('rack')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jumlah -->
            <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah <span class="text-red-500">*</span>
                </label>
                <input type="number" name="qty" id="qty" min="1" value="{{ old('qty', 1) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('qty') border-red-500 @enderror">
                @error('qty')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('keterangan') border-red-500 @enderror" placeholder="Tambahkan catatan jika diperlukan...">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-medium">
                    Simpan
                </button>
                <a href="{{ route('user.barang-rak.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>

<script>
function updateBarangInfo() {
    const select = document.getElementById('idbarang');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('barang-info');
    
    if (selectedOption.value) {
        const nama = selectedOption.dataset.nama;
        const stok = parseInt(selectedOption.dataset.stok);
        const kategori = selectedOption.dataset.kategori;
        const idbarang = selectedOption.value;
        
        document.getElementById('info-nama').textContent = nama;
        document.getElementById('info-stok').textContent = stok;
        
        const kategoriSpan = document.getElementById('info-kategori');
        if (kategori === 'aset_sewa') {
            kategoriSpan.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Aset Sewa</span>';;
        } else {
            kategoriSpan.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Material Umum</span>';
        }
        
        // Fetch qty already in racks via AJAX
        fetch(`/user/barang-rak/check-stock/${idbarang}`)
            .then(response => response.json())
            .then(data => {
                const inRacks = data.qtyInRacks || 0;
                const available = stok - inRacks;
                
                document.getElementById('info-in-racks').textContent = inRacks;
                document.getElementById('info-available').textContent = available;
                
                // Update max attribute on qty input
                document.getElementById('qty').setAttribute('max', available);
            })
            .catch(error => {
                console.error('Error fetching stock info:', error);
                document.getElementById('info-in-racks').textContent = '0';
                document.getElementById('info-available').textContent = stok;
            });
        
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

// Trigger on page load if there's an old value
window.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('idbarang').value) {
        updateBarangInfo();
    }
});
</script>
</div>
@endsection
