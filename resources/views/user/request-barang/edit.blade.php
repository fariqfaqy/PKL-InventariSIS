@extends('layouts.user')

@section('title', 'Edit Request Barang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Request Barang #{{ $requestBarang->id_request }}</h1>
            <p class="text-gray-600 mt-2">@if($requestBarang->status === 'pending') Update data request Anda @else Perubahan akan membuat request baru yang perlu persetujuan admin @endif</p>
        </div>
        <a href="{{ route('user.request-barang.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
            <x-heroicon-o-arrow-left class="h-5 w-5 inline" /> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.request-barang.update', $requestBarang->id_request) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <!-- Pilih Barang -->
        <div class="mb-6">
            <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Barang<span class="text-red-500">*</span>
            </label>
            <select id="idbarang" name="idbarang" required disabled
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-100 cursor-not-allowed">
                <option value="">-- Pilih Barang --</option>
                @foreach($stocks as $stock)
                    <option value="{{ $stock->idbarang }}" 
                            data-kategori="{{ $stock->kategori }}"
                            data-stock="{{ $stock->stock }}"
                            data-nama="{{ $stock->namabarang }}"
                            data-kode="{{ $stock->kodebarang }}"
                            {{ old('idbarang', $requestBarang->idbarang) == $stock->idbarang ? 'selected' : '' }}>
                        {{ $stock->namabarang }} ({{ $stock->kodebarang }}) - Stok: {{ $stock->stock }} - {{ ucwords(str_replace('_', ' ', $stock->kategori)) }}
                    </option>
                @endforeach
            </select>
            <!-- Hidden input to still submit the value -->
            <input type="hidden" name="idbarang" value="{{ $requestBarang->idbarang }}">
            <p class="text-xs text-gray-500 mt-1">Barang tidak dapat diubah. Jika ingin request barang lain, silakan buat request baru.</p>
            
            <!-- Barang Info Display -->
            <div id="barangInfo" class="mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600">Nama:</span>
                        <span id="infoNama" class="font-medium ml-2">{{ $requestBarang->stock->namabarang }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Kode:</span>
                        <span id="infoKode" class="font-medium ml-2">{{ $requestBarang->stock->kodebarang }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Kategori:</span>
                        <span id="infoKategori" class="font-medium ml-2">{{ ucwords(str_replace('_', ' ', $requestBarang->stock->kategori)) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Stok Tersedia:</span>
                        <span id="infoStok" class="font-medium ml-2 text-green-600">{{ $requestBarang->stock->stock }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jumlah -->
        <div class="mb-6">
            <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                Jumlah<span class="text-red-500">*</span>
            </label>
            <input type="number" id="qty" name="qty" min="1" required value="{{ old('qty', $requestBarang->qty) }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Maksimal sesuai stok yang tersedia</p>
        </div>

        <!-- Rental Dates Section (Hidden by default, shown for barang_sewa) -->
        <div id="rentalSection" class="hidden mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Periode Sewa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai_sewa" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai Sewa<span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai_sewa" name="tanggal_mulai_sewa" value="{{ old('tanggal_mulai_sewa', $requestBarang->tanggal_mulai_sewa?->format('Y-m-d')) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tanggal_akhir_sewa" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Akhir Sewa<span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_akhir_sewa" name="tanggal_akhir_sewa" value="{{ old('tanggal_akhir_sewa', $requestBarang->tanggal_akhir_sewa?->format('Y-m-d')) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Keperluan -->
        <div class="mb-6">
            <label for="keperluan" class="block text-sm font-medium text-gray-700 mb-2">
                Keperluan<span class="text-red-500">*</span>
            </label>
            <textarea id="keperluan" name="keperluan" rows="3" required
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      placeholder="Jelaskan untuk apa barang ini dibutuhkan...">{{ old('keperluan', $requestBarang->keperluan) }}</textarea>
        </div>

        <!-- Catatan User -->
        <div class="mb-6">
            <label for="catatan_user" class="block text-sm font-medium text-gray-700 mb-2">
                Catatan Tambahan (Opsional)
            </label>
            <textarea id="catatan_user" name="catatan_user" rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      placeholder="Catatan tambahan jika ada...">{{ old('catatan_user', $requestBarang->catatan_user) }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <button type="submit" class="w-full sm:flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center">
                <x-heroicon-o-pencil class="h-5 w-5 mr-2" />
                @if($requestBarang->status === 'pending') 
                    Update Request 
                @else 
                    Buat Request Perubahan 
                @endif
            </button>
            <a href="{{ route('user.request-barang.index') }}" class="w-full sm:w-auto px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-center font-medium">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function updateBarangInfo() {
    const select = document.getElementById('idbarang');
    const selectedOption = select.options[select.selectedIndex];
    const barangInfo = document.getElementById('barangInfo');
    const rentalSection = document.getElementById('rentalSection');
    const tanggalMulaiInput = document.getElementById('tanggal_mulai_sewa');
    const tanggalAkhirInput = document.getElementById('tanggal_akhir_sewa');
    const qtyInput = document.getElementById('qty');
    
    if (select.value) {
        // Show barang info
        barangInfo.classList.remove('hidden');
        document.getElementById('infoNama').textContent = selectedOption.dataset.nama;
        document.getElementById('infoKode').textContent = selectedOption.dataset.kode;
        document.getElementById('infoKategori').textContent = selectedOption.dataset.kategori.replace('_', ' ');
        document.getElementById('infoStok').textContent = selectedOption.dataset.stock;
        
        // Set max qty
        qtyInput.max = selectedOption.dataset.stock;
        
        // Show/hide rental section based on kategori
        if (selectedOption.dataset.kategori === 'barang_sewa') {
            rentalSection.classList.remove('hidden');
            tanggalMulaiInput.required = true;
            tanggalAkhirInput.required = true;
            
            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            tanggalMulaiInput.min = today;
            tanggalAkhirInput.min = today;
        } else {
            rentalSection.classList.add('hidden');
            tanggalMulaiInput.required = false;
            tanggalAkhirInput.required = false;
            tanggalMulaiInput.value = '';
            tanggalAkhirInput.value = '';
        }
    } else {
        barangInfo.classList.add('hidden');
        rentalSection.classList.add('hidden');
    }
}

// Update tanggal akhir min when tanggal mulai changes
document.getElementById('tanggal_mulai_sewa')?.addEventListener('change', function() {
    const tanggalAkhirInput = document.getElementById('tanggal_akhir_sewa');
    tanggalAkhirInput.min = this.value;
    
    // Reset tanggal akhir if it's before tanggal mulai
    if (tanggalAkhirInput.value && tanggalAkhirInput.value < this.value) {
        tanggalAkhirInput.value = '';
    }
});

// Initialize on page load if old value exists
document.addEventListener('DOMContentLoaded', function() {
    updateBarangInfo();
});
</script>
@endsection
