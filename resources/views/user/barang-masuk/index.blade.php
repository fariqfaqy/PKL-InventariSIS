@extends('layouts.user')

@section('title', 'Barang Masuk')
@section('subtitle', 'Lihat data barang masuk divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">Barang Masuk</h2>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-300">
                    <x-heroicon-o-eye class="w-3 h-3" />
                    Read Only
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="window.location.href='{{ route('user.barang-masuk.index') }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ !request('kategori') ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                </button>
                <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'aset_sewa']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'aset_sewa' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-computer-desktop class="w-4 h-4" />
                        Aset Sewa
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'material_umum']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'material_umum' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        Material Umum
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'aset_tetap']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('kategori') == 'aset_tetap' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-building-office class="w-4 h-4" />
                        Aset Tetap
                    </span>
                </button>
            </nav>
        </div>

        <!-- Sub-tabs untuk Material Umum -->
        @if(request('kategori') == 'material_umum')
            <div class="bg-blue-50 border-b border-blue-200">
                <nav class="flex -mb-px">
                    <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'material_umum']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua Material
                        </span>
                    </button>
                    <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_habis_pakai']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-archive-box class="w-3 h-3" />
                            Barang Habis Pakai
                        </span>
                    </button>
                    <button onclick="window.location.href='{{ route('user.barang-masuk.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_pinjam']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
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
        <form method="GET" action="{{ route('user.barang-masuk.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <!-- Filter Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    <span class="font-medium">Filter</span>
                </button>
                <a href="{{ route('user.barang-masuk.index', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori')])) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    <span class="font-medium">Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div id="table-container" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penginput</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($barangMasuk as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $barangMasuk->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->stock && $item->stock->kategori == 'aset_sewa')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <x-heroicon-o-computer-desktop class="w-3 h-3 mr-1" />
                                    Barang Sewa
                                </span>
                            @elseif($item->stock && $item->stock->kategori == 'aset_tetap')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-building-office class="w-3 h-3 mr-1" />
                                    Aset Tetap
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    <x-heroicon-o-shopping-bag class="w-3 h-3 mr-1" />
                                    Material Umum
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->kodebarang_m }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->namabarang_m }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                +{{ $item->qty }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($item->keterangan, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->penginput }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data barang masuk</p>
                                <p class="text-sm mt-1">Data barang masuk akan muncul setelah admin menambahkannya</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($barangMasuk->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $barangMasuk->links() }}
        </div>
        @endif
    </div>

<script>
(function() {
    const filterForm = document.querySelector('form[action="{{ route('user.barang-masuk.index') }}"]');
    const searchInput = filterForm.querySelector('input[name="search"]');
    const tanggalInput = filterForm.querySelector('input[name="tanggal"]');
    const tableContainer = document.getElementById('table-container');
    let searchTimeout, isLoading = false;
    
    function fetchData(url) {
        if (isLoading) return;
        isLoading = true;
        tableContainer.style.opacity = '0.6';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text()).then(html => {
            const newTable = new DOMParser().parseFromString(html, 'text/html').getElementById('table-container');
            if (newTable) { tableContainer.innerHTML = newTable.innerHTML; attachPaginationListeners(); }
            window.history.pushState({}, '', url);
            isLoading = false; tableContainer.style.opacity = '1';
        });
    }
    filterForm.addEventListener('submit', function(e) { e.preventDefault(); fetchData('{{ route('user.barang-masuk.index') }}?' + new URLSearchParams(new FormData(this))); });
    searchInput.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => filterForm.dispatchEvent(new Event('submit')), 500); });
    tanggalInput.addEventListener('change', () => filterForm.dispatchEvent(new Event('submit')));
    function attachPaginationListeners() { document.querySelectorAll('#table-container .pagination a').forEach(l => l.addEventListener('click', function(e) { e.preventDefault(); fetchData(this.href); window.scrollTo({ top: 0, behavior: 'smooth' }); })); }
    attachPaginationListeners();
})();
</script>
</div>
@endsection
