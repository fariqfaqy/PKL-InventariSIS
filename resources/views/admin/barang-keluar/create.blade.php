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

            <!-- Pilih Barang -->
            <div>
                <label for="idbarang" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Barang <span class="text-red-500">*</span>
                </label>
                <select name="idbarang" id="idbarang" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('idbarang') border-red-500 @enderror">
                    <option value="">-- Pilih Barang yang Tersedia --</option>
                    @forelse($stocks as $stock)
                    <option value="{{ $stock->idbarang }}" 
                            data-stock="{{ $stock->stock }}"
                            {{ old('idbarang') == $stock->idbarang ? 'selected' : '' }}>
                        {{ $stock->kodebarang }} - {{ $stock->namabarang }} (Stok: {{ $stock->stock }})
                    </option>
                    @empty
                    <option value="" disabled>Tidak ada barang tersedia</option>
                    @endforelse
                </select>
                @error('idbarang')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Hanya menampilkan barang yang memiliki stok</p>
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
                    Penerima <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penerima" id="penerima" required
                    value="{{ old('penerima') }}"
                    placeholder="Masukkan nama penerima"
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
// Update max qty based on selected item stock
document.getElementById('idbarang').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    const qtyInput = document.getElementById('qty');
    const stockInfo = document.getElementById('stock-info');
    
    if (stock) {
        qtyInput.max = stock;
        stockInfo.textContent = `Stok tersedia: ${stock}`;
        stockInfo.classList.remove('text-red-500');
        stockInfo.classList.add('text-gray-500');
    } else {
        qtyInput.max = '';
        stockInfo.textContent = '';
    }
});

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
</script>
@endsection
