<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Divisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #14a2ba;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #14a2ba;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: #16a34a;
            font-weight: bold;
        }
        .status-inactive {
            color: #9ca3af;
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
        <h1>LAPORAN DATA DIVISI</h1>
        <p>PT PLN (Persero) - Sistem Inventaris</p>
        <p>Tanggal Cetak: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="25%">Nama Divisi</th>
                <th width="25%">Kepala Divisi</th>
                <th width="15%">Jumlah Pegawai</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($divisions as $index => $division)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $division->kode_divisi }}</strong></td>
                <td>{{ $division->nama_divisi }}</td>
                <td>{{ $division->kepala_divisi ?? '-' }}</td>
                <td style="text-align: center;">{{ $division->users_count }} orang</td>
                <td class="{{ $division->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $division->is_active ? 'Aktif' : 'Nonaktif' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Divisi: <strong>{{ $divisions->count() }}</strong></p>
        <p>Divisi Aktif: <strong>{{ $divisions->where('is_active', true)->count() }}</strong></p>
    </div>
</body>
</html>
