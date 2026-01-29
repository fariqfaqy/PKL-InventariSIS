@extends('layouts.admin')

@section('title', 'Detail Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pegawai</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap pegawai</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" 
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-pencil class="w-5 h-5" /> Edit
            </a>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-32 h-32 rounded-full {{ $user->role === 'admin' ? 'bg-gradient-to-r from-purple-500 to-pink-500' : 'bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' }} flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>

            <!-- User Info -->
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h3>
                <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                
                <div class="flex flex-wrap items-center gap-3 mt-4 justify-center md:justify-start">
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <x-heroicon-o-shield-check class="w-4 h-4 mr-1" />
                            Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <x-heroicon-o-user class="w-4 h-4 mr-1" />
                            User Divisi
                        </span>
                    @endif

                    @if($user->id === auth()->id())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <x-heroicon-o-check-badge class="w-4 h-4 mr-1" />
                            Akun Anda
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
            <x-heroicon-o-information-circle class="w-6 h-6 text-[#14a2ba]" />
            Informasi Akun
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                <p class="text-gray-800 font-semibold">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="text-gray-800">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                <p class="text-gray-800 font-semibold capitalize">{{ $user->role }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terdaftar Sejak</label>
                <p class="text-gray-800">{{ $user->created_at->format('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Employee Data Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
            <x-heroicon-o-identification class="w-6 h-6 text-[#14a2ba]" />
            Data Pegawai
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">NIP</label>
                <p class="text-gray-800 font-mono font-semibold">{{ $user->nip ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Divisi</label>
                @if($user->division)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        <x-heroicon-o-building-office class="w-4 h-4 mr-1" />
                        {{ $user->division->nama_divisi }}
                    </span>
                @else
                    <p class="text-gray-400">-</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jabatan</label>
                <p class="text-gray-800">{{ $user->jabatan ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">No. Telepon</label>
                <p class="text-gray-800">{{ $user->no_telp ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Masuk</label>
                <p class="text-gray-800">{{ $user->tanggal_masuk ? $user->tanggal_masuk->format('d F Y') : '-' }}</p>
            </div>
            @if($user->tanggal_masuk)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Masa Kerja</label>
                <p class="text-gray-800 font-semibold">{{ $user->tanggal_masuk->diffForHumans(null, true) }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-[#14a2ba]" />
            Statistik Aktivitas
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Aktivitas -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-600 font-medium mb-1">Total Aktivitas</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
                    </div>
                    <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-blue-400 opacity-50" />
                </div>
            </div>
            
            <!-- Menyewa Aset -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-purple-600 font-medium mb-1">Menyewa Aset</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $stats['sewa'] }}</p>
                    </div>
                    <x-heroicon-o-building-office-2 class="w-10 h-10 text-purple-400 opacity-50" />
                </div>
            </div>
            
            <!-- Meminjam Barang -->
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg p-4 border border-cyan-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-cyan-600 font-medium mb-1">Meminjam Barang</p>
                        <p class="text-2xl font-bold text-cyan-700">{{ $stats['pinjam'] }}</p>
                    </div>
                    <x-heroicon-o-arrow-path-rounded-square class="w-10 h-10 text-cyan-400 opacity-50" />
                </div>
            </div>
            
            <!-- Memakai Barang -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 font-medium mb-1">Memakai Barang</p>
                        <p class="text-2xl font-bold text-green-700">{{ $stats['pakai'] }}</p>
                    </div>
                    <x-heroicon-o-shopping-bag class="w-10 h-10 text-green-400 opacity-50" />
                </div>
            </div>
        </div>
        
        <!-- Status Summary -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-yellow-600" />
                    <div>
                        <p class="text-xs text-yellow-600 font-medium">Sedang Digunakan</p>
                        <p class="text-lg font-bold text-yellow-700">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-200">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600" />
                    <div>
                        <p class="text-xs text-emerald-600 font-medium">Selesai</p>
                        <p class="text-lg font-bold text-emerald-700">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Details -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
            <x-heroicon-o-clock class="w-6 h-6 text-[#14a2ba]" />
            Riwayat Aktivitas
        </h3>
        
        @if($activities->isEmpty())
        <div class="text-center py-12">
            <x-heroicon-o-inbox class="w-16 h-16 mx-auto mb-4 text-gray-300" />
            <p class="text-gray-500">Belum ada aktivitas</p>
            <p class="text-sm text-gray-400 mt-1">Aktivitas pegawai akan muncul di sini</p>
        </div>
        @else
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @foreach($activities as $activity)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-{{ $activity['color'] }}-50/30">
                <div class="flex items-start justify-between gap-4">
                    <!-- Left: Activity Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <!-- Activity Type Badge -->
                            @if($activity['type'] === 'sewa')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 mr-1" />
                                    {{ $activity['label'] }}
                                </span>
                            @elseif($activity['type'] === 'pinjam')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    <x-heroicon-o-arrow-path-rounded-square class="w-3.5 h-3.5 mr-1" />
                                    {{ $activity['label'] }}
                                </span>
                            @elseif($activity['type'] === 'pakai')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-shopping-bag class="w-3.5 h-3.5 mr-1" />
                                    {{ $activity['label'] }}
                                </span>
                            @endif
                            
                            <!-- Status Badge -->
                            @if($activity['status'] === 'sedang_dipakai')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Selesai
                                </span>
                            @endif
                        </div>
                        
                        <!-- Barang Info -->
                        <h4 class="font-semibold text-gray-800">{{ $activity['barang'] }}</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $activity['kode'] }}</span>
                            <span class="mx-2">•</span>
                            <span class="font-medium">{{ $activity['qty'] }} unit</span>
                        </p>
                        
                        <!-- Dates Info -->
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                <span>{{ \Carbon\Carbon::parse($activity['tanggal'])->format('d M Y') }}</span>
                            </div>
                            
                            @if($activity['type'] === 'pinjam' && $activity['tanggal_pinjam'])
                            <div class="flex items-center gap-1">
                                <x-heroicon-o-arrow-down-on-square class="w-3.5 h-3.5 text-blue-500" />
                                <span>Pinjam: {{ \Carbon\Carbon::parse($activity['tanggal_pinjam'])->format('d M Y') }}</span>
                            </div>
                            @endif
                            
                            @if($activity['type'] === 'pinjam' && $activity['tanggal_kembali'])
                            <div class="flex items-center gap-1">
                                <x-heroicon-o-arrow-up-on-square class="w-3.5 h-3.5 text-green-500" />
                                <span>Kembali: {{ \Carbon\Carbon::parse($activity['tanggal_kembali'])->format('d M Y') }}</span>
                            </div>
                            @endif
                            
                            @if($activity['tanggal_selesai'])
                            <div class="flex items-center gap-1">
                                <x-heroicon-o-check-badge class="w-3.5 h-3.5 text-green-500" />
                                <span>Selesai: {{ \Carbon\Carbon::parse($activity['tanggal_selesai'])->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right: Action Button -->
                    <div>
                        <a href="{{ route('admin.barang-keluar.show', $activity['id']) }}" 
                            class="text-{{ $activity['color'] }}-600 hover:text-{{ $activity['color'] }}-800 transition-colors">
                            <x-heroicon-o-eye class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.users.index') }}" 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Kembali
        </a>
        @if($user->id !== auth()->id())
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
            onsubmit="return customConfirm(event, 'Yakin ingin menghapus pegawai {{ $user->name }}? Data yang terhapus tidak dapat dikembalikan.', {type: 'danger', title: 'Hapus Pegawai', confirmText: 'Ya, Hapus'})">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-trash class="w-5 h-5" /> Hapus Pegawai
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
