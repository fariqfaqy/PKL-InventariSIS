@extends('layouts.user')

@section('title', 'Pemakaian Barang')
@section('subtitle', 'Request pemakaian barang divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pemakaian Barang Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Request permintaan dan peminjaman barang</p>
        </div>
        <a href="{{ route('user.pemakaian.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
            <x-heroicon-o-plus class="w-5 h-5" />
            <span class="font-medium">Ajukan Request Baru</span>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-o-x-circle class="w-5 h-5" />
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Approval</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pemakaian as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pemakaian->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->tipe_request == 'peminjaman')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                    Peminjaman
                                </span>
                                @if($item->tanggal_pinjam && $item->tanggal_kembali)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $item->tanggal_pinjam->format('d/m') }} - {{ $item->tanggal_kembali->format('d/m/Y') }}
                                    </div>
                                @endif
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->qty }} unit
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->penerima }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->status_approval == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                    Menunggu
                                </span>
                            @elseif($item->status_approval == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Disetujui
                                </span>
                                @if($item->tanggal_diproses)
                                    <div class="text-xs text-gray-500 mt-1">{{ $item->tanggal_diproses->format('d/m/Y H:i') }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                    Ditolak
                                </span>
                                @if($item->catatan_admin)
                                    <div class="text-xs text-red-600 mt-1">{{ $item->catatan_admin }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                @if($item->status_approval == 'pending')
                                    <!-- Tombol Hapus untuk pending request -->
                                    <form action="{{ route('user.pemakaian.destroy', $item->idkeluar) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan request ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Batalkan Request">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </form>
                                @elseif($item->status_approval == 'approved')
                                    <!-- View detail -->
                                    <a href="{{ route('user.pemakaian.show', $item->idkeluar) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="Lihat Detail">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </a>
                                @else
                                    <!-- Rejected - View Detail -->
                                    <a href="{{ route('user.pemakaian.show', $item->idkeluar) }}" class="text-gray-600 hover:text-gray-700 transition-colors" title="Lihat Detail">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-inbox class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada request pemakaian</p>
                                <p class="text-sm mt-1">Klik tombol "Ajukan Request Baru" untuk mengajukan permintaan atau peminjaman barang</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pemakaian->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pemakaian->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
