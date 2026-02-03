@extends('layouts.admin')

@section('title', 'Barang Keluar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.barang-keluar.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Barang Keluar</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap transaksi barang keluar</p>
            </div>
        </div>
    </div>

    @if($barangKeluar->stock && $barangKeluar->stock->kategori === 'aset_sewa')
    <!-- Aset Sewa Info Card with Actions -->
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl shadow-md p-6 border-2 border-purple-200">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-computer-desktop class="w-6 h-6 text-purple-600" />
                Informasi Pemakaian Aset Sewa
            </h3>
            @if($barangKeluar->status === 'sedang_dipakai')
            <div class="flex gap-2">
                <button onclick="openExtendModal()" 
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                    Perpanjang
                </button>
                <form action="{{ route('admin.barang-keluar.complete-rental', $barangKeluar->idkeluar) }}" method="POST" 
                      onsubmit="return customConfirm(event, 'Yakin ingin menyelesaikan pemakaian aset sewa ini?', {type: 'success', title: 'Selesaikan Pemakaian', confirmText: 'Ya, Selesaikan'})">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-1.5" />
                        Selesaikan
                    </button>
                </form>
            </div>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Pengguna</label>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                        {{ substr($barangKeluar->user->name ?? substr($barangKeluar->penerima, 0, 1), 0, 1) }}
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold text-lg">{{ $barangKeluar->user->name ?? $barangKeluar->penerima }}</p>
                        @if($barangKeluar->user && $barangKeluar->user->division)
                        <p class="text-sm text-gray-600">{{ $barangKeluar->user->division->nama_divisi }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Status Pemakaian</label>
                @if($barangKeluar->status === 'sedang_dipakai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-blue-100 text-blue-800">
                        <x-heroicon-o-arrow-path class="w-5 h-5 mr-2 animate-spin" />
                        Sedang Digunakan
                    </span>
                @elseif($barangKeluar->status === 'selesai')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-green-100 text-green-800">
                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                        Selesai
                    </span>
                @elseif($barangKeluar->status === 'ditarik')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-red-100 text-red-800">
                        <x-heroicon-o-x-circle class="w-5 h-5 mr-2" />
                        Ditarik
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold bg-gray-100 text-gray-800">
                        {{ ucfirst($barangKeluar->status) }}
                    </span>
                @endif
            </div>
            
            <!-- Rental Period -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Periode Pemakaian</label>
                <div class="space-y-2">
                    @if($barangKeluar->tanggal_mulai_pakai)
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                        <span class="text-gray-600">Mulai:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($barangKeluar->tanggal_akhir_pakai)
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                        <span class="text-gray-600">Berakhir:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Duration Info -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-600 mb-2">Durasi</label>
                @if($barangKeluar->tanggal_mulai_pakai && $barangKeluar->tanggal_akhir_pakai)
                    @php
                        $startDate = \Carbon\Carbon::parse($barangKeluar->tanggal_mulai_pakai)->startOfDay();
                        $endDate = \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->startOfDay();
                        $totalDays = $startDate->diffInDays($endDate) + 1;
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                        <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                        {{ $totalDays }} hari
                    </span>
                @else
                    <span class="text-sm text-gray-400">-</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-[#14a2ba]" />
            Informasi Transaksi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->kodebarang_k }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->namabarang_k }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Keluar</label>
                <p class="text-gray-800">{{ \Carbon\Carbon::parse($barangKeluar->tanggal)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah</label>
                <p class="text-2xl font-bold text-red-600">-{{ $barangKeluar->qty }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penerima</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->penerima }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $barangKeluar->penginput }}</p>
            </div>
            @if($barangKeluar->durasi_sewa)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Durasi Sewa</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->durasi_sewa }} Tahun</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Expire</label>
                <p class="text-red-600 font-bold">
                    {{ \Carbon\Carbon::parse($barangKeluar->tanggal)->addYears($barangKeluar->durasi_sewa)->format('d/m/Y') }}
                </p>
            </div>
            @endif
        </div>
    </div>

    @if($barangKeluar->stock)
    <!-- Stock Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-cube class="w-6 h-6 text-[#14a2ba]" />
            Informasi Stok Terkait
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->stock->kodebarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $barangKeluar->stock->namabarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $barangKeluar->stock->stock > 10 ? 'bg-green-100 text-green-800' : ($barangKeluar->stock->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $barangKeluar->stock->stock }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Rak</label>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                    {{ strtoupper($barangKeluar->stock->rack) }}
                </span>
            </div>
            @if($barangKeluar->stock->kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                @if($barangKeluar->stock->kategori === 'aset_sewa')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <x-heroicon-o-computer-desktop class="w-4 h-4 mr-1.5" />
                        Aset Sewa
                    </span>
                @elseif($barangKeluar->stock->kategori === 'aset_tetap')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                        <x-heroicon-o-building-office class="w-4 h-4 mr-1.5" />
                        Aset Tetap
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        <x-heroicon-o-shopping-bag class="w-4 h-4 mr-1.5" />
                        Material Umum
                    </span>
                @endif
            </div>
            @endif
            @if($barangKeluar->stock->kategori === 'material_umum' && $barangKeluar->stock->sub_kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Sub-Kategori</label>
                @if($barangKeluar->stock->sub_kategori === 'barang_habis_pakai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <x-heroicon-o-archive-box class="w-4 h-4 mr-1.5" />
                        Barang Habis Pakai
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                        Barang Pinjam
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.barang-keluar.index') }}" 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Kembali
        </a>
        <form action="{{ route('admin.barang-keluar.destroy', $barangKeluar->idkeluar) }}" method="POST" 
            onsubmit="return customConfirm(event, 'Yakin ingin menghapus history transaksi ini? Data ini hanya akan dihapus dari history, stok tidak akan terpengaruh.', {type: 'danger', title: 'Hapus History Transaksi', confirmText: 'Ya, Hapus'})">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-trash class="w-5 h-5" /> Hapus Transaksi
            </button>
        </form>
    </div>

<!-- Extend Rental Modal -->
@if($barangKeluar->stock && $barangKeluar->stock->kategori === 'aset_sewa' && $barangKeluar->status === 'sedang_dipakai')
<div id="extendModal" class="hidden fixed inset-0 bg-black/20 backdrop-blur-md z-[9999] flex items-center justify-center transition-all duration-300 opacity-0" onclick="closeExtendModal()">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-blue-600" />
                Perpanjang Masa Sewa
            </h3>
            <button onclick="closeExtendModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>
        
        <form action="{{ route('admin.barang-keluar.extend-rental', $barangKeluar->idkeluar) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-xl border border-blue-100">
                    <p class="text-xs text-gray-600 mb-1">Tanggal Berakhir Sekarang:</p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}
                    </p>
                </div>
                
                <div>
                    <label for="tanggal_akhir_pakai" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Berakhir Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_akhir_pakai" 
                           name="tanggal_akhir_pakai" 
                           min="{{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->addDay()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                        <x-heroicon-o-information-circle class="w-3.5 h-3.5" />
                        Harus setelah {{ \Carbon\Carbon::parse($barangKeluar->tanggal_akhir_pakai)->format('d M Y') }}
                    </p>
                </div>
                
                @if($errors->has('tanggal_akhir_pakai'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg text-sm flex items-start gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <span>{{ $errors->first('tanggal_akhir_pakai') }}</span>
                </div>
                @endif
                
                <div class="flex gap-3 pt-2">
                    <button type="button" 
                            onclick="closeExtendModal()"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium">
                        Perpanjang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openExtendModal() {
    const modal = document.getElementById('extendModal');
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }, 10);
}

function closeExtendModal() {
    const modal = document.getElementById('extendModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExtendModal();
    }
});

// Auto-open modal if there's validation error
@if($errors->has('tanggal_akhir_pakai'))
    openExtendModal();
@endif
</script>
@endif
</div>
@endsection
