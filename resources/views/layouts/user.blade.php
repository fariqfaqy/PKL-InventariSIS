<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard') - InventariSIS</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/pln-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/pln-logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pln-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-gray-200 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]">
                <div class="flex items-center gap-3 px-4">
                    <img src="{{ asset('images/pln-logo.png') }}" alt="PLN Logo" class="w-14 h-14 object-contain bg-white rounded-md p-1">
                    <div class="text-white">
                        <h1 class="text-xl font-bold">InventariSIS</h1>
                        <p class="text-xs opacity-90">User Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-5rem)]">
                <!-- Dashboard -->
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('user.dashboard') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Stok Barang (Parent with Submenu) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('stok-barang-user')" class="w-full flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('user.stok-barang.*') ? 'text-[#14a2ba] bg-[#14a2ba]/5' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-cube-transparent class="w-5 h-5 {{ request()->routeIs('user.stok-barang.*') ? 'text-[#14a2ba]' : 'group-hover:text-[#14a2ba]' }}" />
                            <span class="font-medium">Stok Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="stok-barang-user-icon" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('user.stok-barang.*') ? 'rotate-180' : '' }}" />
                    </button>
                    
                    <!-- Submenu Stok Barang -->
                    <div id="stok-barang-user-submenu" class="{{ request()->routeIs('user.stok-barang.*') ? '' : 'hidden' }} ml-4 space-y-1">
                        <!-- Aset Sewa -->
                        <a href="{{ route('user.stok-barang.index', ['kategori' => 'barang_sewa']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.stok-barang.*') && request('kategori') == 'barang_sewa' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                            <x-heroicon-o-computer-desktop class="w-4 h-4" />
                            <span class="text-sm font-medium">Aset Sewa</span>
                        </a>
                        
                        <!-- Material Umum -->
                        <a href="{{ route('user.stok-barang.index', ['kategori' => 'habis_pakai']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.stok-barang.*') && request('kategori') == 'habis_pakai' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                            <x-heroicon-o-shopping-bag class="w-4 h-4" />
                            <span class="text-sm font-medium">Material Umum</span>
                        </a>
                        
                        <!-- Aset Tetap -->
                        <a href="{{ route('user.stok-barang.index', ['kategori' => 'aset_tetap']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.stok-barang.*') && request('kategori') == 'aset_tetap' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                            <x-heroicon-o-building-office class="w-4 h-4" />
                            <span class="text-sm font-medium">Aset Tetap</span>
                        </a>
                    </div>
                </div>

                <!-- Kelola Barang (Parent) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('riwayat-barang')" class="w-full flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') ? 'text-[#14a2ba] bg-[#14a2ba]/5' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-clipboard-document-check class="w-5 h-5 {{ request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') ? 'text-[#14a2ba]' : 'group-hover:text-[#14a2ba]' }}" />
                            <span class="font-medium">Riwayat Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="riwayat-barang-icon" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') ? 'rotate-180' : '' }}" />
                    </button>
                    
                    <!-- Submenu -->
                    <div id="riwayat-barang-submenu" class="{{ request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') ? '' : 'hidden' }} ml-4 space-y-1">
                        <!-- Barang Masuk -->
                        <a href="{{ route('user.barang-masuk.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.barang-masuk.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Masuk</span>
                        </a>

                        <!-- Barang Keluar -->
                        <a href="{{ route('user.barang-keluar.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.barang-keluar.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Keluar</span>
                        </a>
                    </div>
                </div>

                <!-- Barang di Rak (Read Only - Standalone) -->
                <a href="{{ route('user.barang-rak.index') }}" class="flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('user.barang-rak.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-archive-box class="w-5 h-5" />
                        <span class="font-medium">Barang di Rak</span>
                    </div>
                    <span class="text-xs px-2 py-0.5 {{ request()->routeIs('user.barang-rak.*') ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} rounded">Lihat</span>
                </a>

                <!-- Pemakaian Barang (CRUD - Standalone) -->
                <a href="{{ route('user.pemakaian.index') }}" class="flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('user.pemakaian.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                        <span class="font-medium">Pemakaian Saya</span>
                    </div>
                    @if(isset($userNotificationCount) && $userNotificationCount > 0)
                        <span id="pemakaian-sidebar-badge" class="flex items-center justify-center min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold rounded-full">
                            {{ $userNotificationCount > 9 ? '9+' : $userNotificationCount }}
                        </span>
                    @endif
                </a>

                <div class="border-t border-gray-200 my-4"></div>

                <!-- Profile -->
                <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('user.profile') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-user class="w-5 h-5" />
                    <span class="font-medium">Profil Saya</span>
                </a>

                <div class="border-t border-gray-200 my-4"></div>

                <!-- Activity Log - Temporarily Disabled -->
                {{-- <a href="{{ route('user.activity-log.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('user.activity-log.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-document-text class="w-5 h-5 {{ request()->routeIs('user.activity-log.*') ? 'text-white' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Activity Log</span>
                </a> --}}

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
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <!-- Mobile Menu Button & Breadcrumb -->
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-[#14a2ba] transition-colors">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-500">@yield('subtitle', 'Selamat datang kembali!')</p>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    <!-- User Menu -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">User Divisi</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
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

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Submenu toggle
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id + '-submenu');
            const icon = document.getElementById(id + '-icon');
            submenu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>
