<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Pegawai</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #14a2ba; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0 0 0; font-size: 16px; color: #14a2ba; font-weight: normal; }
        .info { margin-bottom: 20px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #14a2ba; color: white; padding: 8px 6px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; white-space: nowrap; }
        .badge-admin { background-color: #e9d5ff; color: #6b21a8; }
        .badge-user { background-color: #dbeafe; color: #1e40af; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLN Indonesia Power</h1>
        <h2>Laporan Data Pegawai</h2>
    </div>
    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
        <strong>Total Pegawai:</strong> {{ $users->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">NIP</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 18%;">Divisi</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 8%;">Role</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-size: 9px;">{{ $user->nip ?? '-' }}</td>
                <td style="font-weight: bold;">{{ $user->name }}</td>
                <td>{{ $user->division ? $user->division->nama_divisi : '-' }}</td>
                <td>{{ $user->jabatan ?? '-' }}</td>
                <td style="font-size: 9px;">{{ $user->email }}</td>
                <td style="text-align: center;">
                    @if($user->role == 'admin')
                        <span class="badge badge-admin">Admin</span>
                    @else
                        <span class="badge badge-user">User</span>
                    @endif
                </td>
                <td style="text-align: center; color: #16a34a; font-weight: bold;">Aktif</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p><strong>Total Admin:</strong> {{ $users->where('role', 'admin')->count() }} | <strong>Total User:</strong> {{ $users->where('role', 'user')->count() }}</p>
        <p>Dicetak oleh: {{ Auth::user()->name }} | PLN Indonesia Power &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
