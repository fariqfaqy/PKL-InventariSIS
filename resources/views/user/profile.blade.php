@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] px-6 py-8 text-white">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-4xl font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-1">{{ auth()->user()->name }}</h1>
                    <p class="text-white/90 text-lg">{{ auth()->user()->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm">
                            <x-heroicon-s-user class="w-3.5 h-3.5 mr-1" />
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        @if(auth()->user()->division)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm">
                            <x-heroicon-s-building-office-2 class="w-3.5 h-3.5 mr-1" />
                            {{ auth()->user()->division->nama_divisi }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500" />
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-3">
        <x-heroicon-s-x-circle class="w-5 h-5 text-red-500" />
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informasi Akun -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-s-user class="w-6 h-6 text-blue-600" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Akun</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                        {{ auth()->user()->name }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                        {{ auth()->user()->email }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                            {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Pegawai -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-s-identification class="w-6 h-6 text-green-600" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">Data Pegawai</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 font-mono text-gray-900">
                        {{ auth()->user()->nip ?? '-' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                        @if(auth()->user()->division)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <x-heroicon-s-building-office-2 class="w-3.5 h-3.5 mr-1.5" />
                            {{ auth()->user()->division->nama_divisi }}
                        </span>
                        @else
                        <span class="text-gray-500">-</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                        {{ auth()->user()->jabatan ?? '-' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                        {{ auth()->user()->no_telp ?? '-' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-900">
                        @if(auth()->user()->tanggal_masuk)
                            {{ auth()->user()->tanggal_masuk->format('d F Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Password Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <x-heroicon-s-lock-closed class="w-6 h-6 text-orange-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-900">Ubah Password</h2>
        </div>

        <form action="{{ route('user.profile.update-password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                    <input type="password" name="current_password" id="current_password" 
                        class="w-full px-4 py-2.5 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                        placeholder="Masukkan password lama">
                    @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" 
                        class="w-full px-4 py-2.5 border {{ $errors->has('new_password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                        placeholder="Masukkan password baru">
                    @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent"
                        placeholder="Konfirmasi password baru">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white font-semibold rounded-lg hover:from-[#0d7a8f] hover:to-[#14a2ba] transition-all duration-200 shadow-md hover:shadow-lg">
                    <x-heroicon-s-lock-closed class="w-4 h-4 mr-2" />
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Statistik Aktivitas (Optional) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <x-heroicon-s-chart-bar class="w-6 h-6 text-purple-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-900">Statistik Aktivitas</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-700">Request Pending</span>
                    <x-heroicon-s-clock class="w-5 h-5 text-blue-600" />
                </div>
                <p class="text-2xl font-bold text-blue-900">
                    {{ auth()->user()->requestBarangs()->where('status', 'pending')->count() }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-green-700">Request Disetujui</span>
                    <x-heroicon-s-check-circle class="w-5 h-5 text-green-600" />
                </div>
                <p class="text-2xl font-bold text-green-900">
                    {{ auth()->user()->requestBarangs()->where('status', 'approved')->count() }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-purple-700">Total Request</span>
                    <x-heroicon-s-clipboard-document-list class="w-5 h-5 text-purple-600" />
                </div>
                <p class="text-2xl font-bold text-purple-900">
                    {{ auth()->user()->requestBarangs()->count() }}
                </p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-orange-700">Request Selesai</span>
                    <x-heroicon-s-check-badge class="w-5 h-5 text-orange-600" />
                </div>
                <p class="text-2xl font-bold text-orange-900">
                    {{ auth()->user()->requestBarangs()->where('status', 'completed')->count() }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
