<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - InventariSIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }

        .header {
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .header .user-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid white;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .welcome-card h2 {
            color: #1E88E5;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #666;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1E88E5;
        }

        .quick-links {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .quick-links h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .link-card {
            padding: 20px;
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }

        .link-card:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.4);
        }

        .link-card .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .link-card .title {
            font-size: 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Admin</h1>
        <div class="user-info">Selamat datang, {{ Auth::user()->name }}</div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang di InventariSIS</h2>
            <p>Sistem Informasi Inventaris PT PLN (Persero)</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Barang</h3>
                <div class="number">0</div>
            </div>
            <div class="stat-card">
                <h3>Total User</h3>
                <div class="number">2</div>
            </div>
            <div class="stat-card">
                <h3>Barang Masuk</h3>
                <div class="number">0</div>
            </div>
            <div class="stat-card">
                <h3>Barang Keluar</h3>
                <div class="number">0</div>
            </div>
        </div>

        <div class="quick-links">
            <h3>Menu Utama</h3>
            <div class="links-grid">
                <a href="#" class="link-card">
                    <div class="icon">📦</div>
                    <div class="title">Kelola Barang</div>
                </a>
                <a href="#" class="link-card">
                    <div class="icon">📥</div>
                    <div class="title">Barang Masuk</div>
                </a>
                <a href="#" class="link-card">
                    <div class="icon">📤</div>
                    <div class="title">Barang Keluar</div>
                </a>
                <a href="#" class="link-card">
                    <div class="icon">👥</div>
                    <div class="title">Kelola User</div>
                </a>
                <a href="#" class="link-card">
                    <div class="icon">📊</div>
                    <div class="title">Laporan</div>
                </a>
                <a href="#" class="link-card">
                    <div class="icon">⚙️</div>
                    <div class="title">Pengaturan</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
                </ul>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 InventariSIS - Admin Panel</p>
        </footer>
    </div>
</body>
</html>
