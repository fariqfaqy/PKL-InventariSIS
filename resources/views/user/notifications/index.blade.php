@extends('layouts.user')

@section('title', 'Notifikasi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Notifikasi</h2>
            <p class="text-sm text-gray-500 mt-1">Semua notifikasi aktivitas permintaan Anda</p>
        </div>
        @if($notifications->total() > 0)
        <a href="{{ route('user.notifications.mark-all-read') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <x-heroicon-o-check class="w-4 h-4 mr-2" />
            Tandai Semua Dibaca
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @forelse($notifications as $notification)
        <div class="p-6 border-b border-gray-200 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
            <div class="flex items-start gap-4">
                <div class="shrink-0 w-12 h-12 rounded-full {{ $notification->data['action'] === 'approved' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                    @if($notification->data['action'] === 'approved')
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                    @else
                        <x-heroicon-o-x-circle class="w-6 h-6 text-red-600" />
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $notification->data['message'] ?? 'Notifikasi' }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>Barang:</strong> {{ $notification->data['barang'] ?? '' }} 
                                <span class="text-gray-500">({{ $notification->data['qty'] ?? '' }} unit)</span>
                            </p>
                            @if($notification->data['admin_note'] ?? false)
                            <div class="mt-2 p-3 bg-gray-100 rounded-lg">
                                <p class="text-xs font-medium text-gray-600">Catatan Admin:</p>
                                <p class="text-sm text-gray-700 italic">"{{ $notification->data['admin_note'] }}"</p>
                            </div>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @if(is_null($notification->read_at))
                        <span class="shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                        @endif
                    </div>
                    @if($notification->data['url'] ?? false)
                    <a href="{{ route('user.pemakaian.index') }}" 
                       class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <x-heroicon-o-arrow-right class="w-4 h-4 mr-1" />
                        Lihat Permintaan Saya
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <x-heroicon-o-bell-slash class="w-16 h-16 mx-auto mb-4 text-gray-400" />
            <p class="text-lg font-medium text-gray-500">Tidak ada notifikasi</p>
            <p class="text-sm text-gray-400 mt-1">Notifikasi akan muncul di sini ketika admin memproses request Anda</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
