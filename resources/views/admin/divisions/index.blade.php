@extends('layouts.admin')

@section('title', 'Kelola Divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kelola Divisi</h2>
            <p class="text-sm text-gray-500 mt-1">Manajemen divisi organisasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.divisions.export-pdf') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                <span class="font-medium">Export PDF</span>
            </a>
            <a href="{{ route('admin.divisions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-300">
                <x-heroicon-o-plus class="w-5 h-5" />
                <span class="font-medium">Tambah Divisi</span>
            </a>
        </div>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Divisi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $divisions->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-building-office class="w-6 h-6 text-indigo-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Divisi Aktif</p>
                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Division::where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-check-badge class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Pegawai</p>
                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\User::whereNotNull('division_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-user-group class="w-6 h-6 text-cyan-600" />
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Divisi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kepala Divisi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Pegawai</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($divisions as $index => $division)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $divisions->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($division->kode_divisi, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $division->nama_divisi }}</p>
                                    @if($division->deskripsi)
                                    <p class="text-xs text-gray-500 line-clamp-1">{{ Str::limit($division->deskripsi, 50) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-900">
                            {{ $division->kode_divisi }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $division->kepala_divisi ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $division->users_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($division->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 mr-1 bg-green-600 rounded-full"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 mr-1 bg-gray-600 rounded-full"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.divisions.show', $division->id) }}" class="text-[#14a2ba] hover:text-[#0d7a8f] transition-colors" title="Detail">
                                    <x-heroicon-o-eye class="w-5 h-5" />
                                </a>
                                <a href="{{ route('admin.divisions.edit', $division->id) }}" class="text-yellow-600 hover:text-yellow-700 transition-colors" title="Edit">
                                    <x-heroicon-o-pencil class="w-5 h-5" />
                                </a>
                                <form action="{{ route('admin.divisions.destroy', $division->id) }}" method="POST" class="inline" onsubmit="return customConfirm(event, 'Yakin ingin menghapus divisi ini? Pegawai yang terkait akan kehilangan divisi.', {type: 'danger', title: 'Hapus Divisi', confirmText: 'Ya, Hapus'})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Hapus">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <x-heroicon-o-building-office class="w-16 h-16 mb-4 opacity-30" />
                                <p class="text-lg font-medium">Belum ada data divisi</p>
                                <p class="text-sm mt-1">Klik tombol "Tambah Divisi" untuk menambahkan data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($divisions->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            {{ $divisions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
