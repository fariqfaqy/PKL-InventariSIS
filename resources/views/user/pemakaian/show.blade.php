@extends('layouts.user')

@section('title', 'Detail Pemakaian')
@section('subtitle', 'Detail informasi pemakaian barang')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('user.pemakaian.index') }}" class="inline-flex items-center gap-2 text-[#14a2ba] hover:text-[#0d7a8f] font-medium">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            <span>Kembali ke Daftar Pemakaian</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] rounded-2xl shadow-lg p-8 text-white">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-3xl font-bold">Detail Pemakaian</h2>
                    @php
                        // Tentukan status aktual berdasarkan OutgoingTransaction dan periode sewa
                        $actualStatus = 'pending';
                        $statusLabel = 'Pending';
                        $statusColor = 'bg-yellow-500';
                        
                        if ($outgoingTransaction) {
                            // Jika ada transaksi keluar, cek statusnya
                            if ($outgoingTransaction->status == 'sedang_dipakai') {
                                // Cek apakah masih dalam periode sewa
                                if ($request->tanggal_akhir_sewa && now()->startOfDay()->lte($request->tanggal_akhir_sewa)) {
                                    $actualStatus = 'sedang_dipakai';
                                    $statusLabel = 'Sedang Digunakan';
                                    $statusColor = 'bg-blue-500';
                                } else {
                                    // Sudah lewat tanggal akhir sewa
                                    $actualStatus = 'expired';
                                    $statusLabel = 'Masa Sewa Berakhir';
                                    $statusColor = 'bg-orange-500';
                                }
                            } elseif ($outgoingTransaction->status == 'selesai') {
                                $actualStatus = 'selesai';
                                $statusLabel = 'Selesai';
                                $statusColor = 'bg-green-500';
                            }
                        } elseif ($request->status == 'approved') {
                            $actualStatus = 'approved';
                            $statusLabel = 'Disetujui';
                            $statusColor = 'bg-green-500';
                        } elseif ($request->status == 'pending') {
                            $actualStatus = 'pending';
                            $statusLabel = 'Menunggu Approval';
                            $statusColor = 'bg-yellow-500';
                        } elseif ($request->status == 'rejected') {
                            $actualStatus = 'rejected';
                            $statusLabel = 'Ditolak';
                            $statusColor = 'bg-red-500';
                        } elseif ($request->status == 'cancelled') {
                            $actualStatus = 'cancelled';
                            $statusLabel = 'Dibatalkan';
                            $statusColor = 'bg-gray-500';
                        } elseif ($request->status == 'completed') {
                            $actualStatus = 'completed';
                            $statusLabel = 'Selesai';
                            $statusColor = 'bg-green-500';
                        }
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColor }} text-white">
                        {{ $statusLabel }}
                    </span>
                </div>
                <p class="text-white/90 text-lg">Request ID: #{{ $request->id_request }}</p>
            </div>
            <div class="hidden lg:block">
                <x-heroicon-o-clipboard-document-check class="w-24 h-24 opacity-20" />
            </div>
        </div>
    </div>

    <!-- Main Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Barang -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4 pb-4 border-b">
                <x-heroicon-o-cube class="w-6 h-6 text-[#14a2ba]" />
                <h3 class="text-xl font-bold text-gray-800">Informasi Barang</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Nama Barang</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $request->stock->namabarang }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Kode Barang</label>
                    <p class="text-lg font-mono text-gray-700">{{ $request->stock->kodebarang }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Sub-Kategori</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $request->stock->sub_kategori == 'barang_habis_pakai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        @if($request->stock->sub_kategori == 'barang_habis_pakai')
                            <x-heroicon-o-archive-box class="w-4 h-4 mr-1" />
                            Barang Habis Pakai
                        @else
                            <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                            Barang Pinjam
                        @endif
                    </span>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Tipe Request</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $request->tipe_request == 'pinjam_material' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $request->tipe_request_label }}
                    </span>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Jumlah</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $request->qty }} unit</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Penerima</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $request->penerima ?? '-' }}</p>
                </div>

                @if($request->stock->kategori == 'aset_sewa' && $request->stock->durasi_sewa)
                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-1">Durasi Sewa</label>
                    <p class="text-lg font-semibold text-purple-600">{{ $request->stock->durasi_sewa }} Tahun</p>
                </div>
                @endif
            </div>

            <!-- Periode Peminjaman untuk Barang Pinjam -->
            @if($request->tipe_request === 'pinjam_material' && $request->tanggal_mulai_sewa && $request->tanggal_akhir_sewa)
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-5 h-5 text-blue-600" />
                    Periode Peminjaman
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                        <label class="text-xs font-medium text-blue-700 block mb-1">
                            Tanggal Pinjam
                        </label>
                        <p class="text-lg font-bold text-blue-900">{{ $request->tanggal_mulai_sewa->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
                        <label class="text-xs font-medium text-purple-700 block mb-1">
                            Tanggal Kembali
                        </label>
                        <p class="text-lg font-bold text-purple-900">{{ $request->tanggal_akhir_sewa->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                        <label class="text-xs font-medium text-green-700 block mb-1">Total Durasi</label>
                        @php
                            $durasi = $request->tanggal_mulai_sewa->diffInDays($request->tanggal_akhir_sewa) + 1;
                        @endphp
                        <p class="text-lg font-bold text-green-900">{{ $durasi }} hari</p>
                    </div>
                </div>

                @if($request->status == 'approved')
                    @php
                        $today = now()->startOfDay();
                        $sisaHari = $today->diffInDays($request->tanggal_akhir_sewa->startOfDay(), false);
                        $persenSelesai = $durasi > 0 ? min(100, max(0, (($durasi - $sisaHari) / $durasi) * 100)) : 0;
                    @endphp
                    <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                Progress Peminjaman
                            </span>
                            <span class="text-sm font-semibold {{ $sisaHari < 0 ? 'text-red-600' : ($sisaHari <= 7 ? 'text-yellow-600' : 'text-green-600') }}">
                                @if($sisaHari >= 0)
                                    {{ $sisaHari }} hari lagi
                                @else
                                    Sudah berakhir
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-300 {{ $sisaHari < 0 ? 'bg-red-500' : ($sisaHari <= 7 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                 style="width: {{ min(100, $persenSelesai) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Keperluan -->
            <div class="mt-6 pt-6 border-t">
                <label class="text-sm font-medium text-gray-500 block mb-2">Keperluan</label>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700">{{ $request->keperluan }}</p>
                </div>
            </div>

            @if($request->catatan_user)
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-500 block mb-2">Catatan Tambahan</label>
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <p class="text-gray-700">{{ $request->catatan_user }}</p>
                </div>
            </div>
            @endif

            @if($request->catatan_admin)
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-500 block mb-2">Catatan Admin</label>
                <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                    <p class="text-gray-700">{{ $request->catatan_admin }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Timeline & Status -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-2 mb-4 pb-4 border-b">
                    <x-heroicon-o-clock class="w-6 h-6 text-[#14a2ba]" />
                    <h3 class="text-xl font-bold text-gray-800">Timeline</h3>
                </div>

                <div class="space-y-4">
                    <!-- Request Created -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <x-heroicon-o-document-plus class="w-5 h-5 text-blue-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Request Dibuat</p>
                            <p class="text-xs text-gray-500">{{ $request->tanggal_request->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-400 mt-1">Oleh: {{ $request->user->name }}</p>
                        </div>
                    </div>

                    @if($request->status == 'pending')
                    <!-- Pending -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center animate-pulse">
                                <x-heroicon-o-clock class="w-5 h-5 text-yellow-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Menunggu Approval</p>
                            <p class="text-xs text-gray-500">Request sedang direview oleh admin</p>
                        </div>
                    </div>
                    @endif

                    @if($request->status == 'approved' || $request->status == 'completed')
                    <!-- Approved -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Disetujui</p>
                            @if($request->tanggal_diproses)
                                <p class="text-xs text-gray-500">{{ $request->tanggal_diproses->format('d/m/Y H:i') }}</p>
                            @endif
                            @if($request->diproses_oleh)
                                <p class="text-xs text-gray-400 mt-1">Oleh: {{ $request->diproses_oleh }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($request->status == 'rejected' || $request->status == 'cancelled')
                    <!-- Rejected/Cancelled -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <x-heroicon-o-x-circle class="w-5 h-5 text-red-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $request->status == 'rejected' ? 'Ditolak' : 'Dibatalkan' }}</p>
                            @if($request->tanggal_diproses)
                                <p class="text-xs text-gray-500">{{ $request->tanggal_diproses->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($request->status == 'completed')
                    <!-- Completed -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <x-heroicon-o-check-badge class="w-5 h-5 text-purple-600" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Selesai</p>
                            @if($outgoingTransaction && $outgoingTransaction->tanggal_selesai)
                                <p class="text-xs text-gray-500">{{ $outgoingTransaction->tanggal_selesai->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($request->status == 'approved')
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-2 mb-4 pb-4 border-b">
                    <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-[#14a2ba]" />
                    <h3 class="text-xl font-bold text-gray-800">Aksi</h3>
                </div>

                <div class="space-y-3">
                    @if($outgoingTransaction && $outgoingTransaction->status == 'sedang_dipakai')
                    <form action="{{ route('user.pemakaian.selesai', $request->id_request) }}" method="POST" onsubmit="return customConfirm(event, 'Apakah Anda yakin pemakaian barang ini sudah selesai?', {type: 'success', title: 'Tandai Selesai', confirmText: 'Ya, Selesai'})">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            Tandai Selesai
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('user.request-barang.edit', $request->id_request) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                        Request Perubahan
                    </a>

                    <form action="{{ route('user.request-barang.request-cancel', $request->id_request) }}" method="POST" onsubmit="return customConfirm(event, 'Request pembatalan akan diajukan ke admin. Yakin ingin melanjutkan?', {type: 'warning', title: 'Request Pembatalan', confirmText: 'Ya, Ajukan'})">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                            Request Pembatalan
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- History Request Perubahan -->
    @if($request->changeRequests->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center gap-2 mb-4 pb-4 border-b">
            <x-heroicon-o-document-text class="w-6 h-6 text-[#14a2ba]" />
            <h3 class="text-xl font-bold text-gray-800">Riwayat Request Perubahan</h3>
        </div>

        <div class="space-y-4">
            @foreach($request->changeRequests as $change)
            <div class="border rounded-lg p-4 {{ $change->status == 'approved' ? 'bg-green-50 border-green-200' : ($change->status == 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Request #{{ $change->id_request }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium {{ $change->status == 'approved' ? 'bg-green-200 text-green-800' : ($change->status == 'rejected' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800') }}">
                            {{ $change->status_label }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-500">{{ $change->tanggal_request->format('d/m/Y H:i') }}</span>
                </div>

                <div class="text-sm text-gray-700 mb-2">
                    <strong>Keperluan:</strong> {{ $change->keperluan }}
                </div>

                @if($change->catatan_user)
                <div class="text-sm text-gray-700 mb-2">
                    <strong>Catatan:</strong> {{ $change->catatan_user }}
                </div>
                @endif

                @if($change->catatan_admin)
                <div class="text-sm text-gray-700 bg-white p-3 rounded mt-2">
                    <strong>Respon Admin:</strong> {{ $change->catatan_admin }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
