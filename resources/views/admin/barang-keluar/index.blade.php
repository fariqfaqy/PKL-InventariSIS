@extends('layouts.admin')

@section('title', 'Kelola Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Barang Keluar</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data barang keluar</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.barang-keluar.export-pdf', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori'), 'search' => request('search'), 'tanggal' => request('tanggal')])) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                <span class="font-medium">Export PDF</span>
            </a>
            <a href="{{ route('admin.barang-keluar.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                <x-heroicon-o-plus class="w-5 h-5" />
                <span class="font-medium">Tambah Barang Keluar</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Tabs Kategori -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="flex border-b border-gray-200">
            <a href="{{ route('admin.barang-keluar.index', ['search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all {{ !request('kategori') ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'aset_sewa', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'aset_sewa' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-computer-desktop class="w-5 h-5" />
                Aset Sewa
            </a>
            <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'material_umum', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'material_umum' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-shopping-bag class="w-5 h-5" />
                Material Umum
            </a>
            <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'aset_tetap', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'aset_tetap' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-building-office class="w-5 h-5" />
                Aset Tetap
            </a>
        </div>

        <!-- Sub-tabs untuk Aset Sewa -->
        @if(request('kategori') == 'aset_sewa')
            <div class="bg-purple-50 border-b border-purple-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'aset_sewa', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('status_filter') ? 'border-purple-500 text-purple-700' : 'border-transparent text-purple-600 hover:text-purple-800 hover:border-purple-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'aset_sewa', 'status_filter' => 'sedang_dipakai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('status_filter') == 'sedang_dipakai' ? 'border-purple-500 text-purple-700' : 'border-transparent text-purple-600 hover:text-purple-800 hover:border-purple-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Sedang Dipakai
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'aset_sewa', 'status_filter' => 'selesai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('status_filter') == 'selesai' ? 'border-purple-500 text-purple-700' : 'border-transparent text-purple-600 hover:text-purple-800 hover:border-purple-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-check-circle class="w-3 h-3" />
                            Selesai
                        </span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- Sub-tabs untuk Material Umum -->
        @if(request('kategori') == 'material_umum')
            <div class="bg-blue-50 border-b border-blue-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'material_umum', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua Material
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_habis_pakai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-archive-box class="w-3 h-3" />
                            Barang Habis Pakai
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-keluar.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_pinjam', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Barang Pinjam
                        </span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('admin.barang-keluar.index') }}" method="GET">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                @if(request('sub_kategori'))
                    <input type="hidden" name="sub_kategori" value="{{ request('sub_kategori') }}">
                @endif
                @if(request('status_filter'))
                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
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
                        href="{{ route('admin.barang-keluar.index', ['tipe' => request('tipe')]) }}" 
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
                        @if($selectedKategori === 'aset_sewa')
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Penginput</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
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
                        @if($selectedKategori === 'aset_sewa')
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($item->tanggal_mulai_pakai && $item->tanggal_akhir_pakai)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $startDate = \Carbon\Carbon::parse($item->tanggal_mulai_pakai);
                                    $endDate = \Carbon\Carbon::parse($item->tanggal_akhir_pakai);
                                    $totalDays = $startDate->diffInDays($endDate);
                                    $daysLeft = $now->diffInDays($endDate, false);
                                    $isExpired = $daysLeft < 0;
                                @endphp
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-600">{{ $totalDays }} hari total</p>
                                    @if($isExpired)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            <x-heroicon-o-exclamation-circle class="w-3 h-3 mr-1" />
                                            Lewat {{ abs(floor($daysLeft)) }} hari
                                        </span>
                                    @elseif($daysLeft <= 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                            {{ ceil($daysLeft) }} hari lagi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <x-heroicon-o-check class="w-3 h-3 mr-1" />
                                            {{ ceil($daysLeft) }} hari lagi
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($item->status === 'sedang_dipakai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Aktif
                                </span>
                            @elseif($item->status === 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            @elseif($item->status === 'ditarik')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Ditarik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </td>
                        @endif
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->penginput }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.barang-keluar.show', $item->idkeluar) }}" class="text-[#14a2ba] hover:text-[#0d7a8f] transition-colors" title="Detail">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                                <form action="{{ route('admin.barang-keluar.destroy', $item->idkeluar) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data barang keluar</p>
                                <p class="text-sm mt-1">Klik tombol "Tambah Barang Keluar" untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangKeluar->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $barangKeluar->links() }}
        </div>
        @endif
    </div>
</div>

<!-- AJAX Live Search & Pagination Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form[action="{{ route('admin.barang-keluar.index') }}"]');
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
        fetchData('{{ route('admin.barang-keluar.index') }}?' + params.toString());
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
