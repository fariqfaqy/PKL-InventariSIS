<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - InventariSIS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pln-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg-primary: #f4f6f9; --bg-secondary: #ffffff; --text-primary: #333333;
            --text-secondary: #666666; --border-color: #e0e0e0; --brand-color: #1E88E5;
        }
        body.dark-mode {
            --bg-primary: #0f0f0f; --bg-secondary: #1a1a1a; --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0; --border-color: #333333; --brand-color: #a78bfa;
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-primary); transition: all 0.3s; margin: 0; }
        body.dark-mode { background: linear-gradient(135deg, #0a0e27 0%, #1a1042 50%, #0f1535 100%); }
        
        /* Layout */
        .layout { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 280px; background: var(--bg-secondary); box-shadow: 2px 0 20px rgba(0,0,0,0.08); 
            display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; transition: all 0.3s; }
        body.dark-mode .sidebar { box-shadow: 2px 0 30px rgba(138, 97, 230, 0.15); 
            border-right: 1px solid rgba(138, 97, 230, 0.1); }
        .sidebar-header { background: linear-gradient(135deg, #1E88E5, #667eea); color: white; 
            padding: 25px 20px; display: flex; align-items: center; gap: 12px; }
        body.dark-mode .sidebar-header { background: linear-gradient(135deg, #8a61e6, #5e35b1); }
        .sidebar-header .logo { font-size: 2rem; }
        .sidebar-header .title { font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: 800; 
            background: linear-gradient(135deg, #FDD835, #FFEB3B); -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 0.5px; }
        body.dark-mode .sidebar-header .title { background: linear-gradient(135deg, #c4b5fd, #a78bfa); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .sidebar-menu { flex: 1; padding: 20px 0; overflow-y: auto; }
        .menu-item { display: flex; align-items: center; gap: 15px; padding: 16px 25px; 
            color: var(--text-primary); text-decoration: none; transition: all 0.3s; cursor: pointer; 
            border-left: 4px solid transparent; }
        .menu-item:hover { background: rgba(30, 136, 229, 0.08); border-left-color: #1E88E5; }
        body.dark-mode .menu-item:hover { background: rgba(138, 97, 230, 0.12); border-left-color: #8a61e6; }
        .menu-item.active { background: rgba(30, 136, 229, 0.12); border-left-color: #1E88E5; font-weight: 700; }
        body.dark-mode .menu-item.active { background: rgba(138, 97, 230, 0.18); border-left-color: #8a61e6; }
        .menu-item .icon { font-size: 1.4rem; width: 24px; text-align: center; }
        .menu-item .text { font-size: 1rem; }
        .menu-section { padding: 15px 25px 10px; color: var(--text-secondary); font-size: 0.75rem; 
            font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Main Content */
        .main-content { flex: 1; margin-left: 280px; }
        
        .header { background: linear-gradient(135deg, #1E88E5, #1565C0); color: white; padding: 20px 40px;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .header h1 { font-size: 2rem; font-weight: 800; }
        .header-right { display: flex; gap: 15px; align-items: center; }
        .dark-mode-toggle { background: rgba(255,255,255,0.2); border: none; width: 50px; height: 28px;
            border-radius: 14px; cursor: pointer; position: relative; }
        .dark-mode-toggle::before { content: '☀️'; position: absolute; top: 3px; left: 3px; width: 22px;
            height: 22px; background: white; border-radius: 50%; transition: all 0.3s; font-size: 12px;
            display: flex; align-items: center; justify-content: center; }
        body.dark-mode .dark-mode-toggle::before { content: '🌙'; transform: translateX(22px); background: #424242; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; border: 2px solid white;
            padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .logout-btn:hover { background: white; color: #1E88E5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 40px; }
        .welcome-banner { background: linear-gradient(135deg, #667eea, #764ba2); color: white;
            padding: 40px; border-radius: 16px; margin-bottom: 30px; position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
            background: rgba(255,255,255,0.1); border-radius: 50%; }
        .welcome-banner h2 { font-size: 2.5rem; margin-bottom: 10px; position: relative; z-index: 1; }
        .welcome-banner p { font-size: 1.1rem; opacity: 0.95; position: relative; z-index: 1; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px; }
        .stat-card { background: var(--bg-secondary); padding: 30px; border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #1E88E5; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 12px;
            text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .stat-card .number { font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, #1E88E5, #667eea);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-card .sub-info { font-size: 0.9rem; color: var(--text-secondary); margin-top: 8px; }
        .section-title { font-size: 1.5rem; color: var(--text-primary); margin: 40px 0 20px;
            padding-bottom: 12px; border-bottom: 3px solid #1E88E5; font-weight: 700; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .status-card { background: var(--bg-secondary); padding: 24px; border-radius: 12px; text-align: center; 
            transition: transform 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .status-card:hover { transform: translateY(-3px); }
        .status-card.aman { border-left: 4px solid #4CAF50; }
        .status-card.menengah { border-left: 4px solid #FFC107; }
        .status-card.kritis { border-left: 4px solid #F44336; }
        .status-card .icon { font-size: 3rem; margin-bottom: 10px; }
        .status-card h4 { font-size: 1rem; color: var(--text-secondary); margin-bottom: 8px; }
        .status-card .count { font-size: 2.5rem; font-weight: 800; color: var(--text-primary); }
        .rack-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 40px; }
        .rack-card { background: var(--bg-secondary); padding: 20px; border-radius: 12px; text-align: center;
            cursor: pointer; transition: all 0.3s; text-decoration: none; color: var(--text-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 2px solid transparent; }
        .rack-card:hover { transform: scale(1.05); border-color: #1E88E5; }
        .rack-card .rack-icon { font-size: 2rem; margin-bottom: 10px; }
        .rack-card h4 { font-size: 1.2rem; color: #1E88E5; margin-bottom: 5px; font-weight: 700; }
        .rack-card .count { font-size: 1.8rem; font-weight: 700; color: var(--text-primary); }
        .rack-card small { font-size: 0.85rem; color: var(--text-secondary); }
        .quick-action { background: var(--bg-secondary); padding: 30px; border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 30px; }
        .btn-primary { background: linear-gradient(135deg, #1E88E5, #667eea); color: white; padding: 14px 32px;
            border: none; border-radius: 10px; font-size: 1.1rem; font-weight: 700; cursor: pointer;
            transition: all 0.3s; box-shadow: 0 4px 15px rgba(30,136,229,0.3); text-decoration: none;
            display: inline-block; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(30,136,229,0.4); }
        .recent-table { width: 100%; background: var(--bg-secondary); border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .recent-table table { width: 100%; border-collapse: collapse; }
        .recent-table th { background: #1E88E5; color: white; padding: 15px; text-align: left; font-weight: 600; }
        .recent-table td { padding: 12px 15px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
        .recent-table tr:hover { background: rgba(30,136,229,0.05); }
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-header .title { display: none; }
            .menu-item .text { display: none; }
            .menu-section { display: none; }
            .main-content { margin-left: 70px; }
            .container { padding: 20px; }
            .header { flex-direction: column; gap: 15px; }
            .welcome-banner h2 { font-size: 1.8rem; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="title">InventariSIS</div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="{{ route('user.dashboard') }}" class="menu-item active">
                    <div class="icon">🏠</div>
                    <div class="text">Dashboard</div>
                </a>
                
                <div class="menu-section">Master Data</div>
                
                <a href="#" class="menu-item" onclick="alert('Fitur Kategori akan segera hadir!'); return false;">
                    <div class="icon">📑</div>
                    <div class="text">Kategori</div>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
    <div class="header">
        <div>
            <h1>�� InventariSIS</h1>
            <div style="opacity:0.9">{{ Auth::user()->name }} (User Divisi)</div>
        </div>
        <div class="header-right">
            <button class="dark-mode-toggle" onclick="toggleDarkMode()"></button>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="welcome-banner">
            <h2>Halo, {{ Auth::user()->name }}! 👋</h2>
            <p>Selamat datang di Dashboard Inventaris. Lihat semua data barang dan catat pemakaian Anda.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Barang</h3>
                <div class="number">{{ $totalBarang }}</div>
                <div class="sub-info">Total Stok: {{ $totalStok ?? 0 }} unit</div>
            </div>
            <div class="stat-card">
                <h3>Barang Masuk</h3>
                <div class="number">{{ $totalBarangMasuk }}</div>
                <div class="sub-info">Hari ini: {{ $barangMasukHariIni ?? 0 }} unit</div>
            </div>
            <div class="stat-card">
                <h3>Barang Keluar</h3>
                <div class="number">{{ $totalBarangKeluar }}</div>
                <div class="sub-info">Hari ini: {{ $barangKeluarHariIni ?? 0 }} unit</div>
            </div>
        </div>

        <h2 class="section-title">Status Stok Barang</h2>
        <div class="status-grid">
            <div class="status-card aman">
                <div class="icon">●</div>
                <h4>Stok Aman</h4>
                <div class="count">{{ $stokAman }}</div>
                <small style="color:#4CAF50; font-weight: 600;">≥ 10 unit</small>
            </div>
            <div class="status-card menengah">
                <div class="icon">●</div>
                <h4>Stok Menengah</h4>
                <div class="count">{{ $stokMenengah }}</div>
                <small style="color:#FFC107; font-weight: 600;">5-9 unit</small>
            </div>
            <div class="status-card kritis">
                <div class="icon">●</div>
                <h4>Stok Kritis</h4>
                <div class="count">{{ $stokKritis }}</div>
                <small style="color:#F44336; font-weight: 600;">≤ 4 unit</small>
            </div>
        </div>

        <h2 class="section-title">Lokasi Rak Penyimpanan</h2>
        <div class="rack-grid">
            @foreach($raks as $rak)
            <a href="#" class="rack-card">
                <div class="rack-icon">■</div>
                <h4>Rak {{ strtoupper($rak) }}</h4>
                <div class="count">{{ $barangPerRak[$rak]->total ?? 0 }}</div>
                <small>Total: {{ $barangPerRak[$rak]->total_stock ?? 0 }} unit</small>
            </a>
            @endforeach
        </div>

        <h2 class="section-title">Catat Pemakaian Barang</h2>
        <div class="quick-action">
            <p style="color: var(--text-secondary); margin-bottom: 20px;">
                Klik tombol di bawah untuk mencatat pemakaian barang yang Anda gunakan.
                Sistem akan otomatis mengurangi stok barang.
            </p>
            <a href="{{ route('user.pemakaian.create') }}" class="btn-primary">➕ Catat Pemakaian Barang</a>
        </div>

        @if(isset($recentTransactions) && $recentTransactions->count() > 0)
        <h2 class="section-title">📋 Riwayat Pemakaian Anda</h2>
        <div class="recent-table">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trans)
                    <tr>
                        <td>{{ $trans->tanggal->format('d/m/Y H:i') }}</td>
                        <td>{{ $trans->namabarang_k }}</td>
                        <td><strong>{{ $trans->qty }} unit</strong></td>
                        <td>{{ $trans->penerima }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
        </div>
    </div>

    <script>
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) document.body.classList.add('dark-mode');
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        }
    </script>
</body>
</html>
