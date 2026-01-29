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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Foto Produk Section -->
            @if($stock->image)
            <div class="flex flex-col items-center justify-center">
                <img src="{{ asset('images/barang/' . $stock->image) }}" 
                     alt="{{ $stock->namabarang }}" 
                     class="w-full h-64 object-cover rounded-xl border-2 border-gray-200 shadow-lg">
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded">{{ $stock->kodebarang }}</p>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                <x-heroicon-o-photo class="w-24 h-24 text-gray-400 mb-4" />
                <p class="text-sm text-gray-500">Tidak ada foto</p>
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded">{{ $stock->kodebarang }}</p>
                </div>
            </div>
            @endif

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
            @if($stock->kategori === 'material_umum')
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi Rak</label>
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    Rak {{ strtoupper($stock->rack) }}
                </span>
            </div>
            @endif
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
                </div>
                @if($stock->tanggal_update_kondisi)
                <p class="text-xs text-gray-400 mt-1">Diupdate: {{ \Carbon\Carbon::parse($stock->tanggal_update_kondisi)->format('d M Y') }}</p>
                @endif
            </div>
            @php
                // Get last rental (active or completed)
                $lastRental = $stock->outgoingTransactions->whereNull('id_request')->first();
            @endphp
            @if($lastRental && $lastRental->tanggal_mulai_pakai && $lastRental->tanggal_akhir_pakai)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Periode Pemakaian</label>
                <p class="text-sm text-gray-800">
                    {{ \Carbon\Carbon::parse($lastRental->tanggal_mulai_pakai)->format('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($lastRental->tanggal_akhir_pakai)->format('d M Y') }}
                </p>
                @php
                    $start = \Carbon\Carbon::parse($lastRental->tanggal_mulai_pakai);
                    $end = \Carbon\Carbon::parse($lastRental->tanggal_akhir_pakai);
                    $duration = $start->diffInDays($end) + 1;
                @endphp
                <p class="text-xs text-gray-400 mt-1">({{ $duration }} hari)</p>
            </div>
            @endif
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Diinput Oleh</label>
                <p class="text-gray-800">{{ $stock->penginput }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stock->created_at->format('d M Y, H:i') }} WIB</p>
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
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center gap-2">
                            <x-heroicon-o-user class="w-5 h-5 text-blue-600" />
                            Informasi Pemakaian Aktif
                        </h4>
                        @if($activeRental->status === 'sedang_dipakai')
                        <div class="flex gap-2">
                            <button onclick="openExtendModal()" 
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                                Perpanjang
                            </button>
                            <form action="{{ route('admin.barang-keluar.complete-rental', $activeRental->idkeluar) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menyelesaikan pemakaian aset sewa ini?')">
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
                            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                            <p class="text-gray-800 font-semibold">
                                @if($activeRental->status === 'sedang_dipakai')
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1.5" />
                                        Sedang Digunakan
                                    </span>
                                @elseif($activeRental->status === 'ditarik')
                                    <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                                        <x-heroicon-o-x-circle class="w-4 h-4 mr-1.5" />
                                        Ditarik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                        {{ ucfirst($activeRental->status) }}
                                    </span>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Durasi Total</label>
                            <p class="text-gray-800 font-medium">
                                @if($activeRental->tanggal_mulai_pakai && $activeRental->tanggal_akhir_pakai)
                                    @php
                                        $startDate = \Carbon\Carbon::parse($activeRental->tanggal_mulai_pakai)->startOfDay();
                                        $endDate = \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai)->startOfDay();
                                        $totalDays = $startDate->diffInDays($endDate) + 1;
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
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- History Pemakaian untuk Aset Sewa -->
    @if($stock->kategori === 'aset_sewa')
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-clock class="w-6 h-6 text-purple-600" />
            Riwayat Pemakaian Barang Ini
        </h3>
        
        @php
            // Combine transactions and status changes into timeline
            $timeline = collect();
            
            // Add outgoing transactions with all their lifecycle events
            foreach($stock->outgoingTransactions->whereNull('id_request') as $transaction) {
                // Transaction start (created_at)
                $timeline->push([
                    'type' => 'transaction_start',
                    'date' => $transaction->created_at,
                    'data' => $transaction
                ]);
                
                // If transaction completed (selesai), add completion event
                if($transaction->status === 'selesai' && $transaction->tanggal_selesai) {
                    $timeline->push([
                        'type' => 'transaction_end',
                        'date' => \Carbon\Carbon::parse($transaction->tanggal_selesai),
                        'data' => $transaction
                    ]);
                }
                
                // If transaction ditarik, add withdrawal event
                if($transaction->status === 'ditarik' && $transaction->tanggal_selesai) {
                    $timeline->push([
                        'type' => 'transaction_withdraw',
                        'date' => \Carbon\Carbon::parse($transaction->tanggal_selesai),
                        'data' => $transaction
                    ]);
                }
            }
            
            // Add status kondisi changes (simulated from updated_at when status changed)
            // In real implementation, you'd have a separate status_history table
            if($stock->tanggal_update_kondisi) {
                $timeline->push([
                    'type' => 'status_change',
                    'date' => \Carbon\Carbon::parse($stock->tanggal_update_kondisi),
                    'data' => [
                        'status' => $stock->status_kondisi,
                        'keterangan' => $stock->keterangan_kondisi,
                    ]
                ]);
            }
            
            $timeline = $timeline->sortByDesc('date');
        @endphp
        
        @if($timeline->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($timeline as $item)
                    @if($item['type'] === 'transaction_start')
                        @php $transaction = $item['data']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $transaction->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <x-heroicon-o-arrow-right-circle class="w-3 h-3 mr-1" />
                                Pemakaian
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-semibold text-sm">
                                    {{ substr($transaction->user->name ?? substr($transaction->penerima, 0, 1), 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? $transaction->penerima }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->user->division->nama_divisi ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                Sedang Dipakai
                            </span>
                        </td>
                    </tr>
                    
                    @elseif($item['type'] === 'transaction_end')
                        @php $transaction = $item['data']; @endphp
                    <tr class="hover:bg-gray-50 bg-green-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($transaction->tanggal_selesai)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                Selesai
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">Pemakaian selesai - Stok berkurang</p>
                            <p class="text-xs text-gray-500">Oleh {{ $transaction->user->name ?? $transaction->penerima }}</p>
                            @if($transaction->tanggal_mulai_pakai && $transaction->tanggal_akhir_pakai)
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai)->format('d M Y') }}
                                @php
                                    $start = \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai);
                                    $end = \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai);
                                    $duration = $start->diffInDays($end) + 1;
                                @endphp
                                ({{ $duration }} hari)
                            </p>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                Selesai
                            </span>
                        </td>
                    </tr>
                    
                    @elseif($item['type'] === 'transaction_withdraw')
                        @php $transaction = $item['data']; @endphp
                    <tr class="hover:bg-gray-50 bg-red-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($transaction->tanggal_selesai)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                Ditarik
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900">Barang ditarik dari pengguna</p>
                            <p class="text-xs text-gray-500">Ditarik dari {{ $transaction->user->name ?? $transaction->penerima }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                Ditarik
                            </span>
                        </td>
                    </tr>
                    @elseif($item['type'] === 'status_change')
                        @php $statusData = $item['data']; @endphp
                    <tr class="hover:bg-gray-50 bg-yellow-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $item['date']->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <x-heroicon-o-wrench-screwdriver class="w-3 h-3 mr-1" />
                                Status Kondisi
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    Perubahan Status: 
                                    @if($statusData['status'] === 'digunakan')
                                        <span class="text-green-700">Sedang Digunakan</span>
                                    @elseif($statusData['status'] === 'rusak')
                                        <span class="text-red-700">Rusak</span>
                                    @elseif($statusData['status'] === 'diperbaiki')
                                        <span class="text-orange-700">Sedang Diperbaiki</span>
                                    @else
                                        <span>{{ ucfirst($statusData['status']) }}</span>
                                    @endif
                                </p>
                                @if($statusData['keterangan'])
                                <p class="text-xs text-gray-500 mt-1">{{ $statusData['keterangan'] }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($statusData['status'] === 'digunakan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Ready
                                </span>
                            @elseif($statusData['status'] === 'rusak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                                    Rusak
                                </span>
                            @elseif($statusData['status'] === 'diperbaiki')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <x-heroicon-o-wrench class="w-3 h-3 mr-1" />
                                    Maintenance
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center text-gray-500 py-8">Belum ada riwayat pemakaian</p>
        @endif
    </div>
    @endif
</div>

<!-- Extend Modal (untuk Aset Sewa) -->
@if($stock->kategori === 'aset_sewa' && $activeRental && $activeRental->status === 'sedang_dipakai')
<div id="extendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Perpanjang Masa Pakai</h3>
            <button onclick="closeExtendModal()" class="text-gray-400 hover:text-gray-600">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <form action="{{ route('admin.barang-keluar.extend-rental', $activeRental->idkeluar) }}" method="POST">
            @csrf
            
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600">Tanggal Berakhir Saat Ini:</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai)->format('d M Y') }}
                </p>
            </div>

            <div class="mb-6">
                <label for="tanggal_akhir_pakai" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Berakhir Baru <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="tanggal_akhir_pakai" 
                       name="tanggal_akhir_pakai" 
                       min="{{ \Carbon\Carbon::parse($activeRental->tanggal_akhir_pakai)->addDay()->format('Y-m-d') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                @error('tanggal_akhir_pakai')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeExtendModal()" 
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-800 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                    Perpanjang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExtendModal() {
    document.getElementById('extendModal').classList.remove('hidden');
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('extendModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeExtendModal();
    }
});
</script>
@endif

@endsection
