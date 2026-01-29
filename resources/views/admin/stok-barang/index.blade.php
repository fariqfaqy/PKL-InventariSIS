@extends('layouts.admin')

@section('title', 'Stok Barang')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Tabs Kategori (only show when accessed from "Semua" or directly, hide when from sidebar specific category) -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if(!request('kategori'))
        <!-- Show all tabs when no filter -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.stok-barang.index') }}" class="flex-1 px-6 py-4 text-center font-medium transition-all text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50">
                    Semua
                </a>
                <a href="{{ route('admin.stok-barang.index', ['kategori' => 'aset_sewa']) }}" class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    <x-heroicon-o-computer-desktop class="w-5 h-5" />
                    Aset Sewa
                </a>
                <a href="{{ route('admin.stok-barang.index', ['kategori' => 'material_umum']) }}" class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    <x-heroicon-o-shopping-bag class="w-5 h-5" />
                    Material Umum
                </a>
                <a href="{{ route('admin.stok-barang.index', ['kategori' => 'aset_tetap']) }}" class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                    Aset Tetap
                </a>
            </nav>
        </div>
        @else
        <!-- Show only active category header when filtered -->
        <div class="border-b border-gray-200 bg-cyan-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if(request('kategori') == 'aset_sewa')
                        <x-heroicon-o-computer-desktop class="w-6 h-6 text-cyan-600" />
                        <h2 class="text-lg font-semibold text-cyan-900">Aset Sewa</h2>
                    @elseif(request('kategori') == 'material_umum')
                        <x-heroicon-o-shopping-bag class="w-6 h-6 text-cyan-600" />
                        <h2 class="text-lg font-semibold text-cyan-900">Material Umum</h2>
                    @elseif(request('kategori') == 'aset_tetap')
                        <x-heroicon-o-building-office class="w-6 h-6 text-cyan-600" />
                        <h2 class="text-lg font-semibold text-cyan-900">Aset Tetap</h2>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.stok-barang.export-pdf', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori')])) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                        <span class="font-medium">Export PDF</span>
                    </a>
                    
                    <a href="{{ route('admin.stok-barang.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5" />
                        <span class="font-medium">Lihat Semua</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Sub-tabs untuk Material Umum -->
        @if(request('kategori') == 'material_umum')
            <div class="bg-blue-50 border-b border-blue-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('admin.stok-barang.index', ['kategori' => 'material_umum']) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua Material
                        </span>
                    </a>
                    <a href="{{ route('admin.stok-barang.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_habis_pakai']) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-archive-box class="w-3 h-3" />
                            Barang Habis Pakai
                        </span>
                    </a>
                    <a href="{{ route('admin.stok-barang.index', ['kategori' => 'material_umum', 'sub_kategori' => 'barang_pinjam']) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Barang Pinjam
                        </span>
                    </a>
                </nav>
            </div>
        @endif
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.stok-barang.index') }}" method="GET">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            @if(request('sub_kategori'))
                <input type="hidden" name="sub_kategori" value="{{ request('sub_kategori') }}">
            @endif
            
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
                    href="{{ route('admin.stok-barang.index', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori')])) }}" 
                    class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="font-medium">Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div id="table-container" class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kode Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        @if(request('kategori') != 'aset_sewa')
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Kategori</th>
                        @endif
                        @if(request('kategori') == 'aset_sewa')
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status Kondisi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Durasi</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Rak</th>
                        @if(request('kategori') != 'aset_sewa')
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Penginput</th>
                        @endif
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
                        @if(request('kategori') != 'aset_sewa')
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($stock->kategori === 'aset_sewa')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-computer-desktop class="w-3 h-3 mr-1" />
                                    Aset Sewa
                                </span>
                            @elseif($stock->kategori === 'barang_pinjam')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-arrow-path-rounded-square class="w-3 h-3 mr-1" />
                                    Barang Pinjam
                                </span>
                            @elseif($stock->kategori === 'aset_tetap')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                    <x-heroicon-o-building-office class="w-3 h-3 mr-1" />
                                    Aset Tetap
                                </span>
                            @elseif($stock->kategori === 'material_umum')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <x-heroicon-o-shopping-bag class="w-3 h-3 mr-1" />
                                    Material Umum
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <x-heroicon-o-question-mark-circle class="w-3 h-3 mr-1" />
                                    {{ ucfirst(str_replace('_', ' ', $stock->kategori)) }}
                                </span>
                            @endif
                        </td>
                        @endif
                        @if(request('kategori') == 'aset_sewa')
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock->status_kondisi_badge ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $stock->status_kondisi_label ?? 'Digunakan' }}
                                </span>
                                <button 
                                    onclick="openStatusModal({{ $stock->idbarang }}, '{{ $stock->namabarang }}', '{{ $stock->status_kondisi ?? 'digunakan' }}', '{{ $stock->keterangan_kondisi ?? '' }}')"
                                    class="text-blue-600 hover:text-blue-700"
                                    title="Update Status"
                                >
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @php
                                // Get active OutgoingTransaction for current user info
                                $activeTransaction = $stock->outgoingTransactions->first();
                            @endphp
                            @if($activeTransaction)
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800">{{ $activeTransaction->penerima }}</span>
                                    <span class="text-xs text-gray-500">{{ $activeTransaction->divisi }}</span>
                                    @if($activeTransaction->tanggal_mulai_pakai && $activeTransaction->tanggal_akhir_pakai)
                                    <span class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($activeTransaction->tanggal_mulai_pakai)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($activeTransaction->tanggal_akhir_pakai)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Tidak ada pengguna</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $activeTransaction = $stock->outgoingTransactions->first();
                            @endphp
                            @if($activeTransaction && $activeTransaction->tanggal_mulai_pakai && $activeTransaction->tanggal_akhir_pakai)
                                @php
                                    $start = \Carbon\Carbon::parse($activeTransaction->tanggal_mulai_pakai);
                                    $end = \Carbon\Carbon::parse($activeTransaction->tanggal_akhir_pakai);
                                    $daysTotal = $start->diffInDays($end);
                                    $daysRemaining = now()->diffInDays($end, false);
                                    $isExpired = $daysRemaining < 0;
                                @endphp
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $isExpired ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $daysTotal }} hari
                                    </span>
                                    @if($isExpired)
                                        <span class="text-xs text-red-600 font-medium mt-1">
                                            Lewat {{ abs($daysRemaining) }} hari
                                        </span>
                                    @elseif($daysRemaining >= 0)
                                        <span class="text-xs text-gray-500 mt-1">
                                            Sisa {{ $daysRemaining }} hari
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        @endif
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
                        @if(request('kategori') != 'aset_sewa')
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $stock->penginput }}
                        </td>
                        @endif
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
                                <form action="{{ route('admin.stok-barang.destroy', $stock->idbarang) }}" method="POST" class="inline" onsubmit="return customConfirm(event, 'Yakin ingin menghapus stok barang ini? Data yang terhapus tidak dapat dikembalikan.', {type: 'danger', title: 'Hapus Stok Barang', confirmText: 'Ya, Hapus'})">
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

    <!-- AJAX Live Search & Pagination Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[action="{{ route('admin.stok-barang.index') }}"]');
        const searchInput = filterForm.querySelector('input[name="search"]');
        const rackSelect = filterForm.querySelector('select[name="rack"]');
        const statusSelect = filterForm.querySelector('select[name="status"]');
        const tableContainer = document.getElementById('table-container');
        
        let searchTimeout;
        let isLoading = false;
        
        // AJAX function to fetch and update table
        function fetchData(url) {
            if (isLoading) return;
            isLoading = true;
            
            // Show loading state
            tableContainer.style.opacity = '0.6';
            tableContainer.style.pointerEvents = 'none';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');
                
                if (newTable) {
                    tableContainer.innerHTML = newTable.innerHTML;
                    attachPaginationListeners();
                }
                
                // Update URL
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
        
        // Handle form submit
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            fetchData('{{ route('admin.stok-barang.index') }}?' + params.toString());
        });
        
        // Live search on typing
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.dispatchEvent(new Event('submit'));
            }, 500);
        });
        
        // Live filter on change
        [rackSelect, statusSelect].forEach(element => {
            element.addEventListener('change', () => {
                filterForm.dispatchEvent(new Event('submit'));
            });
        });
        
        // Attach pagination listeners
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

<!-- Status Kondisi Modal -->
<div id="statusModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 z-50 flex items-center justify-center transition-all duration-300 opacity-0" onclick="closeStatusModal()">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Update Status Kondisi</h3>
            <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>
        
        <form id="statusForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">Barang: <span id="statusBarangName" class="font-semibold text-gray-800"></span></p>
            </div>
            
            <div class="mb-4">
                <label for="status_kondisi" class="block text-sm font-medium text-gray-700 mb-2">Status Kondisi</label>
                <select id="status_kondisi" name="status_kondisi" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    <option value="digunakan">Digunakan</option>
                    <option value="diperbaiki">Diperbaiki</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="keterangan_kondisi" class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                <textarea id="keterangan_kondisi" name="keterangan_kondisi" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                    placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeStatusModal()"
                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    Simpan
                </button>
            </div>
        </form>
    </div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 z-50 flex items-center justify-center transition-all duration-300 opacity-0" onclick="closeQRModal()">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
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
        const modal = document.getElementById('qrModal');
        modal.classList.remove('hidden');
        document.getElementById('qrCodeText').textContent = kode;
        document.getElementById('qrCodeContainer').innerHTML = qrElement.innerHTML;
        
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
        }, 10);
    }
}

function closeQRModal() {
    const modal = document.getElementById('qrModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Modal Status Kondisi
function openStatusModal(idbarang, namabarang, status, keterangan) {
    const modal = document.getElementById('statusModal');
    modal.classList.remove('hidden');
    document.getElementById('statusBarangName').textContent = namabarang;
    document.getElementById('statusForm').action = `/admin/stok-barang/${idbarang}/update-status`;
    document.getElementById('status_kondisi').value = status;
    document.getElementById('keterangan_kondisi').value = keterangan;
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }, 10);
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Close modal dengan ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQRModal();
        closeStatusModal();
    }
});
</script>
</div>
@endsection
