<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #14a2ba;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #14a2ba;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #14a2ba;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-sewa {
            background-color: #e0d4fc;
            color: #7c3aed;
        }
        .badge-pakai {
            background-color: #fce7f3;
            color: #db2777;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-yellow {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLN Indonesia Power</h1>
        <h2>Laporan Stok Barang</h2>
    </div>

    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
        @if(isset($kategori))
            <strong>Kategori:</strong> {{ $kategori == 'barang_sewa' ? 'Barang Sewa' : 'Barang Habis Pakai' }}<br>
        @endif
        <strong>Total Item:</strong> {{ $stocks->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Barang</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 10%;">Rack</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kodebarang }}</td>
                <td>{{ $item->namabarang }}</td>
                <td>
                    @if($item->kategori == 'barang_sewa')
                        <span class="badge badge-sewa">Sewa</span>
                    @else
                        <span class="badge badge-pakai">Habis Pakai</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $item->stock }}</td>
                <td style="text-align: center;">{{ strtoupper($item->rack) }}</td>
                <td>
                    @if($item->stock > 10)
                        <span class="badge badge-green">Normal</span>
                    @elseif($item->stock > 0)
                        <span class="badge badge-yellow">Rendah</span>
                    @else
                        <span class="badge badge-red">Habis</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} | PLN Indonesia Power &copy; {{ date('Y') }}
    </div>
</body>
</html>
