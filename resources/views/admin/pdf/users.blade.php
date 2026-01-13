<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data User</title>
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
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-admin { background-color: #dbeafe; color: #1e40af; }
        .badge-user { background-color: #e0f2fe; color: #0369a1; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLN Indonesia Power</h1>
        <h2>Laporan Data User</h2>
    </div>
    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
        <strong>Total User:</strong> {{ $users->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 25%;">Nama</th>
                <th style="width: 30%;">Email</th>
                <th style="width: 15%;">Role</th>
                <th style="width: 22%;">Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role == 'admin')
                        <span class="badge badge-admin">Administrator</span>
                    @else
                        <span class="badge badge-user">User</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} | PLN Indonesia Power &copy; {{ date('Y') }}
    </div>
</body>
</html>
