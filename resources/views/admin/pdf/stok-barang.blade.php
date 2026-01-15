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
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .img-barang {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .img-placeholder {
            width: 40px;
            height: 40px;
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
        .qr-code {
            width: 40px;
            height: 40px;
            margin: 0 auto;
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
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 10%;">Kode Barang</th>
                <th style="width: 6%; text-align: center;">Gambar</th>
                <th style="width: 24%;">Nama Barang</th>
                <th style="width: 12%; text-align: center;">Kategori</th>
                <th style="width: 6%; text-align: center;">Stok</th>
                <th style="width: 6%; text-align: center;">Rack</th>
                <th style="width: 13%;">Penginput</th>
                <th style="width: 10%; text-align: center;">QR Code</th>
                <th style="width: 9%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-size: 10px; font-weight: bold;">{{ $item->kodebarang }}</td>
                <td style="text-align: center; padding: 4px;">
                    @php
                        $hasGD = extension_loaded('gd');
                    @endphp
                    @if($hasGD && $item->image && file_exists(public_path('images/barang/' . $item->image)))
                        <img src="{{ public_path('images/barang/' . $item->image) }}" alt="{{ $item->namabarang }}" class="img-barang">
                    @elseif($item->image)
                        <div class="img-placeholder">✓</div>
                    @else
                        <div class="img-placeholder">-</div>
                    @endif
                </td>
                <td style="font-size: 10px;">{{ $item->namabarang }}</td>
                <td style="text-align: center;">
                    @if($item->kategori == 'barang_sewa')
                        <span class="badge badge-sewa">Sewa</span>
                    @elseif($item->kategori == 'aset_tetap')
                        <span class="badge" style="background-color: #dbeafe; color: #1e40af;">Aset</span>
                    @else
                        <span class="badge badge-pakai">Habis Pakai</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold; font-size: 11px;">{{ $item->stock }}</td>
                <td style="text-align: center; font-size: 10px;">{{ strtoupper($item->rack) }}</td>
                <td style="font-size: 9px;">{{ $item->penginput }}</td>
                <td style="text-align: center; padding: 4px;">
                    @php
                        try {
                            // Generate QR code as SVG (doesn't need imagick)
                            $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                ->size(40)
                                ->errorCorrection('H')
                                ->generate($item->kodebarang);
                            // Convert SVG to base64
                            $qrCodeBase64 = base64_encode($qrCodeSvg);
                        } catch (\Exception $e) {
                            $qrCodeBase64 = null;
                        }
                    @endphp
                    @if($qrCodeBase64)
                        <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR" style="width: 40px; height: 40px; display: block; margin: 0 auto;">
                    @else
                        <div style="font-size: 7px; text-align: center;">
                            {{ $item->kodebarang }}
                        </div>
                    @endif
                </td>
                <td style="text-align: center;">
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
