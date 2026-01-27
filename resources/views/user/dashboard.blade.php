@extends('layouts.user')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang kembali!')

@section('content')
<div class="space-y-6">
<!-- Welcome Card -->
<div class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] rounded-2xl shadow-lg p-8 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold mb-2 flex items-center gap-2">
                Selamat Datang, {{ Auth::user()->name }}!
            </h3>
            <p class="text-white/90 flex items-center gap-2">
                <x-heroicon-o-building-office class="w-5 h-5" />
                Sistem Informasi Inventaris PLN Indonesia Power
            </p>
        </div>
        <div class="hidden lg:block">
            <x-heroicon-o-clipboard-document-list class="w-24 h-24 opacity-20" />
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Barang -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#14a2ba]">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Total Barang</p>
                <h4 class="text-3xl font-bold text-[#14a2ba] mt-2">{{ $totalBarang }}</h4>
            </div>
            <div class="w-12 h-12 bg-[#14a2ba]/10 rounded-lg flex items-center justify-center">
                <x-heroicon-o-cube class="w-8 h-8 text-[#14a2ba]" />
            </div>
        </div>
        <p class="text-sm text-gray-500 flex items-center gap-1">
            <x-heroicon-o-archive-box class="w-4 h-4" />
            Total {{ $totalStok }} unit stok
        </p>
    </div>

    <!-- Stok Aman -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-green-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Aman</p>
                <h4 class="text-3xl font-bold text-green-600 mt-2">{{ $stokAman }}</h4>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-check-circle class="w-8 h-8 text-green-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok ≥ 10 unit</p>
    </div>

    <!-- Stok Menengah -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Menengah</p>
                <h4 class="text-3xl font-bold text-yellow-600 mt-2">{{ $stokMenengah }}</h4>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-exclamation-circle class="w-8 h-8 text-yellow-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok 5-9 unit</p>
    </div>

    <!-- Stok Kritis -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase">Stok Kritis</p>
                <h4 class="text-3xl font-bold text-red-600 mt-2">{{ $stokKritis }}</h4>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-red-600" />
            </div>
        </div>
        <p class="text-sm text-gray-500">Stok ≤ 4 unit</p>
    </div>
</div>

<!-- Rak Grid -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-5 h-5 text-[#14a2ba]" />
            Status Rak Inventaris
        </h3>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($raks as $rak)
        <a href="{{ route('user.barang-rak.index', ['rack' => $rak]) }}" class="bg-gradient-to-br from-gray-50 to-gray-100 hover:from-[#14a2ba]/10 hover:to-[#0d7a8f]/10 rounded-lg p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-md border border-gray-200 hover:border-[#14a2ba]">
            <div class="text-3xl font-bold text-[#14a2ba] mb-1">{{ strtoupper($rak) }}</div>
            <div class="text-xs text-gray-500 font-medium mb-2">Rak {{ strtoupper($rak) }}</div>
            <div class="text-sm font-semibold text-gray-700">
                {{ $barangPerRak->get($rak)->total ?? 0 }} items
            </div>
            <div class="text-xs text-gray-500">
                {{ $barangPerRak->get($rak)->total_stock ?? 0 }} unit
            </div>
        </a>
        @endforeach
    </div>
</div>

<!-- Riwayat Pemakaian Terbaru -->
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex items-center gap-2 mb-4">
        <x-heroicon-o-clock class="w-5 h-5 text-[#14a2ba]" />
        <h3 class="text-lg font-bold text-gray-800">Pinjaman Aset Sewa Sedang Progress</h3>
    </div>
    
    <div id="pemakaian-container">
        @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pemakaian-tbody" class="bg-white divide-y divide-gray-200">
                    @foreach($recentTransactions as $trans)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $trans->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div>
                                <div class="text-gray-900">{{ $trans->namabarang_k }}</div>
                                @if($trans->kategori === 'aset_sewa')
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <x-heroicon-o-calendar class="w-3 h-3 mr-1" />
                                            Sewa
                                        </span>
                                        @if($trans->durasi_sewa)
                                            <span class="text-xs text-gray-500">{{ $trans->durasi_sewa }} Tahun</span>
                                        @endif
                                    </div>
                                    @if($trans->tanggal_akhir_sewa)
                                        @php
                                            $sisaHari = now()->startOfDay()->diffInDays($trans->tanggal_akhir_sewa, false);
                                        @endphp
                                        <div class="text-xs mt-1 {{ $sisaHari < 0 ? 'text-red-600' : ($sisaHari <= 7 ? 'text-yellow-600' : 'text-gray-500') }}">
                                            Berakhir: {{ $trans->tanggal_akhir_sewa->format('d/m/Y') }}
                                            @if($sisaHari >= 0)
                                                ({{ $sisaHari }} hari lagi)
                                            @else
                                                (Sudah berakhir)
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $trans->qty }} unit
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $trans->penerima }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <a href="{{ route('user.pemakaian.show', $trans->id_request) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#14a2ba] hover:bg-[#0d7a8f] text-white rounded-lg text-xs font-medium transition-colors">
                                <x-heroicon-o-eye class="w-4 h-4" />
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div id="empty-state" class="text-center py-8 text-gray-500">
            <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm">Tidak ada pinjaman aset sewa yang sedang berjalan</p>
            <p class="text-xs text-gray-400 mt-1">Pinjaman aset sewa yang sudah disetujui admin akan muncul di sini</p>
        </div>
        @endif
    </div>
</div>

<script>
let refreshInterval;
let isRefreshing = false;

// Fungsi untuk update tabel pemakaian
function updatePemakaianTable(data) {
    const tbody = document.getElementById('pemakaian-tbody');
    const container = document.getElementById('pemakaian-container');
    const emptyState = document.getElementById('empty-state');
    
    if (!data || data.length === 0) {
        // Tampilkan empty state jika tidak ada data
        if (tbody) tbody.closest('table').closest('.overflow-x-auto').style.display = 'none';
        if (emptyState) {
            emptyState.style.display = 'block';
        } else {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm">Tidak ada pinjaman aset sewa yang sedang berjalan</p>
                    <p class="text-xs text-gray-400 mt-1">Pinjaman aset sewa yang sudah disetujui admin akan muncul di sini</p>
                </div>
            `;
        }
        return;
    }
    
    // Hide empty state dan tampilkan tabel
    if (emptyState) emptyState.style.display = 'none';
    if (tbody) {
        tbody.closest('table').closest('.overflow-x-auto').style.display = 'block';
        
        // Update konten tabel
        tbody.innerHTML = data.map(trans => {
            let kategoriInfo = '';
            let sewaDetails = '';
            
            if (trans.is_aset_sewa) {
                let sewaColorClass = 'text-gray-500';
                if (trans.is_expired) {
                    sewaColorClass = 'text-red-600';
                } else if (trans.is_near_expiry) {
                    sewaColorClass = 'text-yellow-600';
                }
                
                kategoriInfo = `
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Sewa
                        </span>
                        ${trans.durasi_sewa ? `<span class="text-xs text-gray-500">${trans.durasi_sewa} Tahun</span>` : ''}
                    </div>
                `;
                
                if (trans.tanggal_akhir_sewa) {
                    sewaDetails = `
                        <div class="text-xs mt-1 ${sewaColorClass}">
                            Berakhir: ${trans.tanggal_akhir_sewa}
                            ${trans.sisa_hari_text ? `(${trans.sisa_hari_text})` : ''}
                        </div>
                    `;
                }
            }
            
            return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${trans.tanggal}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div>
                            <div class="text-gray-900">${trans.namabarang}</div>
                            ${kategoriInfo}
                            ${sewaDetails}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            ${trans.qty} unit
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        ${trans.penerima}
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <a href="/user/pemakaian/${trans.id}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#14a2ba] hover:bg-[#0d7a8f] text-white rounded-lg text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }
}

// Fungsi untuk fetch data dari API
async function refreshPemakaianData() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    
    try {
        const response = await fetch('{{ route("user.dashboard.pemakaian-aktif") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const result = await response.json();
        
        if (result.success) {
            updatePemakaianTable(result.data);
        }
    } catch (error) {
        console.error('Error fetching pemakaian data:', error);
    } finally {
        isRefreshing = false;
    }
}

// Start auto-refresh saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Refresh pertama kali setelah 2 detik
    setTimeout(refreshPemakaianData, 2000);
    
    // Set interval untuk refresh setiap 10 detik
    refreshInterval = setInterval(refreshPemakaianData, 10000);
});

// Stop refresh saat user meninggalkan halaman
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    } else {
        // Resume refresh ketika user kembali
        refreshPemakaianData();
        refreshInterval = setInterval(refreshPemakaianData, 10000);
    }
});
</script>
</div>
@endsection