<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InventariSIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="#">Kelola User</a></li>
                    <li><a href="#">Kelola Inventaris</a></li>
                    <li><a href="#">Laporan</a></li>
                    <li><a href="#">Pengaturan</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Selamat Datang di Panel Admin</h2>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total User</h3>
                    <p class="stat-number">150</p>
                </div>
                <div class="stat-card">
                    <h3>Total Inventaris</h3>
                    <p class="stat-number">1,234</p>
                </div>
                <div class="stat-card">
                    <h3>Pending Approval</h3>
                    <p class="stat-number">12</p>
                </div>
                <div class="stat-card">
                    <h3>Laporan Bulan Ini</h3>
                    <p class="stat-number">45</p>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Aktivitas Terbaru</h3>
                <ul>
                    <li>User baru mendaftar: John Doe</li>
                    <li>Inventaris baru ditambahkan: Laptop Dell</li>
                    <li>Laporan disetujui oleh Admin</li>
                </ul>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 InventariSIS - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
