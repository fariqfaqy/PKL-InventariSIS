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

        <div class="bg-purple-100 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-arrow-path class="h-8 w-8 text-purple-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Diproses</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['processing'] }}</p>
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="{{ route('admin.permintaan.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Request</label>
                <select name="tipe_request" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="pinjam_sewa" {{ request('tipe_request') == 'pinjam_sewa' ? 'selected' : '' }}>Pinjam Sewa</option>
                    <option value="pakai_habis_pakai" {{ request('tipe_request') == 'pakai_habis_pakai' ? 'selected' : '' }}>Pakai Habis Pakai</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-funnel class="h-5 w-5 inline" /> Filter
                </button>
                <a href="{{ route('admin.permintaan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Permintaan Table -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($permintaan as $item)
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
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->tipe_request == 'pinjam_sewa' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item->tipe_request_label }}
                                </span>
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
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada permintaan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($permintaan->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $permintaan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
