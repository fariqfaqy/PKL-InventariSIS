@extends('layouts.admin')

@section('title', 'Detail Divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.divisions.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-800">Detail Divisi</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap divisi</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.divisions.edit', $division->id) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                <x-heroicon-o-pencil class="w-5 h-5" />
                <span class="font-medium">Edit</span>
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Division Info Card -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6 space-y-6">
            <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                <div class="w-16 h-16 rounded-full bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($division->kode_divisi, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-800">{{ $division->nama_divisi }}</h3>
                    <p class="text-sm text-gray-500 font-mono">{{ $division->kode_divisi }}</p>
                </div>
                @if($division->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 mr-2 bg-green-600 rounded-full"></span>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <span class="w-2 h-2 mr-2 bg-gray-600 rounded-full"></span>
                        Nonaktif
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Kepala Divisi</label>
                    <p class="mt-1 text-gray-900 font-semibold">{{ $division->kepala_divisi ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Jumlah Pegawai</label>
                    <p class="mt-1 text-gray-900 font-semibold">{{ $division->users->count() }} orang</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                    <p class="mt-1 text-gray-900">{{ $division->deskripsi ?? '-' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Dibuat</label>
                    <p class="mt-1 text-gray-900">{{ $division->created_at->format('d F Y, H:i') }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                    <p class="mt-1 text-gray-900">{{ $division->updated_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-user-group class="w-8 h-8 text-blue-600" />
                        <div>
                            <p class="text-sm text-gray-600">Total Pegawai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $division->users->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-shield-check class="w-8 h-8 text-purple-600" />
                        <div>
                            <p class="text-sm text-gray-600">Admin</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $division->users->where('role', 'admin')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-cyan-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-user class="w-8 h-8 text-cyan-600" />
                        <div>
                            <p class="text-sm text-gray-600">User</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $division->users->where('role', 'user')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">Daftar Pegawai</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Role</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($division->users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $user->role === 'admin' ? 'bg-gradient-to-r from-purple-500 to-pink-500' : 'bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' }} flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $user->nip ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $user->jabatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $user->email }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    User
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <x-heroicon-o-user-group class="w-12 h-12 mx-auto mb-2 opacity-30" />
                            <p>Belum ada pegawai di divisi ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
