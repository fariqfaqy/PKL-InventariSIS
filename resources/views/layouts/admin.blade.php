<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - InventariSIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-gray-200 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-lg">
                        <img src="{{ asset('images/pln-logo.png') }}" alt="PLN Logo" class="w-10 h-10">
                    </div>
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
                        <!-- Barang Sewa -->
                        <a href="{{ route('admin.stok-barang.index', ['kategori' => 'barang_sewa']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.stok-barang.*') && request('kategori') == 'barang_sewa' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-computer-desktop class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Sewa</span>
                        </a>
                        
                        <!-- Barang Habis Pakai -->
                        <a href="{{ route('admin.stok-barang.index', ['kategori' => 'habis_pakai']) }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('admin.stok-barang.*') && request('kategori') == 'habis_pakai' ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-shopping-bag class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Habis Pakai</span>
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

                <!-- Kelola User -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-users class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? '' : 'group-hover:text-[#14a2ba]' }}" />
                    <span class="font-medium">Kelola User</span>
                </a>

                <!-- Laporan -->
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-chart-bar class="w-5 h-5 group-hover:text-[#14a2ba]" />
                    <span class="font-medium">Laporan</span>
                </a>

                <div class="border-t border-gray-200 my-4"></div>

                <!-- Pengaturan -->
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 group-hover:text-[#14a2ba]" />
                    <span class="font-medium">Pengaturan</span>
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
                            <p class="text-xs text-gray-500">Administrator</p>
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
                        &copy; 2026 InventariSIS - PT PLN (Persero)
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
    </script>
</body>
</html>
