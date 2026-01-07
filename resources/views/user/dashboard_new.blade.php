@extends('layouts.user')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang kembali!')

@section('content')
<!-- Welcome Card -->
<div class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] rounded-2xl shadow-lg p-8 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold mb-2 flex items-center gap-2">
                Selamat Datang, {{ Auth::user()->name }}!
            </h3>
            <p class="text-white/90 flex items-center gap-2">
                <x-heroicon-o-building-office class="w-5 h-5" />
                Sistem Informasi Inventaris PT PLN (Persero)
            </p>
        </div>
        <div class="hidden lg:block">
            <x-heroicon-o-clipboard-document-list class="w-24 h-24 opacity-20" />
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Barang -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#14a2ba]">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Total Barang</p>
                <h4 class="text-3xl font-bold text-[#14a2ba] mt-2">{{ $totalBarang }}</h4>
            </div>
            <div class="w-12 h-12 bg-[#14a2ba]/10 rounded-lg flex items-center justify-center">
                <x-heroicon-o-cube class="w-8 h-8 text-[#14a2ba]" />
            </div>
        </div>
        <p class="text-sm text-gray-500 flex items-center gap-1">
            <x-heroicon-o-archive-box class="w-4 h-4" />
            Total {{ $totalStok }} unit stok
        </p>
    </div>

    <!-- Stok Aman -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Aman</p>
                <h4 class="text-3xl font-bold text-green-600 mt-2">{{ $stokAman }}</h4>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-check-circle class="w-8 h-8 text-green-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok ≥ 10 unit</p>
    </div>

    <!-- Stok Menengah -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Menengah</p>
                <h4 class="text-3xl font-bold text-yellow-600 mt-2">{{ $stokMenengah }}</h4>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-exclamation-circle class="w-8 h-8 text-yellow-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok 5-9 unit</p>
    </div>

    <!-- Stok Kritis -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Kritis</p>
                <h4 class="text-3xl font-bold text-red-600 mt-2">{{ $stokKritis }}</h4>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-red-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok ≤ 4 unit</p>
    </div>
</div>

<!-- Rak Grid -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-5 h-5 text-[#14a2ba]" />
            Status Rak Inventaris
        </h3>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($raks as $rak)
        <a href="{{ route('user.stok-barang.index', ['rack' => $rak]) }}" class="bg-gradient-to-br from-gray-50 to-gray-100 hover:from-[#14a2ba]/10 hover:to-[#0d7a8f]/10 rounded-lg p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-md border border-gray-200 hover:border-[#14a2ba]">
            <div class="text-3xl font-bold text-[#14a2ba] mb-1">{{ strtoupper($rak) }}</div>
            <div class="text-xs text-gray-500 font-medium mb-2">Rak {{ strtoupper($rak) }}</div>
            <div class="text-sm font-semibold text-gray-700">
                {{ $barangPerRak->get($rak)->total ?? 0 }} items
            </div>
            <div class="text-xs text-gray-500">
                {{ $barangPerRak->get($rak)->total_stock ?? 0 }} unit
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Riwayat Pemakaian Terbaru -->
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-clock class="w-5 h-5 text-[#14a2ba]" />
            Riwayat Pemakaian Terbaru
        </h3>
        <a href="{{ route('user.pemakaian.index') }}" class="text-sm text-[#14a2ba] hover:text-[#0d7a8f] font-medium flex items-center gap-1">
            Lihat Semua
            <x-heroicon-o-arrow-right class="w-4 h-4" />
        </a>
    </div>
    
    @if($recentTransactions->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentTransactions as $trans)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $trans->tanggal->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $trans->namabarang_k }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $trans->qty }} unit
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $trans->penerima }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-3 opacity-30" />
        <p class="text-sm">Belum ada riwayat pemakaian</p>
    </div>
    @endif
</div>
@endsection
