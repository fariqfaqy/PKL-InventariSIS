<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - InventariSIS</title>
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] rounded-lg transition-all duration-200">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Kelola Barang (Parent) -->
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('kelola-barang')" class="w-full flex items-center justify-between gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-cube class="w-5 h-5 group-hover:text-[#14a2ba]" />
                            <span class="font-medium">Kelola Barang</span>
                        </div>
                        <x-heroicon-o-chevron-down id="kelola-barang-icon" class="w-4 h-4 transition-transform duration-200" />
                    </button>
                    
                    <!-- Submenu -->
                    <div id="kelola-barang-submenu" class="hidden ml-4 space-y-1">
                        <!-- Barang Masuk -->
                        <a href="{{ route('admin.barang-masuk.index') }}" class="flex items-center gap-3 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 group-hover:text-[#14a2ba]" />
                            <span class="text-sm font-medium">Barang Masuk</span>
                        </a>

                        <!-- Barang Keluar -->
                        <a href="{{ route('admin.barang-keluar.index') }}" class="flex items-center gap-3 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                            <x-heroicon-o-arrow-up-tray class="w-4 h-4 group-hover:text-[#14a2ba]" />
                            <span class="text-sm font-medium">Barang Keluar</span>
                        </a>
                    </div>
                </div>

                <!-- Kelola User -->
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200 group">
                    <x-heroicon-o-users class="w-5 h-5 group-hover:text-[#14a2ba]" />
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
                        <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
                        <p class="text-sm text-gray-500">Selamat datang kembali!</p>
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
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] rounded-2xl shadow-lg p-8 mb-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2 flex items-center gap-2">
                                Selamat Datang di InventariSIS
                            </h3>
                            <p class="text-white/90 flex items-center gap-2">
                                <x-heroicon-o-building-office class="w-5 h-5" />
                                Sistem Informasi Inventaris PT PLN (Persero)
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
                                <h4 class="text-3xl font-bold text-[#14a2ba] mt-2">0</h4>
                            </div>
                            <div class="bg-[#14a2ba]/10 p-3 rounded-lg">
                                <x-heroicon-o-cube class="w-8 h-8 text-[#14a2ba]" />
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-sm">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4 text-green-500" />
                            <span class="text-green-500 font-medium">0%</span>
                            <span class="text-gray-500">dari bulan lalu</span>
                        </div>
                    </div>

                    <!-- Total User -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#16b8d1]">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase">Total User</p>
                                <h4 class="text-3xl font-bold text-[#16b8d1] mt-2">2</h4>
                            </div>
                            <div class="bg-[#16b8d1]/10 p-3 rounded-lg">
                                <x-heroicon-o-users class="w-8 h-8 text-[#16b8d1]" />
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-sm">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4 text-green-500" />
                            <span class="text-green-500 font-medium">2 Active</span>
                            <span class="text-gray-500">users</span>
                        </div>
                    </div>

                    <!-- Barang Masuk -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#0d7a8f]">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase">Barang Masuk</p>
                                <h4 class="text-3xl font-bold text-[#0d7a8f] mt-2">0</h4>
                            </div>
                            <div class="bg-[#0d7a8f]/10 p-3 rounded-lg">
                                <x-heroicon-o-arrow-down-tray class="w-8 h-8 text-[#0d7a8f]" />
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-sm">
                            <x-heroicon-o-minus class="w-4 h-4 text-gray-400" />
                            <span class="text-gray-500">Bulan ini</span>
                        </div>
                    </div>

                    <!-- Barang Keluar -->
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-l-4 border-[#17ceea]">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase">Barang Keluar</p>
                                <h4 class="text-3xl font-bold text-[#17ceea] mt-2">0</h4>
                            </div>
                            <div class="bg-[#17ceea]/10 p-3 rounded-lg">
                                <x-heroicon-o-arrow-up-tray class="w-8 h-8 text-[#17ceea]" />
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-sm">
                            <x-heroicon-o-minus class="w-4 h-4 text-gray-400" />
                            <span class="text-gray-500">Bulan ini</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <x-heroicon-o-rocket-launch class="w-6 h-6 text-[#14a2ba]" />
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#14a2ba] to-[#0d7a8f] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-plus-circle class="w-10 h-10 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                                <p class="text-sm font-semibold">Tambah Barang</p>
                            </a>
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#16b8d1] to-[#14a2ba] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-arrow-down-circle class="w-10 h-10 mx-auto mb-2 group-hover:translate-y-1 transition-transform" />
                                <p class="text-sm font-semibold">Input Masuk</p>
                            </a>
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#0d7a8f] to-[#0a5f6f] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-arrow-up-circle class="w-10 h-10 mx-auto mb-2 group-hover:-translate-y-1 transition-transform" />
                                <p class="text-sm font-semibold">Output Keluar</p>
                            </a>
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#17ceea] to-[#14a2ba] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-user-plus class="w-10 h-10 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                                <p class="text-sm font-semibold">Tambah User</p>
                            </a>
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#0a5f6f] to-[#084b58] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-document-chart-bar class="w-10 h-10 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                                <p class="text-sm font-semibold">Lihat Laporan</p>
                            </a>
                            <a href="#" class="group p-4 bg-gradient-to-br from-[#14a2ba] to-[#16b8d1] rounded-lg hover:shadow-lg transition-all duration-300 text-white text-center">
                                <x-heroicon-o-arrow-path class="w-10 h-10 mx-auto mb-2 group-hover:rotate-180 transition-transform" />
                                <p class="text-sm font-semibold">Refresh Data</p>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-6 h-6 text-[#14a2ba]" />
                            Aktivitas Terakhir
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-200">
                                <div class="bg-[#14a2ba]/10 p-2 rounded-lg">
                                    <x-heroicon-o-user-circle class="w-5 h-5 text-[#14a2ba]" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Database seeded</p>
                                    <p class="text-xs text-gray-500 mt-1">Admin & User account created</p>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-500 py-8">
                                <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p>Tidak ada aktivitas lainnya</p>
                            </div>
                        </div>
                    </div>
                </div>
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
            
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                submenu.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
