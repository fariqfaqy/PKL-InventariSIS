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

    @php
        $requestsWithAdminNotes = $requests->filter(fn($r) => $r->catatan_admin);
        $hasAdminUpdates = $requestsWithAdminNotes->count() > 0;
        
        // Get latest update timestamp untuk invalidate localStorage jika ada update baru
        $latestUpdate = $requests->filter(fn($r) => $r->tanggal_diproses)
            ->sortByDesc('tanggal_diproses')
            ->first();
        $latestUpdateTimestamp = $latestUpdate ? $latestUpdate->tanggal_diproses->timestamp : 0;
    @endphp

    @if($hasAdminUpdates)
    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-3">
        <x-heroicon-o-bell class="w-5 h-5 mt-0.5" />
        <div>
            <p class="font-medium">Ada {{ $requestsWithAdminNotes->count() }} pesan dari admin!</p>
            <p class="text-sm mt-1">Klik "Lihat Detail" pada request yang memiliki badge "Pesan" untuk membaca catatan dari admin.</p>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('requests')" id="tab-requests" class="border-[#14a2ba] text-[#14a2ba] hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button active">
                Request Saya
                @if($requestNotifCount > 0)
                    <span class="ml-2 bg-blue-500 text-white rounded-full px-2 py-0.5 text-xs font-semibold">{{ $requestNotifCount }}</span>
                @endif
            </button>
            <button onclick="switchTab('changes')" id="tab-changes" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                Request Perubahan
                @if($changeRequestNotifCount > 0)
                    <span class="ml-2 bg-orange-500 text-white rounded-full px-2 py-0.5 text-xs font-semibold">{{ $changeRequestNotifCount }}</span>
                @endif
            </button>
            <button onclick="switchTab('aset-sewa')" id="tab-aset-sewa" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                Aset Sewa Saya
                @if($asetSewaAssigned->where('status', 'sedang_dipakai')->count() > 0)
                    <span class="ml-2 bg-purple-500 text-white rounded-full px-2 py-0.5 text-xs font-semibold">{{ $asetSewaAssigned->where('status', 'sedang_dipakai')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('history')" id="tab-history" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                History Pemakaian
                @if($historyNotifCount > 0)
                    <span class="ml-2 bg-green-500 text-white rounded-full px-2 py-0.5 text-xs font-semibold">{{ $historyNotifCount }}</span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Request Tab Content -->
    <div id="content-requests" class="tab-content">
        @php
            $pendingRequests = $requests->where('status', 'pending');
            $approvedRequests = $requests->where('status', 'approved');
        @endphp

        <!-- Pending Section -->
        @if($pendingRequests->count() > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-clock class="h-6 w-6 text-yellow-600" />
                <h3 class="text-lg font-semibold text-gray-800">Menunggu Approval</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $pendingRequests->count() }}</span>
            </div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-yellow-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-yellow-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub-Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingRequests as $item)
                            <tr class="hover:bg-yellow-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $item->id_request }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_request->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->penerima ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->stock && $item->stock->sub_kategori)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->sub_kategori_badge_color == 'green' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            @if($item->stock->sub_kategori == 'barang_habis_pakai')
                                                <x-heroicon-o-archive-box class="w-3 h-3 mr-1" />
                                                Barang Habis Pakai
                                            @else
                                                <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                                Barang Pinjam
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('user.pemakaian.show', $item->id_request) }}" class="text-[#14a2ba] hover:text-[#0d7a8f]" title="Lihat Detail">
                                            <x-heroicon-o-eye class="h-5 w-5 inline" />
                                        </a>
                                        <a href="{{ route('user.request-barang.edit', $item->id_request) }}" class="text-blue-600 hover:text-blue-900" title="Edit Request">
                                            <x-heroicon-o-pencil class="h-5 w-5 inline" />
                                        </a>
                                        <form action="{{ route('user.request-barang.destroy', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Batalkan request ini?')" title="Batalkan Request">
                                                <x-heroicon-o-trash class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Approved Section -->
        @if($approvedRequests->count() > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-check-circle class="h-6 w-6 text-green-600" />
                <h3 class="text-lg font-semibold text-gray-800">Disetujui - Sedang Digunakan</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $approvedRequests->count() }}</span>
            </div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub-Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($approvedRequests as $item)
                            <tr class="hover:bg-green-50 transition-colors {{ $item->catatan_admin ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">#{{ $item->id_request }}</span>
                                        @if($item->catatan_admin)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-500 text-white">
                                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-3 h-3 mr-0.5" />
                                                Pesan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_request->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $outgoing = $outgoingTransactions->get($item->id_request);
                                    @endphp
                                    @if($outgoing && $outgoing->penerima)
                                        <div class="text-gray-900 font-medium">{{ $outgoing->penerima }}</div>
                                        @if($outgoing->kategori == 'barang_sewa' && $outgoing->tanggal_akhir_sewa)
                                            @php
                                                $sisaHari = now()->startOfDay()->diffInDays($outgoing->tanggal_akhir_sewa, false);
                                            @endphp
                                            <div class="text-xs mt-1 {{ $sisaHari < 0 ? 'text-red-600' : ($sisaHari <= 7 ? 'text-yellow-600' : 'text-gray-500') }}">
                                                @if($sisaHari >= 0)
                                                    {{ $sisaHari }} hari lagi
                                                @else
                                                    Sudah berakhir
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->stock && $item->stock->sub_kategori)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->stock->sub_kategori == 'barang_habis_pakai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            @if($item->stock->sub_kategori == 'barang_habis_pakai')
                                                <x-heroicon-o-archive-box class="w-3 h-3 mr-1" />
                                                Barang Habis Pakai
                                            @else
                                                <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                                Barang Pinjam
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @php
                                        // Gunakan eager loaded data untuk prevent N+1 query
                                        $pendingRequest = $item->changeRequests->first(); // sudah di-eager load
                                        
                                        // Check cancellation dari eager loaded data
                                        $hasPendingCancellation = $pendingRequest && (
                                            str_contains(strtoupper($pendingRequest->keperluan), 'PEMBATALAN') ||
                                            str_contains(strtoupper($pendingRequest->catatan_user ?? ''), 'PEMBATALAN')
                                        );
                                    @endphp
                                    @if($hasPendingCancellation)
                                        <div class="text-xs text-gray-500 italic">Menunggu approval pembatalan</div>
                                    @else
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('user.pemakaian.show', $item->id_request) }}" class="text-[#14a2ba] hover:text-[#0d7a8f]" title="Lihat Detail">
                                            <x-heroicon-o-eye class="h-5 w-5 inline" />
                                        </a>
                                        <form action="{{ route('user.request-barang.complete', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Tandai request ini sudah selesai?')" title="Tandai Selesai">
                                                <x-heroicon-o-check-circle class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                        <a href="{{ route('user.request-barang.edit', $item->id_request) }}" class="text-orange-600 hover:text-orange-900" title="Request Perubahan">
                                            <x-heroicon-o-pencil-square class="h-5 w-5 inline" />
                                        </a>
                                        <form action="{{ route('user.request-barang.request-cancel', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Request pembatalan? Admin harus approve untuk return barang.')" title="Request Pembatalan">
                                                <x-heroicon-o-x-circle class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Empty State -->
        @if($pendingRequests->isEmpty() && $approvedRequests->isEmpty())
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <x-heroicon-o-inbox class="h-16 w-16 mx-auto mb-4 text-gray-400" />
            <p class="text-lg font-medium text-gray-500">Belum ada request</p>
            <p class="text-sm text-gray-400 mt-1">Request yang Anda buat akan muncul di sini</p>
        </div>
        @endif
    </div>
    <!-- Request Perubahan Tab Content -->
    <div id="content-changes" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Asli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($changeRequests as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $item->id_request }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->tanggal_request->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->qty }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->isCancellationRequest())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                        Pembatalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <x-heroicon-o-pencil-square class="w-3 h-3 mr-1" />
                                        Perubahan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->tipe_request == 'pinjam_sewa')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                        Pinjam Aset Sewa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-shopping-cart class="w-3 h-3 mr-1" />
                                        Pakai Material Umum
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->parentRequest)
                                    <a href="{{ route('user.pemakaian.show', $item->parentRequest->id_request) }}" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        #{{ $item->parentRequest->id_request }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                        Pending
                                    </span>
                                @elseif($item->status == 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        Disetujui
                                    </span>
                                @elseif($item->status == 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    @if($item->status == 'pending')
                                        <!-- Delete request perubahan yang masih pending -->
                                        <form action="{{ route('user.request-barang.destroy', $item->id_request) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus request perubahan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-document-text class="w-16 h-16 text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg font-medium mb-1">Belum Ada Request Perubahan</p>
                                    <p class="text-gray-400 text-sm">Request perubahan akan muncul di sini ketika Anda mengubah atau membatalkan request yang sudah disetujui</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Aset Sewa Tab Content -->
    <div id="content-aset-sewa" class="tab-content hidden">
        @php
            $asetSedangDigunakan = $asetSewaAssigned->where('status', 'sedang_dipakai');
            $asetSelesai = $asetSewaAssigned->where('status', 'selesai');
        @endphp

        @if($asetSewaAssigned->count() > 0)
            <!-- Sub-tabs untuk Aset Sewa -->
            <div class="mb-4">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6" aria-label="Aset Sewa Sub tabs">
                        <button onclick="switchAsetSewaTab('sedang-digunakan')" id="aset-subtab-sedang-digunakan" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm aset-subtab-button active-aset-subtab">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-computer-desktop class="w-4 h-4" />
                                Sedang Digunakan
                                @if($asetSedangDigunakan->count() > 0)
                                    <span class="bg-purple-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $asetSedangDigunakan->count() }}</span>
                                @endif
                            </span>
                        </button>
                        <button onclick="switchAsetSewaTab('selesai')" id="aset-subtab-selesai" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm aset-subtab-button">
                            <span class="flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                Selesai
                                @if($asetSelesai->count() > 0)
                                    <span class="bg-gray-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $asetSelesai->count() }}</span>
                                @endif
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Sedang Digunakan Content -->
            <div id="aset-content-sedang-digunakan" class="aset-subtab-content">
                @if($asetSedangDigunakan->count() > 0)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-purple-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Akhir</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Di-assign oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($asetSedangDigunakan as $aset)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-mono font-semibold text-gray-900">{{ $aset->stock->kodebarang }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $aset->stock->namabarang }}</div>
                                            <div class="text-xs text-gray-500">{{ $aset->stock->deskripsi }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($aset->tanggal_mulai_pakai)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if($aset->tanggal_akhir_pakai)
                                                {{ \Carbon\Carbon::parse($aset->tanggal_akhir_pakai)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $kondisi = $aset->stock->status_kondisi ?? 'digunakan';
                                                $badgeColor = match($kondisi) {
                                                    'digunakan' => 'bg-green-100 text-green-800',
                                                    'diperbaiki' => 'bg-yellow-100 text-yellow-800',
                                                    'rusak' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                                {{ ucfirst($kondisi) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $aset->diproses_oleh ?? 'Admin' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-xl shadow-md">
                        <x-heroicon-o-computer-desktop class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aset yang sedang digunakan</h3>
                        <p class="mt-1 text-sm text-gray-500">Aset sewa yang sedang Anda gunakan akan muncul di sini</p>
                    </div>
                @endif
            </div>

            <!-- Selesai Content -->
            <div id="aset-content-selesai" class="aset-subtab-content hidden">
                @if($asetSelesai->count() > 0)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Di-assign oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($asetSelesai as $aset)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-mono font-semibold text-gray-900">{{ $aset->stock->kodebarang }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $aset->stock->namabarang }}</div>
                                            <div class="text-xs text-gray-500">{{ $aset->stock->deskripsi }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($aset->tanggal)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $aset->tanggal_selesai ? \Carbon\Carbon::parse($aset->tanggal_selesai)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if($aset->tanggal && $aset->tanggal_selesai)
                                                {{ \Carbon\Carbon::parse($aset->tanggal)->diffInDays(\Carbon\Carbon::parse($aset->tanggal_selesai)) }} hari
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $aset->diproses_oleh ?? 'Admin' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-xl shadow-md">
                        <x-heroicon-o-archive-box class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada aset yang selesai</h3>
                        <p class="mt-1 text-sm text-gray-500">Riwayat pemakaian aset sewa akan muncul di sini</p>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-xl shadow-md">
                <x-heroicon-o-computer-desktop class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada aset sewa</h3>
                <p class="mt-1 text-sm text-gray-500">Aset sewa yang di-assign admin ke Anda akan muncul di sini</p>
            </div>
        @endif
    </div>
    
    <!-- History Tab Content -->
    <div id="content-history" class="tab-content hidden">
        <!-- Sub-tabs untuk History -->
        <div class="mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6" aria-label="Sub tabs">
                    <button onclick="switchHistoryTab('sedang-dipakai')" id="subtab-sedang-dipakai" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm subtab-button active-subtab">
                        <span class="flex items-center gap-2">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Sedang Dipakai
                            @if($sedangDipakai->count() > 0)
                                <span class="bg-blue-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $sedangDipakai->count() }}</span>
                            @endif
                        </span>
                    </button>
                    <button onclick="switchHistoryTab('selesai')" id="subtab-selesai" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm subtab-button">
                        <span class="flex items-center gap-2">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Selesai
                            @if($selesai->count() > 0)
                                <span class="bg-green-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $selesai->count() }}</span>
                            @endif
                        </span>
                    </button>
                    <button onclick="switchHistoryTab('dibatalkan')" id="subtab-dibatalkan" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm subtab-button">
                        <span class="flex items-center gap-2">
                            <x-heroicon-o-x-circle class="w-4 h-4" />
                            Ditolak/Dibatalkan
                            @if($ditolakDibatalkan->count() > 0)
                                <span class="bg-red-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $ditolakDibatalkan->count() }}</span>
                            @endif
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Sedang Dipakai Content -->
        <div id="content-sedang-dipakai" class="subtab-content">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-blue-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($sedangDipakai as $item)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $item->idkeluar }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tanggal->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->namabarang_k }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->kodebarang_k }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $item->qty }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->penerima }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->tipe_request == 'peminjaman')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                            Peminjaman
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-heroicon-o-shopping-cart class="w-3 h-3 mr-1" />
                                            Permintaan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <x-heroicon-o-arrow-path class="w-16 h-16 mb-4 opacity-30" />
                                        <p class="text-lg font-medium">Tidak ada pemakaian yang sedang berjalan</p>
                                        <p class="text-sm mt-1">Pemakaian yang disetujui akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Selesai Content -->
        <div id="content-selesai" class="subtab-content hidden">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pakai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($selesai as $item)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $item->idkeluar }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->namabarang_k }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->kodebarang_k }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $item->qty }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->penerima }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->tipe_request == 'peminjaman')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                            Peminjaman
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-heroicon-o-shopping-cart class="w-3 h-3 mr-1" />
                                            Permintaan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <x-heroicon-o-check-circle class="w-16 h-16 mb-4 opacity-30" />
                                        <p class="text-lg font-medium">Belum ada pemakaian yang selesai</p>
                                        <p class="text-sm mt-1">Pemakaian yang sudah dikembalikan akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ditolak/Dibatalkan Content -->
        <div id="content-dibatalkan" class="subtab-content hidden">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ditolakDibatalkan as $item)
                            <tr class="hover:bg-gray-50 transition-colors {{ $item->catatan_admin ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">#{{ $item->id_request }}</span>
                                        @if($item->catatan_admin)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-500 text-white">
                                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-3 h-3 mr-0.5" />
                                                Alasan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tanggal_request->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $item->qty }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->parent_request_id)
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                                Perubahan
                                            </span>
                                            <span class="text-xs text-gray-500">dari #{{ $item->parent_request_id }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-heroicon-o-document-plus class="w-3 h-3 mr-1" />
                                            Request Baru
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->status == 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                            Ditolak Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->catatan_admin ?? ($item->status == 'rejected' ? 'Ditolak oleh admin' : 'Dibatalkan oleh user') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <x-heroicon-o-x-circle class="w-16 h-16 mb-4 opacity-30" />
                                        <p class="text-lg font-medium">Tidak ada request yang ditolak atau dibatalkan</p>
                                        <p class="text-sm mt-1">Request yang ditolak/dibatalkan akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple tab switching - no localStorage tracking, seperti admin
function switchTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-[#14a2ba]', 'text-[#14a2ba]');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.add('active', 'border-[#14a2ba]', 'text-[#14a2ba]');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

function switchHistoryTab(subtab) {
    // Hide all subtab contents
    document.querySelectorAll('.subtab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all subtabs
    document.querySelectorAll('.subtab-button').forEach(button => {
        button.classList.remove('active-subtab', 'border-[#14a2ba]', 'text-[#14a2ba]');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected subtab content
    document.getElementById('content-' + subtab).classList.remove('hidden');
    
    // Add active class to selected subtab
    const activeSubtab = document.getElementById('subtab-' + subtab);
    activeSubtab.classList.add('active-subtab', 'border-[#14a2ba]', 'text-[#14a2ba]');
    activeSubtab.classList.remove('border-transparent', 'text-gray-500');
}

function switchAsetSewaTab(subtab) {
    // Hide all aset sewa subtab contents
    document.querySelectorAll('.aset-subtab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all aset sewa subtabs
    document.querySelectorAll('.aset-subtab-button').forEach(button => {
        button.classList.remove('active-aset-subtab', 'border-[#14a2ba]', 'text-[#14a2ba]');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected subtab content
    document.getElementById('aset-content-' + subtab).classList.remove('hidden');
    
    // Add active class to selected subtab
    const activeSubtab = document.getElementById('aset-subtab-' + subtab);
    activeSubtab.classList.add('active-aset-subtab', 'border-[#14a2ba]', 'text-[#14a2ba]');
    activeSubtab.classList.remove('border-transparent', 'text-gray-500');
}
</script>

@endsection
