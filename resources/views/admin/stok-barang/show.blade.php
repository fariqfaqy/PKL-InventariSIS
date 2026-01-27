@extends('layouts.admin')

@section('title', 'Detail Stok Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.stok-barang.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Stok Barang</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap stok barang</p>
            </div>
        </div>
    </div>

    <!-- Main Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <!-- Gambar Barang (Full Width) -->
        @if($stock->image)
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/barang/' . $stock->image) }}" 
                 alt="{{ $stock->namabarang }}" 
                 class="w-full max-w-md h-64 object-cover rounded-xl border-2 border-gray-200 shadow-lg">
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- QR Code Section -->
            <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                <div class="mb-4 text-center">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">QR Code Produk</h4>
                    <p class="text-xs text-gray-500">Scan untuk lihat detail</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    {!! QrCode::size(200)->generate(route('admin.stok-barang.show', $stock->idbarang)) !!}
                </div>
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded">{{ $stock->kodebarang }}</p>
                </div>
            </div>

            <!-- Info Section -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Barang</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Barang</label>
                <p class="text-gray-800 font-semibold">{{ $stock->kodebarang }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Barang</label>
                <p class="text-gray-800 font-semibold">{{ $stock->namabarang }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-800">{{ $stock->deskripsi ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Stok Saat Ini</label>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold 
                        @if($stock->stock > 10) text-green-600
                        @elseif($stock->stock > 0) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $stock->stock }}
                    </span>
                    <span class="text-sm text-gray-500">unit</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi Rak</label>
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Rak {{ strtoupper($stock->rack) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                @if($stock->kategori === 'aset_sewa')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <x-heroicon-o-computer-desktop class="w-4 h-4 mr-1.5" />
                        Aset Sewa
                    </span>
                @elseif($stock->kategori === 'material_umum')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        <x-heroicon-o-shopping-bag class="w-4 h-4 mr-1.5" />
                        Material Umum
                    </span>
                @elseif($stock->kategori === 'aset_tetap')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                        <x-heroicon-o-building-office class="w-4 h-4 mr-1.5" />
                        Aset Tetap
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ ucfirst(str_replace('_', ' ', $stock->kategori)) }}
                    </span>
                @endif
            </div>
            @if($stock->kategori === 'material_umum' && $stock->sub_kategori)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Sub-Kategori</label>
                @if($stock->sub_kategori === 'barang_habis_pakai')
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
            @if($stock->kategori === 'aset_sewa')
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status Kondisi</label>
                <div class="flex items-center gap-2">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $stock->status_kondisi_badge ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $stock->status_kondisi_label ?? 'Digunakan' }}
                    </span>
                    @if($stock->keterangan_kondisi)
                    <span class="text-xs text-gray-500">- {{ $stock->keterangan_kondisi }}</span>
                    @endif
                </div>
                @if($stock->tanggal_update_kondisi)
                <p class="text-xs text-gray-400 mt-1">Diupdate: {{ \Carbon\Carbon::parse($stock->tanggal_update_kondisi)->format('d M Y') }}</p>
                @endif
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Penginput</label>
                <p class="text-gray-800">{{ $stock->penginput }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Input</label>
                <p class="text-gray-800">{{ $stock->created_at->format('d M Y H:i') }}</p>
            </div>
            @if($stock->jenis || $stock->merek || $stock->tipe)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-2">Spesifikasi Teknis</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @if($stock->jenis)
                    <div class="bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-xs text-gray-500 block">Jenis</span>
                        <span class="text-sm font-medium text-gray-800">{{ $stock->jenis }}</span>
                    </div>
                    @endif
                    @if($stock->merek)
                    <div class="bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-xs text-gray-500 block">Merek</span>
                        <span class="text-sm font-medium text-gray-800">{{ $stock->merek }}</span>
                    </div>
                    @endif
                    @if($stock->tipe)
                    <div class="bg-gray-50 px-3 py-2 rounded-lg">
                        <span class="text-xs text-gray-500 block">Tipe</span>
                        <span class="text-sm font-medium text-gray-800">{{ $stock->tipe }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
                </div>
                
                <!-- Informasi Pengguna & Peminjaman (untuk Aset Sewa) -->
                @if($stock->kategori === 'aset_sewa' && $activeRental)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <x-heroicon-o-user class="w-5 h-5 text-blue-600" />
                        Informasi Pemakaian Aktif
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-blue-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Pengguna</label>
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">
                                    {{ substr($activeRental->user->name ?? 'N', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-gray-800 font-semibold">{{ $activeRental->user->name ?? $activeRental->penerima }}</p>
                                    @if($activeRental->user && $activeRental->user->division)
                                    <p class="text-xs text-gray-500">{{ $activeRental->user->division->nama_divisi }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Durasi Total</label>
                            <p class="text-gray-800 font-semibold">
                                @if($activeRental->tanggal_mulai_pakai && $activeRental->tanggal_akhir_pakai)
                                    @php
                                        $startDate = \Carbon\Carbon::parse($activeRental->tanggal_mulai_pakai);
                                        $endDate = \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai);
                                        $totalDays = $startDate->diffInDays($endDate);
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                                        <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                                        {{ $totalDays }} hari
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                            <p class="text-gray-800 font-medium">
                                @if($activeRental->tanggal_mulai_pakai)
                                    <span class="flex items-center gap-2">
                                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                                        {{ \Carbon\Carbon::parse($activeRental->tanggal_mulai_pakai)->format('d M Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Berakhir</label>
                            <p class="text-gray-800 font-medium">
                                @if($activeRental->tanggal_akhir_pakai)
                                    <span class="flex items-center gap-2">
                                        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                                        {{ \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai)->format('d M Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        @if($activeRental->tanggal_akhir_pakai && $activeRental->tanggal_mulai_pakai)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Status Pemakaian</label>
                            @php
                                $now = \Carbon\Carbon::now();
                                $endDate = \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai);
                                $startDate = \Carbon\Carbon::parse($activeRental->tanggal_mulai_pakai);
                                $daysLeft = $now->diffInDays($endDate, false);
                                $totalDays = $startDate->diffInDays($endDate);
                                $daysPassed = $startDate->diffInDays($now);
                                $progress = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 0;
                            @endphp
                            
                            <div class="space-y-2">
                                @if($daysLeft > 7)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                                        Masih {{ ceil($daysLeft) }} hari lagi
                                    </span>
                                @elseif($daysLeft > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 mr-2" />
                                        Segera berakhir dalam {{ ceil($daysLeft) }} hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <x-heroicon-o-x-circle class="w-5 h-5 mr-2" />
                                        Sudah melewati {{ abs(floor($daysLeft)) }} hari
                                    </span>
                                @endif
                                
                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                                    <div class="h-2.5 rounded-full transition-all duration-300
                                        @if($progress < 50) bg-green-500
                                        @elseif($progress < 90) bg-yellow-500
                                        @else bg-red-500
                                        @endif" 
                                        style="width: {{ $progress }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- History Pemakaian untuk Aset Sewa -->
    @if($stock->kategori === 'aset_sewa' && $stock->outgoingTransactions->whereNull('id_request')->count() > 0)
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-clock class="w-6 h-6 text-purple-600" />
            Riwayat Pemakaian
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stock->outgoingTransactions->whereNull('id_request')->sortByDesc('created_at') as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-semibold text-sm">
                                    {{ substr($transaction->user->name ?? substr($transaction->penerima, 0, 1), 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? $transaction->penerima }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-sm text-gray-600">{{ $transaction->user->division->nama_divisi ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($transaction->tanggal_mulai_pakai && $transaction->tanggal_akhir_pakai)
                                    <p>{{ \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai)->format('d M Y') }}</p>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($transaction->tanggal_mulai_pakai && $transaction->tanggal_akhir_pakai)
                                @php
                                    $start = \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai);
                                    $end = \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai);
                                    $duration = $start->diffInDays($end);
                                @endphp
                                <span class="text-sm text-gray-900">{{ $duration }} hari</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($transaction->status === 'sedang_dipakai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                    Aktif
                                </span>
                            @elseif($transaction->status === 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Selesai
                                </span>
                            @elseif($transaction->status === 'ditarik')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                    Ditarik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Transaction History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Incoming Transactions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-arrow-down-circle class="w-6 h-6 text-green-600" />
                Riwayat Barang Masuk
            </h3>
            @if($stock->incomingTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($stock->incomingTransactions->take(5) as $transaction)
                <div class="border-l-4 border-green-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">+{{ $transaction->qty }} unit</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $transaction->penginput }}</span>
                    </div>
                    @if($transaction->keterangan)
                    <p class="text-sm text-gray-600 mt-1">{{ $transaction->keterangan }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @if($stock->incomingTransactions->count() > 5)
            <p class="text-sm text-gray-500 mt-4 text-center">
                Dan {{ $stock->incomingTransactions->count() - 5 }} transaksi lainnya
            </p>
            @endif
            @else
            <p class="text-gray-500 text-center py-4">Belum ada transaksi masuk</p>
            @endif
        </div>

        <!-- Outgoing Transactions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-arrow-up-circle class="w-6 h-6 text-red-600" />
                Riwayat Barang Keluar
            </h3>
            @if($stock->outgoingTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($stock->outgoingTransactions->take(5) as $transaction)
                <div class="border-l-4 border-red-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">-{{ $transaction->qty }} unit</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $transaction->penerima }}</span>
                    </div>
                    @if($transaction->keterangan)
                    <p class="text-sm text-gray-600 mt-1">{{ $transaction->keterangan }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @if($stock->outgoingTransactions->count() > 5)
            <p class="text-sm text-gray-500 mt-4 text-center">
                Dan {{ $stock->outgoingTransactions->count() - 5 }} transaksi lainnya
            </p>
            @endif
            @else
            <p class="text-gray-500 text-center py-4">Belum ada transaksi keluar</p>
            @endif
        </div>
    </div>
</div>
@endsection
