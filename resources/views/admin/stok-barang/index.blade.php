@extends('layouts.admin')

@section('title', 'Stok Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                @if(isset($kategori))
                    @if($kategori == 'barang_sewa')
                        Stok Barang Sewa
                    @elseif($kategori == 'aset_tetap')
                        Stok Aset Tetap
                    @else
                        Stok Barang Habis Pakai
                    @endif
                @else
                    Stok Barang - Semua Kategori
                @endif
            </h2>
            <p class="text-sm text-gray-500 mt-1">Daftar stok barang</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.stok-barang.export-pdf', ['kategori' => request('kategori')]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                <span class="font-medium">Export PDF</span>
            </a>
            
            @if(isset($kategori))
            <a href="{{ route('admin.stok-barang.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <x-heroicon-o-arrows-right-left class="w-5 h-5" />
                <span class="font-medium">Lihat Semua</span>
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.stok-barang.index') }}" method="GET">
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Cari Barang -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Barang</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Kode atau nama barang..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Filter Rak -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Rak</label>
                    <select 
                        name="rack" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                    >
                        <option value="">Semua Rak</option>
                        @foreach($availableRacks as $rak)
                            <option value="{{ $rak }}" {{ request('rack') == $rak ? 'selected' : '' }}>
                                Rak {{ strtoupper($rak) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                    <select 
                        name="status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                    >
                        <option value="">Semua Status</option>
                        <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman (≥10)</option>
                        <option value="menengah" {{ request('status') == 'menengah' ? 'selected' : '' }}>Menengah (5-9)</option>
                        <option value="kritis" {{ request('status') == 'kritis' ? 'selected' : '' }}>Kritis (≤4)</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-800 transition-all duration-300 shadow-md hover:shadow-lg"
                >
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    <span class="font-medium">Filter</span>
                </button>
                
                <a 
                    href="{{ route('admin.stok-barang.index', ['kategori' => request('kategori')]) }}" 
                    class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="font-medium">Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kode Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Rak</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Penginput</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $index => $stock)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $stocks->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $stock->kodebarang }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($stock->image)
                                <img src="{{ asset('images/barang/' . $stock->image) }}" alt="{{ $stock->namabarang }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                    <x-heroicon-o-photo class="w-8 h-8 text-gray-400" />
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $stock->namabarang }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($stock->kategori === 'barang_sewa')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-computer-desktop class="w-3 h-3 mr-1" />
                                    Barang Sewa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <x-heroicon-o-shopping-bag class="w-3 h-3 mr-1" />
                                    Habis Pakai
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock->stock > 10 ? 'bg-green-100 text-green-800' : ($stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $stock->stock }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ strtoupper($stock->rack) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $stock->penginput }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.stok-barang.show', $stock->idbarang) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="Detail">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                                <button onclick="openQRModal('qr-{{ $stock->idbarang }}', '{{ $stock->kodebarang }}')" class="text-[#14a2ba] hover:text-[#0d7a8f] transition-colors cursor-pointer" title="QR Code">
                                    <div class="inline-block p-1 bg-white border-2 border-gray-300 rounded hover:border-[#14a2ba] transition-colors">
                                        {!! QrCode::size(30)->generate(route('admin.stok-barang.show', $stock->idbarang)) !!}
                                    </div>
                                </button>
                                <!-- Hidden large QR code -->
                                <div id="qr-{{ $stock->idbarang }}" class="hidden">
                                    {!! QrCode::size(250)->generate(route('admin.stok-barang.show', $stock->idbarang)) !!}
                                </div>
                                <form action="{{ route('admin.stok-barang.destroy', $stock->idbarang) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus stok barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Hapus">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data stok barang</p>
                                <p class="text-sm mt-1">Gunakan "Barang Masuk" untuk menambah stok baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $stocks->links() }}
        </div>
        @endif
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" onclick="closeQRModal()">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl" onclick="event.stopPropagation()">
        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-800 mb-2">QR Code Produk</h3>
            <p class="text-sm text-gray-500 mb-6">Scan untuk lihat detail produk</p>
            <div id="qrCodeContainer" class="bg-gray-50 p-6 rounded-xl inline-block">
                <!-- QR Code will be inserted here -->
            </div>
            <p id="qrCodeText" class="text-xs text-gray-600 font-mono mt-4 bg-gray-100 px-4 py-2 rounded"></p>
            <button onclick="closeQRModal()" class="mt-6 px-6 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function openQRModal(qrId, kode) {
    const qrElement = document.getElementById(qrId);
    if (qrElement) {
        document.getElementById('qrModal').classList.remove('hidden');
        document.getElementById('qrCodeText').textContent = kode;
        document.getElementById('qrCodeContainer').innerHTML = qrElement.innerHTML;
    }
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

// Close modal dengan ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQRModal();
    }
});
</script>
@endsection
