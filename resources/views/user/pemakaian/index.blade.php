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

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('requests')" id="tab-requests" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button active">
                Request Saya
                @if($requests->where('status', 'pending')->count() > 0)
                    <span class="ml-2 bg-red-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $requests->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('changes')" id="tab-changes" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                Request Perubahan
                @if($changeRequests->where('status', 'pending')->count() > 0)
                    <span class="ml-2 bg-orange-500 text-white rounded-full px-2 py-0.5 text-xs">{{ $changeRequests->where('status', 'pending')->count() }}</span>
                @endif
            </button>
            <button onclick="switchTab('history')" id="tab-history" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                History Pemakaian
            </button>
        </nav>
    </div>

    <!-- Request Tab Content -->
    <div id="content-requests" class="tab-content">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $item)
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
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item->tipe_request_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="space-y-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                        {{ $item->status_label }}
                                    </span>
                                    @php
                                        // Cek apakah request ini punya request perubahan
                                        $hasChangeRequest = \App\Models\RequestBarang::where('parent_request_id', $item->id_request)->exists();
                                        $changeRequest = $hasChangeRequest ? \App\Models\RequestBarang::where('parent_request_id', $item->id_request)->latest()->first() : null;
                                    @endphp
                                    @if($hasChangeRequest && $changeRequest)
                                        <div class="text-xs">
                                            @if($changeRequest->status == 'pending')
                                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">
                                                    <x-heroicon-o-clock class="w-3 h-3 inline" /> Ada perubahan pending
                                                </span>
                                            @elseif($changeRequest->status == 'approved')
                                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                                    <x-heroicon-o-check class="w-3 h-3 inline" /> Diubah #{{ $changeRequest->id_request }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($item->status === 'pending')
                                        <!-- Edit Button -->
                                        <a href="{{ route('user.request-barang.edit', $item->id_request) }}" class="text-blue-600 hover:text-blue-900" title="Edit Request">
                                            <x-heroicon-o-pencil class="h-5 w-5 inline" />
                                        </a>
                                        <!-- Delete Button -->
                                        <form action="{{ route('user.request-barang.destroy', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Batalkan request ini?')" title="Batalkan Request">
                                                <x-heroicon-o-trash class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                    @elseif($item->status === 'approved')
                                        <!-- Mark as Completed -->
                                        <form action="{{ route('user.request-barang.complete', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Tandai request ini sudah selesai?')" title="Tandai Selesai">
                                                <x-heroicon-o-check-circle class="h-5 w-5 inline" />
                                            </button>
                                        </form>
                                        <!-- Edit Approved (akan create request perubahan) -->
                                        <a href="{{ route('user.request-barang.edit', $item->id_request) }}" class="text-orange-600 hover:text-orange-900" title="Request Perubahan">
                                            <x-heroicon-o-pencil-square class="h-5 w-5 inline" />
                                        </a>
                                        <!-- Delete Approved (akan create request pembatalan) -->
                                        <form action="{{ route('user.request-barang.request-cancel', $item->id_request) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Request pembatalan? Admin harus approve untuk return barang.')" title="Request Pembatalan">
                                                <x-heroicon-o-x-circle class="h-5 w-5 inline" />
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
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <x-heroicon-o-inbox class="h-12 w-12 mx-auto mb-2 text-gray-400" />
                                <p>Belum ada request</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
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
                                @if($item->tipe_request == 'pinjam_sewa')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                        Pinjam Barang Sewa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-heroicon-o-shopping-cart class="w-3 h-3 mr-1" />
                                        Pakai Barang Habis Pakai
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
                            <td colspan="8" class="px-6 py-12 text-center">
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
    <!-- History Tab Content -->
    <div id="content-history" class="tab-content hidden"
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $historyIndex = 1; @endphp
                        
                        @forelse($completedRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $historyIndex++ }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $request->id_request }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $request->tanggal_request->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $request->stock->namabarang }}</div>
                                <div class="text-xs text-gray-500">{{ $request->stock->kodebarang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $request->qty }} unit
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Selesai
                                </span>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        
                        @forelse($pemakaian as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $historyIndex++ }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $item->idkeluar }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tanggal->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $item->namabarang_k }}</div>
                            <div class="text-xs text-gray-500">{{ $item->kodebarang_k }}</div>
                            @if($item->tipe_request == 'peminjaman')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                    <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                    Peminjaman
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->qty }} unit
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-clipboard-document-list class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada history pemakaian</p>
                                <p class="text-sm mt-1">Request yang sudah selesai akan muncul di sini</p>
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

<script>
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
</script>

@endsection
