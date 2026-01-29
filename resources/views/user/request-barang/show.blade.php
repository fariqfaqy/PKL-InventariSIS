@extends('layouts.user')

@section('title', 'Detail Request')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Request #{{ $request->id_request }}</h1>
            <p class="text-gray-600 mt-2">Informasi lengkap permintaan Anda</p>
        </div>
        <a href="{{ route('user.request-barang.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
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
                    <span class="px-4 py-2 text-lg font-semibold rounded-full bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                        {{ $request->status_label }}
                    </span>
                    <div class="text-sm text-gray-600">
                        <p>Tanggal Request: {{ $request->tanggal_request->format('d/m/Y H:i') }}</p>
                        @if($request->tanggal_diproses)
                            <p>Diproses: {{ $request->tanggal_diproses->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Status Description -->
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    @if($request->status === 'pending')
                        <p class="text-sm text-gray-700">⏳ Permintaan Anda sedang menunggu persetujuan dari admin.</p>
                    @elseif($request->status === 'approved')
                        <p class="text-sm text-green-700">✅ Permintaan Anda telah disetujui dan sedang menunggu diproses.</p>
                    @elseif($request->status === 'processing')
                        <p class="text-sm text-blue-700">🔄 Permintaan Anda sedang diproses. Barang akan segera tersedia.</p>
                    @elseif($request->status === 'completed')
                        <p class="text-sm text-purple-700">🎉 Permintaan Anda telah selesai diproses!</p>
                    @elseif($request->status === 'rejected')
                        <p class="text-sm text-red-700">❌ Permintaan Anda ditolak. Lihat catatan admin di bawah.</p>
                    @endif
                </div>
            </div>

            <!-- Item Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Barang</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama Barang:</span>
                        <span class="font-medium">{{ $request->stock->namabarang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kode Barang:</span>
                        <span class="font-medium">{{ $request->stock->kodebarang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium">Material Umum</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sub-Kategori:</span>
                        <span class="font-medium">
                            {{ $request->stock->sub_kategori == 'barang_habis_pakai' ? 'Barang Habis Pakai' : 'Barang Pinjam' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah:</span>
                        <span class="font-medium text-lg text-blue-600">{{ $request->qty }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipe Request:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $request->tipe_request == 'pinjam_material' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $request->tipe_request_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Rental/Loan Dates (if applicable) -->
            @if($request->tipe_request === 'pinjam_material')
                <div class="bg-blue-50 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        Periode Peminjaman
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pinjam:</span>
                            <span class="font-medium">{{ $request->tanggal_mulai_sewa?->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Kembali:</span>
                            <span class="font-medium">{{ $request->tanggal_akhir_sewa?->format('d/m/Y') }}</span>
                        </div>
                        @if($request->tanggal_mulai_sewa && $request->tanggal_akhir_sewa)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Durasi:</span>
                                <span class="font-medium">{{ $request->tanggal_mulai_sewa->diffInDays($request->tanggal_akhir_sewa) }} hari</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Purpose & Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Keperluan & Catatan</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Keperluan:</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $request->keperluan }}</p>
                    </div>
                    @if($request->catatan_user)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Catatan Anda:</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ $request->catatan_user }}</p>
                        </div>
                    @endif
                    @if($request->catatan_admin)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Catatan dari Admin:</label>
                            <p class="text-gray-900 bg-red-50 p-3 rounded border border-red-200">{{ $request->catatan_admin }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Aksi</h2>
                
                @if($request->status === 'pending')
                    <form action="{{ route('user.request-barang.destroy', $request->id_request) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center justify-center mb-3"
                                onclick="return customConfirm(event, 'Hapus permintaan ini? Data yang terhapus tidak dapat dikembalikan.', {type: 'danger', title: 'Hapus Permintaan', confirmText: 'Ya, Hapus'})">
                            <x-heroicon-o-trash class="h-5 w-5 mr-2" />
                            Hapus Permintaan
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 text-center">Anda hanya bisa menghapus permintaan yang masih pending</p>
                @else
                    <div class="text-center text-gray-500">
                        <x-heroicon-o-information-circle class="h-12 w-12 mx-auto mb-2 text-gray-400" />
                        <p class="text-sm">Permintaan tidak dapat dihapus karena sudah diproses oleh admin</p>
                    </div>
                @endif

                @if($request->diproses_oleh)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 mb-1">Diproses oleh:</p>
                        <p class="font-medium">{{ $request->diproses_oleh }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
