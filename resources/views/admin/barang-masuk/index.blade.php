@extends('layouts.admin')

@section('title', 'Kelola Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Barang Masuk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data barang masuk</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.barang-masuk.export-pdf', array_filter(['kategori' => request('kategori'), 'sub_kategori' => request('sub_kategori'), 'search' => request('search'), 'tanggal' => request('tanggal')])) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                <span class="font-medium">Export PDF</span>
            </a>
            <a href="{{ route('admin.barang-masuk.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                <x-heroicon-o-plus class="w-5 h-5" />
                <span class="font-medium">Tambah Barang Masuk</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Category Tabs -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="flex border-b border-gray-200">
            <a href="{{ route('admin.barang-masuk.index', ['search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all {{ !request('kategori') ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                Semua
            </a>
            <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'barang_sewa', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'barang_sewa' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-computer-desktop class="w-5 h-5" />
                Aset Sewa
            </a>
            <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'habis_pakai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'habis_pakai' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-shopping-bag class="w-5 h-5" />
                Material Umum
            </a>
            <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'aset_tetap', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-all flex items-center justify-center gap-2 {{ request('kategori') == 'aset_tetap' ? 'text-cyan-600 border-b-2 border-cyan-600 bg-cyan-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <x-heroicon-o-building-office class="w-5 h-5" />
                Aset Tetap
            </a>
        </div>

        <!-- Sub-tabs untuk Material Umum -->
        @if(request('kategori') == 'habis_pakai')
            <div class="bg-blue-50 border-b border-blue-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'habis_pakai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ !request('sub_kategori') ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-squares-2x2 class="w-3 h-3" />
                            Semua Material
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'habis_pakai', 'sub_kategori' => 'barang_habis_pakai', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_habis_pakai' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-archive-box class="w-3 h-3" />
                            Barang Habis Pakai
                        </span>
                    </a>
                    <a href="{{ route('admin.barang-masuk.index', ['kategori' => 'habis_pakai', 'sub_kategori' => 'barang_pinjam', 'search' => request('search'), 'tanggal' => request('tanggal')]) }}" class="flex-1 py-3 px-4 text-center border-b-2 font-medium text-xs transition-colors {{ request('sub_kategori') == 'barang_pinjam' ? 'border-blue-500 text-blue-700' : 'border-transparent text-blue-600 hover:text-blue-800 hover:border-blue-300' }}">
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
            <form action="{{ route('admin.barang-masuk.index') }}" method="GET">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                @if(request('sub_kategori'))
                    <input type="hidden" name="sub_kategori" value="{{ request('sub_kategori') }}">
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                        href="{{ route('admin.barang-masuk.index', ['kategori' => request('kategori')]) }}" 
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
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kode Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Penginput</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($barangMasuk as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $barangMasuk->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->kodebarang_m }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $item->namabarang_m }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                +{{ $item->qty }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ Str::limit($item->keterangan, 40) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->penginput }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.barang-masuk.show', $item->idmasuk) }}" class="text-[#14a2ba] hover:text-[#0d7a8f] transition-colors" title="Detail">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                                <form action="{{ route('admin.barang-masuk.destroy', $item->idmasuk) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                                <p class="text-lg font-medium">Belum ada data barang masuk</p>
                                <p class="text-sm mt-1">Klik tombol "Tambah Barang Masuk" untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangMasuk->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $barangMasuk->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
