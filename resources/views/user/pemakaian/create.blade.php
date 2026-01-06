<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pemakaian Barang - InventariSIS</title>
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
        .container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #1E88E5; text-decoration: none; }
        .form-card { background: var(--bg-secondary); padding: 40px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        body.dark-mode .form-card { box-shadow: 0 4px 20px rgba(138, 97, 230, 0.15), 0 0 15px rgba(66, 133, 244, 0.08); border: 1px solid rgba(138, 97, 230, 0.15); }
        .form-card h2 { font-size: 1.8rem; color: var(--text-primary); margin-bottom: 30px; font-weight: 700; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; color: var(--text-primary); font-weight: 600; font-size: 0.95rem; }
        .form-group select, .form-group input { width: 100%; padding: 14px 16px; border: 2px solid var(--border-color); border-radius: 10px; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary); transition: all 0.3s; }
        .form-group select:focus, .form-group input:focus { outline: none; border-color: #1E88E5; box-shadow: 0 0 0 4px rgba(30,136,229,0.1); }
        .info-box { background: #E3F2FD; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #1E88E5; }
        body.dark-mode .info-box { background: rgba(138, 97, 230, 0.12); border-left-color: #8a61e6; color: var(--text-primary); }
        .info-box .stock-info { font-size: 1.1rem; font-weight: 600; color: #1E88E5; margin-top: 5px; }
        .btn-group { display: flex; gap: 15px; margin-top: 30px; }
        .btn-primary { background: linear-gradient(135deg, #1E88E5, #667eea); color: white; padding: 14px 32px; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; flex: 1; }
        .btn-secondary { background: var(--border-color); color: var(--text-primary); padding: 14px 32px; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .alert-error { background: #F8D7DA; color: #721C24; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #DC3545; }
    </style>
</head>
<body>
    <div class="header">
        <div><h1>📦 InventariSIS</h1></div>
        <button class="dark-mode-toggle" onclick="toggleDarkMode()"></button>
    </div>
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('user.dashboard') }}">Dashboard</a> / 
            <a href="{{ route('user.pemakaian.index') }}">Riwayat Pemakaian</a> / 
            Catat Baru
        </div>

        @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
        @endif

        <div class="form-card">
            <h2>📤 Catat Pemakaian Barang</h2>
            
            <form action="{{ route('user.pemakaian.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="idbarang">Pilih Barang *</label>
                    <select name="idbarang" id="idbarang" required onchange="updateStockInfo()">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $barang)
                        <option value="{{ $barang->idbarang }}" 
                                data-stock="{{ $barang->stock }}"
                                data-nama="{{ $barang->namabarang }}"
                                data-kode="{{ $barang->kodebarang }}">
                            {{ $barang->kodebarang }} - {{ $barang->namabarang }} (Stok: {{ $barang->stock }})
                        </option>
                        @endforeach
                    </select>
                    @error('idbarang')
                    <small style="color:#DC3545">{{ $message }}</small>
                    @enderror
                </div>

                <div id="stockInfo" class="info-box" style="display:none">
                    <div>Stok Tersedia:</div>
                    <div class="stock-info" id="stockValue">0 unit</div>
                </div>

                <div class="form-group">
                    <label for="qty">Jumlah yang Digunakan *</label>
                    <input type="number" name="qty" id="qty" min="1" required 
                           placeholder="Masukkan jumlah barang" value="{{ old('qty') }}">
                    @error('qty')
                    <small style="color:#DC3545">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="penerima">Penerima / Keterangan *</label>
                    <input type="text" name="penerima" id="penerima" required 
                           placeholder="Nama penerima atau keterangan penggunaan" 
                           value="{{ old('penerima') }}">
                    @error('penerima')
                    <small style="color:#DC3545">{{ $message }}</small>
                    @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn-primary">✓ Simpan Pemakaian</button>
                    <a href="{{ route('user.pemakaian.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const isDark = localStorage.getItem('darkMode') === 'true';
        if (isDark) document.body.classList.add('dark-mode');
        function toggleDarkMode() { 
            document.body.classList.toggle('dark-mode'); 
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); 
        }

        function updateStockInfo() {
            const select = document.getElementById('idbarang');
            const option = select.options[select.selectedIndex];
            const stockInfo = document.getElementById('stockInfo');
            const stockValue = document.getElementById('stockValue');
            
            if (option.value) {
                const stock = option.dataset.stock;
                stockValue.textContent = stock + ' unit';
                stockInfo.style.display = 'block';
                
                // Update max value for qty input
                document.getElementById('qty').max = stock;
            } else {
                stockInfo.style.display = 'none';
            }
        }
    </script>
</body>
</html>
