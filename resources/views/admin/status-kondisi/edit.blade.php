<!-- Modal Form untuk Update Status Kondisi -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Update Status Kondisi</h3>
            <button onclick="window.history.back()" class="text-gray-400 hover:text-gray-600">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <form action="{{ route('admin.status-kondisi.update', $barang->idbarang) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informasi Barang -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Kode: <span class="font-semibold">{{ $barang->kodebarang }}</span></p>
                <p class="text-sm text-gray-600">Nama: <span class="font-semibold">{{ $barang->namabarang }}</span></p>
                <p class="text-sm text-gray-600">Status Saat Ini: 
                    <span class="font-semibold">
                        @if($barang->status_kondisi === 'digunakan')
                            Sedang Digunakan
                        @elseif($barang->status_kondisi === 'rusak')
                            Rusak
                        @elseif($barang->status_kondisi === 'diperbaiki')
                            Sedang Diperbaiki
                        @else
                            {{ $barang->status_kondisi }}
                        @endif
                    </span>
                </p>
            </div>

            <!-- Status Kondisi -->
            <div class="mb-4">
                <label for="status_kondisi" class="block text-sm font-medium text-gray-700 mb-2">
                    Status Kondisi Baru <span class="text-red-500">*</span>
                </label>
                <select name="status_kondisi" id="status_kondisi" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    <option value="">-- Pilih Status --</option>
                    <option value="digunakan" {{ old('status_kondisi', $barang->status_kondisi) === 'digunakan' ? 'selected' : '' }}>
                        Sedang Digunakan
                    </option>
                    <option value="rusak" {{ old('status_kondisi', $barang->status_kondisi) === 'rusak' ? 'selected' : '' }}>
                        Rusak
                    </option>
                    <option value="diperbaiki" {{ old('status_kondisi', $barang->status_kondisi) === 'diperbaiki' ? 'selected' : '' }}>
                        Sedang Diperbaiki
                    </option>
                </select>
                @error('status_kondisi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan Kondisi -->
            <div class="mb-6">
                <label for="keterangan_kondisi" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan (Opsional)
                </label>
                <textarea name="keterangan_kondisi" id="keterangan_kondisi" rows="3"
                          placeholder="Tambahkan keterangan jika diperlukan..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">{{ old('keterangan_kondisi', $barang->keterangan_kondisi) }}</textarea>
                @error('keterangan_kondisi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alert Info -->
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                    <strong>Catatan:</strong> Jika status diubah ke "Rusak" atau "Sedang Diperbaiki", barang akan otomatis ditarik dari user.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button type="button" onclick="window.history.back()" 
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-800 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
