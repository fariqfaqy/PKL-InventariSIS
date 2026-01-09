<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard') - InventariSIS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pln-logo.png') }}">
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

                <!-- Kelola Barang (Parent) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('kelola-barang')" class="w-full flex items-center justify-between gap-3 px-4 py-3 {{ request()->routeIs('user.stok-barang.*') || request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') || request()->routeIs('user.pemakaian.*') || request()->routeIs('user.barang-rak.*') ? 'text-[#14a2ba] bg-[#14a2ba]/5' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-cube class="w-5 h-5 {{ request()->routeIs('user.stok-barang.*') || request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') || request()->routeIs('user.pemakaian.*') || request()->routeIs('user.barang-rak.*') ? 'text-[#14a2ba]' : 'group-hover:text-[#14a2ba]' }}" />
                            <span class="font-medium">Kelola Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="kelola-barang-icon" class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('user.stok-barang.*') || request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') || request()->routeIs('user.pemakaian.*') || request()->routeIs('user.barang-rak.*') ? 'rotate-180' : '' }}" />
                    </button>
                    
                    <!-- Submenu -->
                    <div id="kelola-barang-submenu" class="{{ request()->routeIs('user.stok-barang.*') || request()->routeIs('user.barang-masuk.*') || request()->routeIs('user.barang-keluar.*') || request()->routeIs('user.pemakaian.*') || request()->routeIs('user.barang-rak.*') ? '' : 'hidden' }} ml-4 space-y-1">
                        <!-- Barang di Rak -->
                        <a href="{{ route('user.barang-rak.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.barang-rak.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-archive-box class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang di Rak</span>
                        </a>
                        
                        <!-- Stok Barang (Read Only) -->
                        <a href="{{ route('user.stok-barang.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.stok-barang.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-cube-transparent class="w-4 h-4" />
                            <span class="text-sm font-medium">Stok Barang</span>
                        </a>
                        
                        <!-- Barang Masuk (Read Only) -->
                        <a href="{{ route('user.barang-masuk.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.barang-masuk.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Masuk</span>
                        </a>

                        <!-- Barang Keluar (Read Only) -->
                        <a href="{{ route('user.barang-keluar.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.barang-keluar.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                            <span class="text-sm font-medium">Barang Keluar</span>
                        </a>

                        <!-- Pemakaian Barang -->
                        <a href="{{ route('user.pemakaian.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('user.pemakaian.*') ? 'text-[#14a2ba] bg-[#14a2ba]/10' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                            <span class="text-sm font-medium">Pemakaian Saya</span>
                        </a>
                    </div>
                </div>

                <div class="border-t border-gray-200 my-4"></div>

                <!-- Profile -->
                <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('user.profile') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-user class="w-5 h-5" />
                    <span class="font-medium">Profil Saya</span>
                </a>

                <!-- Pengaturan -->
                <a href="{{ route('user.settings') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('user.settings') ? 'text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f]' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
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
