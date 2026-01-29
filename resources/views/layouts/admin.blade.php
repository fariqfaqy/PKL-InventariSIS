<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - InventariSIS</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/pln-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/pln-logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pln-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 transition-colors duration-300">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-gray-200 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]">
                <div class="flex items-center gap-3 px-4">
                    <img src="{{ asset('images/pln-logo.png') }}" alt="PLN Logo" class="w-14 h-14 object-contain bg-white rounded-md p-1">
                    <div class="text-white">
                        <h1 class="text-xl font-bold">InventariSIS</h1>
                        <p class="text-xs opacity-90">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-5rem)]">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Stok Barang (Parent with Submenu) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('stok-barang')" class="w-full flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('admin.stok-barang.*') ? 'text-[#14a2ba] bg-[#14a2ba]/5' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-cube-transparent class="w-5 h-5 {{ request()->routeIs('admin.stok-barang.*') ? 'text-[#14a2ba]' : 'group-hover:text-[#14a2ba]' }}" />
                            <span class="font-medium">Stok Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="stok-barang-icon" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('admin.stok-barang.*') ? 'rotate-180' : '' }}" />
                    </button>
                    
                    <!-- Submenu Stok Barang -->
                    <div id="stok-barang-submenu" class="{{ request()->routeIs('admin.stok-barang.*') ? '' : 'hidden' }} ml-4 space-y-1">
                        <!-- Aset Sewa -->
                        <a href="{{ route('admin.stok-barang.index', ['kategori' => 'aset_sewa']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.stok-barang.*') && request('kategori') == 'aset_sewa' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-computer-desktop class="w-4 h-4" />
                            <span class="text-sm font-medium">Aset Sewa</span>
                        </a>
                        
                        <!-- Material Umum -->
                        <a href="{{ route('admin.stok-barang.index', ['kategori' => 'material_umum']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.stok-barang.*') && request('kategori') == 'material_umum' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-shopping-bag class="w-4 h-4" />
                            <span class="text-sm font-medium">Material Umum</span>
                        </a>
                        
                        <!-- Aset Tetap -->
                        <a href="{{ route('admin.stok-barang.index', ['kategori' => 'aset_tetap']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.stok-barang.*') && request('kategori') == 'aset_tetap' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-building-office class="w-4 h-4" />
                            <span class="text-sm font-medium">Aset Tetap</span>
                        </a>
                    </div>
                </div>

                <!-- Kelola Barang (Parent) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('kelola-barang')" class="w-full flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('admin.barang-masuk.*') || request()->routeIs('admin.barang-keluar.*') ? 'text-[#14a2ba] bg-[#14a2ba]/5' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-cube class="w-5 h-5 {{ request()->routeIs('admin.barang-masuk.*') || request()->routeIs('admin.barang-keluar.*') ? 'text-[#14a2ba]' : 'group-hover:text-[#14a2ba]' }}" />
                            <span class="font-medium">Kelola Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="kelola-barang-icon" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('admin.barang-masuk.*') || request()->routeIs('admin.barang-keluar.*') ? 'rotate-180' : '' }}" />
                    </button>
                    
                    <!-- Submenu -->
                    <div id="kelola-barang-submenu" class="{{ request()->routeIs('admin.barang-masuk.*') || request()->routeIs('admin.barang-keluar.*') ? '' : 'hidden' }} ml-4 space-y-1">
                        <!-- Barang Masuk -->
                        <a href="{{ route('admin.barang-masuk.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.barang-masuk.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Masuk</span>
                        </a>

                        <!-- Barang Keluar -->
                        <a href="{{ route('admin.barang-keluar.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.barang-keluar.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Keluar</span>
                        </a>
                    </div>
                </div>

                <!-- Kelola Permintaan -->
                <a href="{{ route('admin.permintaan.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.permintaan.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group relative">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 {{ request()->routeIs('admin.permintaan.*') ? '' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Kelola Permintaan</span>
                    @php
                        $pendingCount = \App\Models\RequestBarang::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="absolute -top-1 -right-1 px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <!-- Kelola User -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-users class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? '' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Kelola Pegawai</span>
                </a>

                <!-- Kelola Divisi -->
                <a href="{{ route('admin.divisions.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.divisions.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-building-office class="w-5 h-5 {{ request()->routeIs('admin.divisions.*') ? '' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Kelola Divisi</span>
                </a>

                <div class="border-t border-gray-200 my-4"></div>

                <!-- Activity Log -->
                <a href="{{ route('admin.activity-log.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.activity-log.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-document-text class="w-5 h-5 {{ request()->routeIs('admin.activity-log.*') ? 'text-white' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Activity Log</span>
                </a>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <!-- Mobile Menu Button & Breadcrumb -->
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-[#14a2ba] transition-colors">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500">@yield('subtitle', 'SIS')</p>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    <!-- User Menu -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6 mt-auto">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p class="text-sm text-gray-600 flex items-center gap-2">
                        <x-heroicon-o-heart class="w-4 h-4 text-red-500" />
                        &copy; 2026 InventariSIS - PLN Indonesia Power 
                    </p>
                    <p class="text-xs text-gray-500">Version 1.0.0</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Global Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm z-[9999] flex items-center justify-center transition-all duration-300 opacity-0">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl transform scale-90 transition-all duration-300 border border-gray-100" onclick="event.stopPropagation()">
            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div id="confirmIcon" class="w-16 h-16 rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            
            <!-- Title & Message -->
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2" id="confirmTitle">Konfirmasi</h3>
            <p class="text-sm text-gray-600 text-center mb-6" id="confirmMessage"></p>
            
            <!-- Buttons -->
            <div class="flex gap-3" id="confirmButtons">
                <button type="button" 
                        id="cancelButton"
                        onclick="closeConfirmModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium">
                    Batal
                </button>
                <button type="button" 
                        id="confirmButton"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        // Submenu toggle function
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId + '-submenu');
            const icon = document.getElementById(menuId + '-icon');
            
            submenu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Global Confirm Modal Functions
        let confirmCallback = null;

        function showConfirm(message, callback, options = {}) {
            const modal = document.getElementById('confirmModal');
            const title = options.title || 'Konfirmasi';
            const confirmText = options.confirmText || 'Ya, Lanjutkan';
            const type = options.type || 'warning'; // warning, danger, success
            const isAlert = confirmText === 'OK';
            
            // Set content
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmButton').textContent = confirmText;
            
            // Hide cancel button for alerts
            const cancelBtn = document.getElementById('cancelButton');
            const confirmBtn = document.getElementById('confirmButton');
            if (isAlert) {
                cancelBtn.classList.add('hidden');
                confirmBtn.classList.remove('flex-1');
                confirmBtn.classList.add('w-full');
            } else {
                cancelBtn.classList.remove('hidden');
                confirmBtn.classList.remove('w-full');
                confirmBtn.classList.add('flex-1');
            }
            
            // Set icon and color based on type
            const iconContainer = document.getElementById('confirmIcon');
            
            if (type === 'danger') {
                iconContainer.className = 'w-16 h-16 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center';
                iconContainer.innerHTML = '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>';
                confirmBtn.className = 'flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium';
            } else if (type === 'success') {
                iconContainer.className = 'w-16 h-16 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center';
                iconContainer.innerHTML = '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                confirmBtn.className = 'flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium';
            } else {
                iconContainer.className = 'w-16 h-16 rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 flex items-center justify-center';
                iconContainer.innerHTML = '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
                if (isAlert) {
                    confirmBtn.className = 'w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium';
                } else {
                    confirmBtn.className = 'flex-1 px-4 py-2.5 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium';
                }
            }
            
            confirmCallback = callback;
            
            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-90');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.remove('scale-100');
            modal.querySelector('div').classList.add('scale-90');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                confirmCallback = null;
            }, 300);
        }

        // Confirm button click
        document.getElementById('confirmButton')?.addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            closeConfirmModal();
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
            }
        });

        // Helper function to replace default confirm
        function customConfirm(event, message, options = {}) {
            event.preventDefault();
            const form = event.target.closest('form');
            showConfirm(message, () => {
                if (form) {
                    form.submit();
                }
            }, options);
            return false;
        }

        // Custom Alert Function
        function customAlert(message, options = {}) {
            const type = options.type || 'warning';
            const title = options.title || 'Peringatan';
            showConfirm(message, () => {}, { 
                ...options, 
                type: type, 
                title: title,
                confirmText: 'OK'
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
