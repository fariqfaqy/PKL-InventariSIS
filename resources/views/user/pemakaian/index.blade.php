<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemakaian - InventariSIS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --bg-primary: #f4f6f9; --bg-secondary: #ffffff; --text-primary: #333333; --text-secondary: #666666; --border-color: #e0e0e0; }
        body.dark-mode { --bg-primary: #0a0e27; --bg-secondary: #151937; --text-primary: #e8eaf6; --text-secondary: #b0b5d1; --border-color: #2d3354; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-primary); transition: all 0.3s; }
        body.dark-mode { background: linear-gradient(135deg, #0a0e27 0%, #1a1042 50%, #0f1535 100%); }
        .header { background: linear-gradient(135deg, #1E88E5, #1565C0); color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .header h1 { font-size: 2rem; font-weight: 800; }
        .dark-mode-toggle { background: rgba(255,255,255,0.2); border: none; width: 50px; height: 28px; border-radius: 14px; cursor: pointer; position: relative; }
        .dark-mode-toggle::before { content: '☀️'; position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; background: white; border-radius: 50%; transition: all 0.3s; font-size: 12px; display: flex; align-items: center; justify-content: center; }
        body.dark-mode .dark-mode-toggle::before { content: '🌙'; transform: translateX(22px); background: #424242; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; border: 2px solid white; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
        .logout-btn:hover { background: white; color: #1E88E5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 40px; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #1E88E5; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: var(--text-primary); font-weight: 700; }
        .btn-primary { background: linear-gradient(135deg, #1E88E5, #667eea); color: white; padding: 12px 28px; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(30,136,229,0.3); text-decoration: none; display: inline-block; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(30,136,229,0.4); }
        .alert { padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 0.95rem; }
        .alert-success { background: #D4EDDA; color: #155724; border-left: 4px solid #28A745; }
        .alert-error { background: #F8D7DA; color: #721C24; border-left: 4px solid #DC3545; }
        body.dark-mode .alert-success { background: #1f3d1f; color: #6fd96f; }
        body.dark-mode .alert-error { background: #3d1f1f; color: #ff6b6b; }
        .table-container { background: var(--bg-secondary); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1E88E5; color: white; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
        tr:hover { background: rgba(30,136,229,0.05); }
        .btn-edit { background: #FFC107; color: #333; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .btn-delete { background: #DC3545; color: white; padding: 6px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-secondary); }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; opacity: 0.5; }
    </style>
</head>
<body>
    <div class="header">
        <div><h1>📦 InventariSIS</h1><div style="opacity:0.9">{{ Auth::user()->name }} (User)</div></div>
        <div style="display:flex;gap:15px;align-items:center">
            <button class="dark-mode-toggle" onclick="toggleDarkMode()"></button>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf<button type="submit" class="logout-btn">Logout</button></form>
        </div>
    </div>
    <div class="container">
        <div class="breadcrumb"><a href="{{ route('user.dashboard') }}">Dashboard</a> / Riwayat Pemakaian</div>
        
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="page-header">
            <h2>📋 Riwayat Pemakaian Barang</h2>
            <a href="{{ route('user.pemakaian.create') }}" class="btn-primary">➕ Catat Pemakaian Baru</a>
        </div>

        @if($pemakaian->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Penerima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemakaian as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $item->kodebarang_k }}</strong></td>
                        <td>{{ $item->namabarang_k }}</td>
                        <td><strong>{{ $item->qty }} unit</strong></td>
                        <td>{{ $item->penerima }}</td>
                        <td>
                            <a href="{{ route('user.pemakaian.edit', $item->idkeluar) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('user.pemakaian.destroy', $item->idkeluar) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus pemakaian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:20px">{{ $pemakaian->links() }}</div>
        @else
        <div class="empty-state">
            <div class="icon">📦</div>
            <h3>Belum Ada Pemakaian</h3>
            <p>Klik tombol "Catat Pemakaian Baru" untuk mulai mencatat pemakaian barang</p>
        </div>
        @endif
    </div>
    <script>
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) document.body.classList.add('dark-mode');
        function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); }
    </script>
</body>
</html>
