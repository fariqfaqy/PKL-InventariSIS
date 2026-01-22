@extends('layouts.user')

@section('title', 'Stok Barang')
@section('subtitle', 'Lihat data stok barang yang tersedia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">Stok Barang</h2>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-300">
                    <x-heroicon-o-eye class="w-3 h-3" />
                    Read Only
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs Kategori -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="window.location.href='{{ route('user.stok-barang.index') }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ !request('kategori') ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                </button>
                <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'barang_sewa']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'barang_sewa' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-computer-desktop class="w-4 h-4" />
                        Aset Sewa
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'habis_pakai']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'habis_pakai' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        Material Umum
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'aset_tetap']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'aset_tetap' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-building-office class="w-4 h-4" />
                        Aset Tetap
                    </span>
                </button>
            </nav>
        </div>

        <!-- Sub-tabs untuk Material Umum -->
        @if(request('kategori') == 'habis_pakai')
            <div class="bg-blue-50 border-b border-blue-200">
                <nav class="flex -mb-px">
                    <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'habis_pakai']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua Material
                        </span>
                    </button>
                    <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'habis_pakai', 'sub_kategori' => 'barang_habis_pakai']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-archive-box class="w-3 h-3" />
                            Barang Habis Pakai
                        </span>
                    </button>
                    <button onclick="window.location.href='{{ route('user.stok-barang.index', ['kategori' => 'habis_pakai', 'sub_kategori' => 'barang_pinjam']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Barang Pinjam
                        </span>
                    </button>
                </nav>
            </div>
        @endif
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <form method="GET" action="{{ route('user.stok-barang.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            @if(request('sub_kategori'))
                <input type="hidden" name="sub_kategori" value="{{ request('sub_kategori') }}">
            @endif
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Barang</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode atau nama barang..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
            </div>

            <!-- Filter Rak -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Rak</label>
                <select name="rack" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    <option value="">Semua Rak</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack }}" {{ request('rack') == $rack ? 'selected' : '' }}>Rak {{ strtoupper($rack) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Stok -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman (≥10)</option>
                    <option value="menengah" {{ request('status') == 'menengah' ? 'selected' : '' }}>Menengah (5-9)</option>
                    <option value="kritis" {{ request('status') == 'kritis' ? 'selected' : '' }}>Kritis (≤4)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    <span class="font-medium">Filter</span>
                </button>
                <a href="{{ route('user.stok-barang.index', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori')])) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span class="font-medium">Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rak</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $index => $stock)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $stocks->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $stock->kodebarang }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->image)
                                <img src="{{ asset('images/barang/' . $stock->image) }}" alt="{{ $stock->namabarang }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                    <x-heroicon-o-photo class="w-8 h-8 text-gray-400" />
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $stock->namabarang }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($stock->deskripsi, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock->stock >= 10 ? 'bg-green-100 text-green-800' : ($stock->stock >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $stock->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($stock->rack)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ strtoupper($stock->rack) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                    Belum di rak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button onclick="openQRModal('qr-{{ $stock->idbarang }}', '{{ route('user.stok-barang.show', $stock->idbarang) }}')" class="inline-flex flex-col items-center gap-1 text-[#14a2ba] hover:text-[#0d7a8f] transition-colors cursor-pointer" title="QR Code">
                                <div class="inline-block p-1 bg-white border-2 border-gray-300 rounded hover:border-[#14a2ba] transition-colors">
                                    {!! QrCode::size(30)->generate(route('user.stok-barang.show', $stock->idbarang)) !!}
                                </div>
                                <span class="text-xs">Scan</span>
                            </button>
                            <!-- Hidden large QR code -->
                            <div id="qr-{{ $stock->idbarang }}" class="hidden">
                                {!! QrCode::size(250)->generate(route('user.stok-barang.show', $stock->idbarang)) !!}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data stok barang</p>
                                <p class="text-sm mt-1">Stok barang akan muncul setelah admin menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
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
            <div id="qrCodeContainer" class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-xl inline-block border-2 border-dashed border-blue-300">
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
