<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - InventariSIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container">
        <header>
            <h1>User Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    <li><a href="#">My Inventaris</a></li>
                    <li><a href="#">Pengajuan</a></li>
                    <li><a href="#">Profile</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Selamat Datang, {{ auth()->user()->name ?? 'User' }}</h2>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Inventaris Saya</h3>
                    <p class="stat-number">15</p>
                </div>
                <div class="stat-card">
                    <h3>Pengajuan Pending</h3>
                    <p class="stat-number">3</p>
                </div>
                <div class="stat-card">
                    <h3>Pengajuan Disetujui</h3>
                    <p class="stat-number">8</p>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <button>Tambah Inventaris</button>
                <button>Buat Pengajuan</button>
                <button>Lihat Laporan</button>
            </div>

            <div class="recent-items">
                <h3>Inventaris Terbaru</h3>
                <ul>
                    <li>Laptop HP - Serial: LP12345</li>
                    <li>Mouse Logitech - Serial: MS54321</li>
                    <li>Keyboard Mechanical - Serial: KB98765</li>
                </ul>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 InventariSIS - User Panel</p>
        </footer>
    </div>
</body>
</html>
