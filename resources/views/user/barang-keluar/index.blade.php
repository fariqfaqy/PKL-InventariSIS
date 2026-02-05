@extends('layouts.user')

@section('title', 'Barang Keluar')
@section('subtitle', 'Lihat semua pemakaian barang divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-300">
                    <x-heroicon-o-eye class="w-3 h-3" />
                    Read Only
                </span>
            </div>
        </div>
    </div>

    <!-- Category Header - USER HANYA LIHAT MATERIAL UMUM -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header Material Umum -->
        <div class="border-b border-gray-200 bg-cyan-50 px-6 py-4">
            <div class="flex items-center gap-3">
                <x-heroicon-o-shopping-bag class="w-6 h-6 text-cyan-600" />
                <h2 class="text-lg font-semibold text-cyan-900">Material Umum</h2>
            </div>
        </div>

        <!-- Sub-tabs untuk Material Umum -->
        <div class="bg-blue-50 border-b border-blue-200">
            <nav class="flex -mb-px">
                <button onclick="window.location.href='{{ route('user.barang-keluar.index') }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                    <span class="inline-flex items-center gap-1">
                        <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                        Semua Material
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.barang-keluar.index', ['sub_kategori' => 'barang_habis_pakai']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                    <span class="inline-flex items-center gap-1">
                        <x-heroicon-o-archive-box class="w-3 h-3" />
                        Barang Habis Pakai
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.barang-keluar.index', ['sub_kategori' => 'barang_pinjam']) }}'" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                    <span class="inline-flex items-center gap-1">
                        <x-heroicon-o-arrow-path class="w-3 h-3" />
                        Barang Pinjam
                    </span>
                </button>
            </nav>
        </div>

        <!-- Filter Section -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('user.barang-keluar.index') }}" method="GET">
                @if(request('sub_kategori'))
                    <input type="hidden" name="sub_kategori" value="{{ request('sub_kategori') }}">
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Cari Barang / Penerima -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Barang / Penerima</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Kode, nama barang, atau penerima..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                        >
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input 
                            type="date" 
                            name="tanggal" 
                            value="{{ request('tanggal') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                        >
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
                        href="{{ route('user.barang-keluar.index', array_filter(['sub_kategori' => request('sub_kategori')])) }}" 
                        class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        <span class="font-medium">Reset</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div id="table-container" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kode Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Penginput</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($barangKeluar as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $barangKeluar->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->kodebarang_k }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $item->namabarang_k }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                -{{ $item->qty }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $item->penerima }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->penginput }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('user.barang-keluar.show', $item->idkeluar) }}" class="text-[#14a2ba] hover:text-[#0d7a8f] transition-colors" title="Detail">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data barang keluar untuk Material Umum</p>
                                <p class="text-sm mt-1">Data pemakaian barang Material Umum akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $barangKeluar->links() }}
        </div>
    </div>

<!-- AJAX Live Search & Pagination Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form[action="{{ route('user.barang-keluar.index') }}"]');
    const searchInput = filterForm.querySelector('input[name="search"]');
    const tanggalInput = filterForm.querySelector('input[name="tanggal"]');
    const tableContainer = document.getElementById('table-container');
    
    let searchTimeout;
    let isLoading = false;
    
    function fetchData(url) {
        if (isLoading) return;
        isLoading = true;
        
        tableContainer.style.opacity = '0.6';
        tableContainer.style.pointerEvents = 'none';
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('table-container');
            
            if (newTable) {
                tableContainer.innerHTML = newTable.innerHTML;
                attachPaginationListeners();
            }
            
            window.history.pushState({}, '', url);
            isLoading = false;
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        })
        .catch(error => {
            console.error('Error:', error);
            isLoading = false;
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        });
    }
    
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        fetchData('{{ route('user.barang-keluar.index') }}?' + params.toString());
    });
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => filterForm.dispatchEvent(new Event('submit')), 500);
    });
    
    tanggalInput.addEventListener('change', () => filterForm.dispatchEvent(new Event('submit')));
    
    function attachPaginationListeners() {
        document.querySelectorAll('#table-container .pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchData(this.href);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }
    
    attachPaginationListeners();
});
</script>
</div>
@endsection
