<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #14a2ba; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0 0 0; font-size: 16px; color: #14a2ba; font-weight: normal; }
        .info { margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #14a2ba; color: white; padding: 10px 8px; text-align: left; font-size: 11px; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLN Indonesia Power</h1>
        <h2>Laporan Barang Keluar</h2>
    </div>
    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
        <strong>Total Transaksi:</strong> {{ $transactions->count() }}<br>
        <strong>Total Quantity:</strong> {{ $transactions->sum('qty') }} item
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Kode Barang</th>
                <th style="width: 22%;">Nama Barang</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 18%;">Penerima</th>
                <th style="width: 20%;">Penginput</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $item->stock->kodebarang ?? '-' }}</td>
                <td>{{ $item->stock->namabarang ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item->qty }}</td>
                <td>{{ $item->penerima ?? '-' }}</td>
                <td>{{ $item->penginput }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} | PLN Indonesia Power &copy; {{ date('Y') }}
    </div>
</body>
</html>
