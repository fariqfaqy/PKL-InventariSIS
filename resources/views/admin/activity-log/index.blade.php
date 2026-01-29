@extends('layouts.admin')

@section('title', 'Activity Log Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Activity Log Report</h2>
            <p class="text-sm text-gray-500 mt-1">Monitor seluruh aktivitas sistem</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <input type="text" name="usr" value="{{ request('usr') }}" 
                           placeholder="Cari nama user..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                </div>

                <!-- Method Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Method</label>
                    <select name="method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">Semua Method</option>
                        <option value="GET" {{ request('method') == 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ request('method') == 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="PUT" {{ request('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                        <option value="DELETE" {{ request('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                    </select>
                </div>

                <!-- Endpoint Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint</label>
                    <input type="text" name="endpoint" value="{{ request('endpoint') }}" 
                           placeholder="Cari endpoint..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                </div>

                <!-- Status Code Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Code</label>
                    <select name="status_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="200" {{ request('status_code') == '200' ? 'selected' : '' }}>200 - OK</option>
                        <option value="201" {{ request('status_code') == '201' ? 'selected' : '' }}>201 - Created</option>
                        <option value="302" {{ request('status_code') == '302' ? 'selected' : '' }}>302 - Redirect</option>
                        <option value="400" {{ request('status_code') == '400' ? 'selected' : '' }}>400 - Bad Request</option>
                        <option value="401" {{ request('status_code') == '401' ? 'selected' : '' }}>401 - Unauthorized</option>
                        <option value="403" {{ request('status_code') == '403' ? 'selected' : '' }}>403 - Forbidden</option>
                        <option value="404" {{ request('status_code') == '404' ? 'selected' : '' }}>404 - Not Found</option>
                        <option value="500" {{ request('status_code') == '500' ? 'selected' : '' }}>500 - Server Error</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#14a2ba] text-white rounded-lg hover:bg-[#0d7a8f] transition-all duration-200">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                    <span class="font-medium">Filter</span>
                </button>
                <a href="{{ route('admin.activity-log.index') }}" class="flex items-center gap-2 px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="font-medium">Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Instant Search Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[action="{{ route('admin.activity-log.index') }}"]');
        const usrInput = filterForm.querySelector('input[name="usr"]');
        const endpointInput = filterForm.querySelector('input[name="endpoint"]');
        const methodSelect = filterForm.querySelector('select[name="method"]');
        const statusCodeSelect = filterForm.querySelector('select[name="status_code"]');
        const dateFromInput = filterForm.querySelector('input[name="date_from"]');
        const dateToInput = filterForm.querySelector('input[name="date_to"]');
        
        let searchTimeout;
        
        // Instant search on typing
        [usrInput, endpointInput].forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 500);
            });
        });
        
        // Instant filter on select/date change
        [methodSelect, statusCodeSelect, dateFromInput, dateToInput].forEach(element => {
            element.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
    </script>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-md p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Log</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($logs->total()) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <x-heroicon-o-document-text class="w-8 h-8 text-blue-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Success</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['success']) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-green-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Client Error</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['client_error']) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-yellow-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Server Error</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['server_error']) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <x-heroicon-o-x-circle class="w-8 h-8 text-red-600" />
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Method</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Endpoint</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->idlog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->date->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                                <span class="text-sm font-medium text-gray-900">{{ $log->usr }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($log->method == 'GET') bg-blue-100 text-blue-800
                                @elseif($log->method == 'POST') bg-green-100 text-green-800
                                @elseif($log->method == 'PUT') bg-yellow-100 text-yellow-800
                                @elseif($log->method == 'DELETE') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $log->endpoint }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if(str_starts_with($log->status_code, '2')) bg-green-100 text-green-800
                                @elseif(str_starts_with($log->status_code, '3')) bg-blue-100 text-blue-800
                                @elseif(str_starts_with($log->status_code, '4')) bg-yellow-100 text-yellow-800
                                @elseif(str_starts_with($log->status_code, '5')) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $log->status_code }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-document-text class="w-16 h-16 mb-4 text-gray-300" />
                                <p class="text-lg font-medium">Tidak ada data log</p>
                                <p class="text-sm mt-1">Belum ada aktivitas yang tercatat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
