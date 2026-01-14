@extends('layouts.admin')

@section('title', 'Kelola Permintaan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Permintaan</h1>
        <p class="text-gray-600 mt-2">Kelola permintaan barang dari user</p>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-100 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="h-8 w-8 text-yellow-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-100 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle class="h-8 w-8 text-blue-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disetujui</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-100 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-shield-check class="h-8 w-8 text-green-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('normal')" id="tab-normal" class="border-blue-600 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button active">
                Request Biasa
                @if($normalRequests->where('status', 'pending')->count() > 0)
                    <span class="ml-2 bg-red-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $normalRequests->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('perubahan')" id="tab-perubahan" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                Request Perubahan
                @if($changeRequests->where('status', 'pending')->count() > 0)
                    <span class="ml-2 bg-orange-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $changeRequests->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('history')" id="tab-history" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                History
                <span class="ml-2 bg-gray-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $completedRequests->count() + $outgoingTransactions->count() }}</span>
            </button>
        </nav>
    </div>

    <!-- Normal Request Tab -->
    <div id="content-normal" class="tab-content">
        @php
            $pendingRequests = $normalRequests->where('status', 'pending');
            $approvedRequests = $normalRequests->where('status', 'approved');
            $rejectedRequests = $normalRequests->where('status', 'rejected');
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingRequests as $item)
                            <tr class="hover:bg-yellow-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $item->id_request }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_request->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->tipe_request_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.permintaan.show', $item->id_request) }}" class="text-blue-600 hover:text-blue-900">
                                        <x-heroicon-o-eye class="h-5 w-5 inline" /> Detail
                                    </a>
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
                <h3 class="text-lg font-semibold text-gray-800">Disetujui - Menunggu Selesai</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $approvedRequests->count() }}</span>
            </div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($approvedRequests as $item)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $item->id_request }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_request->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->tipe_request_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $hasPendingRequest = \App\Models\RequestBarang::where('parent_request_id', $item->id_request)
                                            ->where('status', 'pending')
                                            ->exists();
                                        $pendingRequest = $hasPendingRequest ? \App\Models\RequestBarang::where('parent_request_id', $item->id_request)->where('status', 'pending')->latest()->first() : null;
                                        
                                        // Detect tipe request: pembatalan atau perubahan
                                        $isCancellation = $pendingRequest && (
                                            str_contains(strtoupper($pendingRequest->keperluan), 'PEMBATALAN') ||
                                            str_contains(strtoupper($pendingRequest->catatan_user ?? ''), 'PEMBATALAN')
                                        );
                                    @endphp
                                    @if($hasPendingRequest && $pendingRequest)
                                        @if($isCancellation)
                                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs">
                                                <x-heroicon-o-exclamation-circle class="w-3 h-3 inline" /> Pembatalan pending
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs">
                                                <x-heroicon-o-clock class="w-3 h-3 inline" /> Perubahan pending
                                            </span>
                                        @endif
                                    @else
                                        @php
                                            $hasApprovedChange = \App\Models\RequestBarang::where('parent_request_id', $item->id_request)
                                                ->where('status', 'approved')
                                                ->exists();
                                        @endphp
                                        @if($hasApprovedChange)
                                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">
                                                <x-heroicon-o-check class="w-3 h-3 inline" /> Sudah diubah
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @php
                                        // Re-check untuk action buttons
                                        $hasPendingCancellation = \App\Models\RequestBarang::where('parent_request_id', $item->id_request)
                                            ->where('status', 'pending')
                                            ->where(function($q) {
                                                $q->where('keperluan', 'like', '%PEMBATALAN%')
                                                  ->orWhere('catatan_user', 'like', '%PEMBATALAN%');
                                            })
                                            ->exists();
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        @if(!$hasPendingCancellation)
                                        <form action="{{ route('admin.permintaan.mark-complete', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Tandai request ini sudah selesai?')" title="Tandai Selesai">
                                                <x-heroicon-o-check-circle class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ route('admin.permintaan.show', $item->id_request) }}" class="text-blue-600 hover:text-blue-900">
                                            <x-heroicon-o-eye class="h-5 w-5 inline" /> Detail
                                        </a>
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

        <!-- Rejected Section -->
        @if($rejectedRequests->count() > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-o-x-circle class="h-6 w-6 text-red-600" />
                <h3 class="text-lg font-semibold text-gray-800">Ditolak</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $rejectedRequests->count() }}</span>
            </div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rejectedRequests as $item)
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $item->id_request }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_request->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->tipe_request_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.permintaan.show', $item->id_request) }}" class="text-blue-600 hover:text-blue-900">
                                        <x-heroicon-o-eye class="h-5 w-5 inline" /> Detail
                                    </a>
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
        @if($normalRequests->isEmpty())
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <x-heroicon-o-inbox class="h-16 w-16 mx-auto mb-4 text-gray-400" />
            <p class="text-lg font-medium text-gray-500">Belum ada request</p>
            <p class="text-sm text-gray-400 mt-1">Request dari user akan muncul di sini</p>
        </div>
        @endif
    </div>

    <!-- Request Perubahan Tab -->
    <div id="content-perubahan" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
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
                        @php
                            $isPembatalan = stripos($item->keperluan, 'PEMBATALAN') !== false || 
                                           stripos($item->catatan_user ?? '', 'PEMBATALAN') !== false;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $item->id_request }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->tanggal_request->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $item->stock->namabarang }}</div>
                                <div class="text-gray-500 text-xs">{{ $item->stock->kodebarang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->qty }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($isPembatalan)
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
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item->tipe_request_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->parentRequest)
                                    <a href="{{ route('admin.permintaan.show', $item->parentRequest->id_request) }}" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        #{{ $item->parentRequest->id_request }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.permintaan.show', $item->id_request) }}" class="text-blue-600 hover:text-blue-900">
                                    <x-heroicon-o-eye class="h-5 w-5 inline" /> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-document-text class="w-16 h-16 text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg font-medium">Belum Ada Request Perubahan</p>
                                    <p class="text-gray-400 text-sm mt-1">Request perubahan akan muncul ketika user mengubah atau membatalkan request yang sudah disetujui</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div id="content-history" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $historyIndex = 1; @endphp
                        
                        @forelse($completedRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $historyIndex++ }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $request->id_request }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->tanggal_request->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $request->stock->namabarang }}</div>
                                <div class="text-gray-500 text-xs">{{ $request->stock->kodebarang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $request->qty }} unit
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($request->status == 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        Selesai
                                    </span>
                                @elseif($request->status == 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $request->status_label }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        
                        @forelse($outgoingTransactions as $trans)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $historyIndex++ }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #T{{ $trans->idkeluar }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $trans->tanggal->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $trans->penerima }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $trans->namabarang_k }}</div>
                                <div class="text-gray-500 text-xs">{{ $trans->kodebarang_k }}</div>
                                @if($trans->tipe_request == 'peminjaman')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                        <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                        Peminjaman
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $trans->qty }} unit
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Disetujui
                                </span>
                            </td>
                        </tr>
                        @empty
                        @if($completedRequests->isEmpty())
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-clipboard-document-list class="w-16 h-16 text-gray-300 mb-4" />
                                    <p class="text-gray-500 text-lg font-medium">Belum Ada History</p>
                                    <p class="text-gray-400 text-sm mt-1">Request yang sudah selesai akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-600', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}
</script>

@endsection
