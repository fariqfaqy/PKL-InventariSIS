@extends('layouts.admin')

@section('title', 'Barang Keluar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Barang Keluar</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data barang keluar</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.barang-keluar.export-pdf') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
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
@endsection
