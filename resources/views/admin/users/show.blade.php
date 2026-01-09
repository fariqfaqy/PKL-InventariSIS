@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-[#14a2ba] transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail User</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap pengguna</p>
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
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-information-circle class="w-6 h-6 text-[#14a2ba]" />
            Informasi Akun
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ID User</label>
                <p class="text-gray-800 font-semibold">{{ $user->id }}</p>
            </div>
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
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</label>
                <p class="text-gray-800">{{ $user->updated_at->format('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Activity Summary (Placeholder - can be extended) -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-[#14a2ba]" />
            Aktivitas
        </h3>
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-clock class="w-16 h-16 mx-auto mb-4 opacity-30" />
            <p class="text-sm">Fitur aktivitas akan tersedia segera</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.users.index') }}" 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Kembali
        </a>
        @if($user->id !== auth()->id())
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
            onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-trash class="w-5 h-5" /> Hapus User
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
