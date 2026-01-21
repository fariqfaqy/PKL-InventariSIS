@extends('layouts.user')

@section('title', 'Edit Barang di Rak')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 text-sm text-gray-600 mb-2">
            <a href="{{ route('user.barang-rak.index') }}" class="hover:text-[#14a2ba] transition-colors">Barang di Rak</a>
            <x-heroicon-o-chevron-right class="w-4 h-4" />
            <span class="text-gray-800 font-medium">Edit Barang</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Barang di Rak</h2>
        <p class="text-gray-600 mt-1">Ubah penempatan atau jumlah barang di rak</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('user.barang-rak.update', $assignment->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Info Barang (Read Only) -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-cube class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Informasi Barang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Kode Barang:</span>
                                <span class="font-mono font-medium text-gray-900 ml-2">{{ $assignment->stock->kodebarang }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Barang:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $assignment->stock->namabarang }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Kategori:</span>
                                @if($assignment->stock->kategori === 'barang_sewa')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full ml-2">Aset Sewa</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full ml-2">Habis Pakai</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-gray-600">Stok Total:</span>
                                <span class="font-bold text-gray-900 ml-2">{{ $assignment->stock->stock }}</span>
                            </div>
                            <div class="md:col-span-2 pt-2 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Di rak ini saat ini:</span>
                                    <span class="font-semibold text-blue-600">{{ $assignment->qty }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-gray-600">Di rak lain:</span>
                                    <span class="font-semibold text-orange-600">{{ $qtyInOtherRacks }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-1 pt-1 border-t border-gray-200">
                                    <span class="text-gray-700 font-medium">Maksimal untuk rak ini:</span>
                                    <span class="font-bold text-green-600 text-lg">{{ $availableStock }}</span>
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
                        <option value="{{ $rack }}" {{ old('rack', $assignment->rack) == $rack ? 'selected' : '' }}>Rak {{ strtoupper($rack) }}</option>
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
                <input type="number" name="qty" id="qty" min="1" max="{{ $availableStock }}" value="{{ old('qty', $assignment->qty) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('qty') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Maksimal: {{ $availableStock }} (berdasarkan stok tersedia)</p>
                @error('qty')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea name="keterangan" id="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent @error('keterangan') border-red-500 @enderror" placeholder="Tambahkan catatan jika diperlukan...">{{ old('keterangan', $assignment->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-200 font-medium">
                    Update
                </button>
                <a href="{{ route('user.barang-rak.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
