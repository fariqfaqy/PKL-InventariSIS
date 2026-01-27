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

    <!-- Activity Summary (Placeholder - can be extended) -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-[#14a2ba]" />
            Aktivitas
        </h3>
        
        @php
            // Get all outgoing transactions (aset sewa) for this user - ALL statuses
            $userTransactions = \App\Models\OutgoingTransaction::where('user_id', $user->id)
                ->where('kategori', 'aset_sewa')
                ->with(['stock'])
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp
        
        @if($userTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($userTransactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $transaction->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-computer-desktop class="w-5 h-5 text-purple-600" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->namabarang_k }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->kodebarang_k }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            @if($transaction->tanggal_mulai_pakai && $transaction->tanggal_akhir_pakai)
                                {{ \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            @if($transaction->tanggal_mulai_pakai && $transaction->tanggal_akhir_pakai)
                                @php
                                    $start = \Carbon\Carbon::parse($transaction->tanggal_mulai_pakai);
                                    $end = \Carbon\Carbon::parse($transaction->tanggal_akhir_pakai);
                                    $duration = $start->diffInDays($end) + 1;
                                @endphp
                                {{ $duration }} hari
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($transaction->status_approval === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                    Menunggu Approval
                                </span>
                            @elseif($transaction->status_approval === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
                                    Ditolak
                                </span>
                            @elseif($transaction->status_approval === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <x-heroicon-o-no-symbol class="w-3 h-3 mr-1" />
                                    Dibatalkan
                                </span>
                            @elseif($transaction->status === 'sedang_dipakai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                    Sedang Dipakai
                                </span>
                            @elseif($transaction->status === 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Selesai
                                </span>
                            @elseif($transaction->status === 'ditarik')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                    Ditarik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-inbox class="w-16 h-16 mx-auto mb-4 opacity-30" />
            <p class="text-sm">Belum ada aktivitas pemakaian aset sewa</p>
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
            onsubmit="return confirm('Yakin ingin menghapus pegawai {{ $user->name }}?');">
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
