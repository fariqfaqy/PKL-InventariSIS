@extends('layouts.user')

@section('title', 'Barang di Rak')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-gray-800">Barang di Rak</h2>
            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full border border-gray-300">
                <x-heroicon-o-eye class="w-3 h-3" />
                Read Only
            </span>
        </div>
        <p class="text-gray-600 mt-1">Lihat penempatan barang di rak penyimpanan (Admin yang mengelola penempatan barang)</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" />
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('user.barang-rak.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Barang</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode atau nama barang..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
            </div>
            
            <!-- Filter Rak -->
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Rak</label>
                <select name="rack" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14a2ba] focus:border-transparent">
                    <option value="">Semua Rak</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack }}" {{ request('rack') == $rack ? 'selected' : '' }}>Rak {{ strtoupper($rack) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Button -->
            <div class="md:col-span-1 flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    <span class="font-medium">Cari</span>
                </button>
                @if(request('search') || request('rack'))
                    <a href="{{ route('user.barang-rak.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div id="table-container" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Rak</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Kode Barang</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nama Barang</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Jumlah</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Keterangan</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Update Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assignments as $index => $assignment)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $assignments->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    <x-heroicon-o-archive-box class="w-4 h-4" />
                                    {{ strtoupper($assignment->rack) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $assignment->kodebarang }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $assignment->namabarang }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-lg">
                                    {{ $assignment->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $assignment->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $assignment->updated_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <x-heroicon-o-archive-box class="w-16 h-16 mb-4" />
                                    <p class="text-lg font-medium">Belum ada barang di rak</p>
                                    <p class="text-sm mt-1">Admin akan menambahkan barang melalui barang masuk</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assignments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

<script>
(function() {
    const filterForm = document.querySelector('form[action="{{ route('user.barang-rak.index') }}"]');
    const searchInput = filterForm.querySelector('input[name="search"]');
    const rackSelect = filterForm.querySelector('select[name="rack"]');
    const tableContainer = document.getElementById('table-container');
    let searchTimeout, isLoading = false;
    
    function fetchData(url) {
        if (isLoading) return;
        isLoading = true;
        tableContainer.style.opacity = '0.6';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text()).then(html => {
            const newTable = new DOMParser().parseFromString(html, 'text/html').getElementById('table-container');
            if (newTable) { tableContainer.innerHTML = newTable.innerHTML; attachPaginationListeners(); }
            window.history.pushState({}, '', url);
            isLoading = false; tableContainer.style.opacity = '1';
        });
    }
    filterForm.addEventListener('submit', function(e) { e.preventDefault(); fetchData('{{ route('user.barang-rak.index') }}?' + new URLSearchParams(new FormData(this))); });
    searchInput.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => filterForm.dispatchEvent(new Event('submit')), 500); });
    rackSelect.addEventListener('change', () => filterForm.dispatchEvent(new Event('submit')));
    function attachPaginationListeners() { document.querySelectorAll('#table-container .pagination a').forEach(l => l.addEventListener('click', function(e) { e.preventDefault(); fetchData(this.href); window.scrollTo({ top: 0, behavior: 'smooth' }); })); }
    attachPaginationListeners();
})();
</script>
</div>
@endsection