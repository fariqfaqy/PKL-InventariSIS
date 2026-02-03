@extends('layouts.admin')

@section('title', 'Detail Permintaan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Permintaan #{{ $permintaan->id_request }}</h1>
            <p class="text-gray-600 mt-2">Informasi lengkap permintaan barang</p>
        </div>
        <a href="{{ route('admin.permintaan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
            <x-heroicon-o-arrow-left class="h-5 w-5 inline" /> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Status Permintaan</h2>
                <div class="flex items-center justify-between">
                    <span class="px-4 py-2 text-lg font-semibold rounded-full bg-{{ $permintaan->status_color }}-100 text-{{ $permintaan->status_color }}-800">
                        {{ $permintaan->status_label }}
                    </span>
                    @if($permintaan->diproses_oleh)
                        <div class="text-sm text-gray-600">
                            <p>Diproses oleh: <span class="font-medium">{{ $permintaan->diproses_oleh }}</span></p>
                            <p>Tanggal: {{ $permintaan->tanggal_diproses?->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Pegawai</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium">{{ $permintaan->user->name }}</span>
                    </div>
                    @if($permintaan->user->nip)
                    <div class="flex justify-between">
                        <span class="text-gray-600">NIP:</span>
                        <span class="font-medium font-mono">{{ $permintaan->user->nip }}</span>
                    </div>
                    @endif
                    @if($permintaan->user->division)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Divisi:</span>
                        <span class="font-medium">{{ $permintaan->user->division->nama_divisi }}</span>
                    </div>
                    @endif
                    @if($permintaan->user->jabatan)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jabatan:</span>
                        <span class="font-medium">{{ $permintaan->user->jabatan }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $permintaan->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Request:</span>
                        <span class="font-medium">{{ $permintaan->tanggal_request->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Item Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Barang</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama Barang:</span>
                        <span class="font-medium">{{ $permintaan->stock->namabarang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kode Barang:</span>
                        <span class="font-medium">{{ $permintaan->stock->kodebarang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium capitalize">{{ str_replace('_', ' ', $permintaan->stock->kategori) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipe Request:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $permintaan->tipe_request == 'pinjam_material' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $permintaan->tipe_request_label }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Diminta:</span>
                        <span class="font-medium text-lg text-blue-600">{{ $permintaan->qty }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Penerima:</span>
                        <span class="font-medium">{{ $permintaan->penerima ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Stok Tersedia:</span>
                        <span class="font-medium text-lg {{ $permintaan->stock->stock >= $permintaan->qty ? 'text-green-600' : 'text-red-600' }}">
                            {{ $permintaan->stock->stock }}
                        </span>
                    </div>
                    @if($permintaan->parent_request_id)
                    <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-arrow-path class="w-4 h-4 text-orange-600" />
                            <span class="font-medium text-orange-800">Request Perubahan</span>
                        </div>
                        <div class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Request Asli:</span>
                                <a href="{{ route('admin.permintaan.show', $permintaan->parent_request_id) }}" class="text-blue-600 hover:underline font-medium">#{{ $permintaan->parent_request_id }}</a>
                            </div>
                            @if($permintaan->parentRequest)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Qty Asli:</span>
                                <span class="font-medium">{{ $permintaan->parentRequest->qty }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Qty Baru:</span>
                                <span class="font-medium">{{ $permintaan->qty }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Perubahan:</span>
                                @php
                                    $diff = $permintaan->qty - $permintaan->parentRequest->qty;
                                @endphp
                                <span class="font-medium {{ $diff > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ $diff }} ({{ $diff > 0 ? 'tambahan keluar' : 'dikembalikan' }})
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Rental/Loan Dates (only for pinjam_material with dates) -->
            @if($permintaan->tipe_request === 'pinjam_material' && $permintaan->tanggal_mulai_sewa && $permintaan->tanggal_akhir_sewa)
                <div class="bg-blue-50 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        Periode Peminjaman
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pinjam:</span>
                            <span class="font-medium">{{ $permintaan->tanggal_mulai_sewa?->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Kembali:</span>
                            <span class="font-medium">{{ $permintaan->tanggal_akhir_sewa?->format('d/m/Y') }}</span>
                        </div>
                        @if($permintaan->tanggal_mulai_sewa && $permintaan->tanggal_akhir_sewa)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Durasi:</span>
                                <span class="font-medium">{{ $permintaan->tanggal_mulai_sewa->diffInDays($permintaan->tanggal_akhir_sewa) }} hari</span>
                            </div>
                        @endif
                        
                        @php
                            $now = now();
                            $isOverdue = $permintaan->tanggal_akhir_sewa && $permintaan->tanggal_akhir_sewa->lt($now);
                            $daysRemaining = $permintaan->tanggal_akhir_sewa ? $now->diffInDays($permintaan->tanggal_akhir_sewa, false) : null;
                        @endphp
                        
                        @if($permintaan->status === 'approved')
                            <div class="flex justify-between items-center pt-3 border-t border-blue-200">
                                <span class="text-gray-600">Status:</span>
                                @if($isOverdue)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline" />
                                        Terlambat {{ abs($daysRemaining) }} hari
                                    </span>
                                @elseif($daysRemaining <= 3)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <x-heroicon-o-clock class="w-4 h-4 inline" />
                                        {{ $daysRemaining }} hari lagi
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        <x-heroicon-o-check-circle class="w-4 h-4 inline" />
                                        Masih {{ $daysRemaining }} hari
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Extend Rental Button (only for approved rentals) -->
                    @if($permintaan->status === 'approved')
                        <div class="mt-4 pt-4 border-t border-blue-200">
                            <button type="button" onclick="document.getElementById('extendRentalModal').classList.remove('hidden')" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <x-heroicon-o-arrow-path class="h-5 w-5 mr-2" />
                                Perpanjang Peminjaman
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Purpose & Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Keperluan & Catatan</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Keperluan:</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $permintaan->keperluan }}</p>
                    </div>
                    @if($permintaan->catatan_user)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Tambahan:</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $permintaan->catatan_user }}</p>
                        </div>
                    @endif
                    @if($permintaan->catatan_admin)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Admin:</label>
                            <p class="text-gray-900 bg-red-50 p-3 rounded border border-red-200">{{ $permintaan->catatan_admin }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
                
                @if($permintaan->status === 'pending')
                    <!-- Approve Button -->
                    <form action="{{ route('admin.permintaan.approve', $permintaan->id_request) }}" method="POST" class="mb-3">
                        @csrf
                        @php
                            // Tentukan pesan konfirmasi berdasarkan tipe request
                            if ($permintaan->parent_request_id && $permintaan->parentRequest) {
                                // Cek apakah ini pembatalan atau perubahan
                                $isPembatalan = stripos($permintaan->keperluan, 'PEMBATALAN') !== false;
                                
                                if ($isPembatalan) {
                                    // Request pembatalan
                                    $confirmMsg = "Request PEMBATALAN: User akan mengembalikan {$permintaan->qty} unit. Stok akan bertambah {$permintaan->qty} unit. Request asli akan dibatalkan. Setujui?";
                                } else {
                                    // Request perubahan qty
                                    $diff = $permintaan->qty - $permintaan->parentRequest->qty;
                                    if ($diff > 0) {
                                        $confirmMsg = "Request perubahan: User meminta TAMBAHAN {$diff} unit. Stok akan berkurang {$diff} unit. Setujui?";
                                    } elseif ($diff < 0) {
                                        $returnQty = abs($diff);
                                        $confirmMsg = "Request perubahan: User akan MENGEMBALIKAN {$returnQty} unit. Stok akan bertambah {$returnQty} unit. Setujui?";
                                    } else {
                                        $confirmMsg = "Request perubahan tanpa perubahan qty (mungkin ubah tanggal). Setujui?";
                                    }
                                }
                            } else {
                                // Request biasa
                                $confirmMsg = "Setujui permintaan ini dan berikan barang ke user? Stok akan berkurang {$permintaan->qty} unit.";
                            }
                        @endphp
                        <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center"
                                onclick="return customConfirm(event, '{{ $confirmMsg }}', {type: 'success', title: 'Setujui Permintaan', confirmText: 'Ya, Setujui'})">
                            <x-heroicon-o-check-circle class="h-5 w-5 mr-2" />
                            Setujui
                        </button>
                    </form>

                    <!-- Reject Button with Modal -->
                    <button type="button" onclick="openRejectModal()" 
                            class="w-full px-4 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center justify-center">
                        <x-heroicon-o-x-circle class="h-5 w-5 mr-2" />
                        Tolak
                    </button>
                
                @elseif($permintaan->status === 'approved' && $permintaan->tipe_request === 'pinjam_material')
                    <!-- Complete Rental Button (untuk barang pinjam yang sudah approved) -->
                    <form action="{{ route('admin.permintaan.complete-rental', $permintaan->id_request) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center mb-3"
                                onclick="return customConfirm(event, 'Tandai peminjaman ini sebagai selesai? Barang akan dikembalikan dan stok akan bertambah {{ $permintaan->qty }} unit.', {type: 'success', title: 'Selesaikan Peminjaman', confirmText: 'Ya, Selesai'})">
                            <x-heroicon-o-check-badge class="h-5 w-5 mr-2" />
                            Selesaikan Peminjaman
                        </button>
                    </form>
                    
                    <div class="text-center text-gray-500 py-2 text-xs">
                        <x-heroicon-o-information-circle class="h-4 w-4 inline mb-1" />
                        <p>Klik tombol di atas ketika barang sudah dikembalikan</p>
                    </div>
                
                @else
                    <div class="text-center text-gray-500 py-4">
                        <x-heroicon-o-information-circle class="h-12 w-12 mx-auto mb-2 text-gray-400" />
                        <p class="text-sm">Permintaan sudah diproses</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 z-50 flex items-center justify-center transition-all duration-300 opacity-0" onclick="closeRejectModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Tolak Permintaan</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <form id="rejectForm" action="{{ route('admin.permintaan.reject', $permintaan->id_request) }}" method="POST"
              onsubmit="return customConfirm(event, 'Yakin menolak permintaan ini? Tindakan tidak dapat dibatalkan.', {type: 'danger', title: 'Tolak Permintaan', confirmText: 'Ya, Tolak'})">
            @csrf
            <div class="mb-6">
                <label for="catatan_admin" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="catatan_admin" name="catatan_admin" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Masukkan alasan penolakan..."></textarea>
                <p class="mt-1 text-sm text-gray-500">Alasan akan dikirimkan ke pemohon</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" 
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    }, 10);
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
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
        closeRejectModal();
    }
});
</script>

<!-- Extend Rental Modal -->
@if($permintaan->status === 'approved' && $permintaan->tipe_request === 'pinjam_material' && $permintaan->tanggal_akhir_sewa)
<div id="extendRentalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-arrow-path class="h-6 w-6 text-blue-600" />
                <h3 class="text-lg font-bold text-gray-900">Perpanjang Peminjaman</h3>
            </div>
            
            <form action="{{ route('admin.permintaan.extend-rental', $permintaan->id_request) }}" method="POST">
                @csrf
                
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Tanggal Kembali Saat Ini:</span><br>
                        <span class="text-lg font-bold text-blue-600">{{ $permintaan->tanggal_akhir_sewa->format('d/m/Y') }}</span>
                    </p>
                </div>
                
                <div class="mb-4">
                    <label for="tanggal_akhir_sewa_baru" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kembali Baru<span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_akhir_sewa_baru" 
                           name="tanggal_akhir_sewa_baru" 
                           min="{{ \Carbon\Carbon::parse($permintaan->tanggal_akhir_sewa)->addDay()->format('Y-m-d') }}"
                           required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        <x-heroicon-o-information-circle class="w-3 h-3 inline" />
                        Tanggal baru harus setelah {{ $permintaan->tanggal_akhir_sewa->format('d/m/Y') }}
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center">
                        <x-heroicon-o-arrow-path class="h-4 w-4 mr-2" />
                        Perpanjang
                    </button>
                    <button type="button" onclick="document.getElementById('extendRentalModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
