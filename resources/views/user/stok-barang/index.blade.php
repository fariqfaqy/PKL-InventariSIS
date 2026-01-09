@extends('layouts.user')

@section('title', 'Stok Barang')
@section('subtitle', 'Lihat data stok barang yang tersedia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Stok Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Data stok barang yang tersedia di inventaris</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-4">
        <form method="GET" action="{{ route('user.stok-barang.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <a href="{{ route('user.stok-barang.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300">
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
                            @if(isset($rackAssignments[$stock->idbarang]) && $rackAssignments[$stock->idbarang]->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($rackAssignments[$stock->idbarang] as $assignment)
                                        @if($assignment->qty > 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ strtoupper($assignment->rack) }} ({{ $assignment->qty }})
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                    Belum di rak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('user.stok-barang.show', $stock->idbarang) }}" class="inline-flex items-center gap-1 text-[#14a2ba] hover:text-[#0d7a8f] transition-colors" title="Detail">
                                <x-heroicon-o-eye class="w-5 h-5" />
                                <span class="text-xs">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
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
@endsection
