@extends('layouts.user')

@section('title', 'Barang Keluar')
@section('subtitle', 'Lihat semua pemakaian barang divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">Barang Keluar</h2>
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
                <button onclick="window.location.href='{{ route('user.barang-keluar.index') }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ !request('tipe') ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                </button>
                <button onclick="window.location.href='{{ route('user.barang-keluar.index', ['tipe' => 'peminjaman']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('tipe') == 'peminjaman' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-computer-desktop class="w-4 h-4" />
                        Peminjaman (Sewa)
                    </span>
                </button>
                <button onclick="window.location.href='{{ route('user.barang-keluar.index', ['tipe' => 'permintaan']) }}'" class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ request('tipe') == 'permintaan' ? 'border-[#14a2ba] text-[#14a2ba]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <span class="inline-flex items-center gap-2">
                        <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        Permintaan (Habis Pakai)
                    </span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <form method="GET" action="{{ route('user.barang-keluar.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(isset($tipe))
                <input type="hidden" name="tipe" value="{{ $tipe }}">
            @endif
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Barang / Penerima</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, nama barang, atau penerima..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
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
                <a href="{{ route('user.barang-keluar.index', isset($tipe) ? ['tipe' => $tipe] : []) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode Sewa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penginput</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($barangKeluar as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $barangKeluar->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->kategori == 'barang_sewa')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                    Peminjaman
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-document-text class="w-3 h-3 mr-1" />
                                    Permintaan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->kodebarang_k }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->namabarang_k }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                -{{ $item->qty }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($item->kategori == 'barang_sewa' && ($item->tanggal_mulai_sewa || $item->tanggal_akhir_sewa))
                                <div class="space-y-1">
                                    @if($item->tanggal_mulai_sewa)
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="text-gray-500">Mulai:</span>
                                            <span class="font-medium text-blue-600">{{ $item->tanggal_mulai_sewa->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if($item->tanggal_akhir_sewa)
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="text-gray-500">Kembali:</span>
                                            <span class="font-medium text-orange-600">{{ $item->tanggal_akhir_sewa->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if($item->tanggal_mulai_sewa && $item->tanggal_akhir_sewa)
                                        <div class="text-xs text-gray-500">
                                            ({{ $item->tanggal_mulai_sewa->diffInDays($item->tanggal_akhir_sewa) }} hari)
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->penerima }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center gap-1">
                                {{ $item->penginput }}
                                @if($item->penginput == auth()->user()->email)
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">Anda</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->status == 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Selesai
                                </span>
                                @if($item->tanggal_selesai)
                                    <div class="text-xs text-gray-500 mt-1">{{ $item->tanggal_selesai->format('d/m/Y') }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sedang Dipakai
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data barang keluar</p>
                                <p class="text-sm mt-1">Data barang keluar akan muncul saat ada pemakaian barang</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($barangKeluar->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $barangKeluar->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
