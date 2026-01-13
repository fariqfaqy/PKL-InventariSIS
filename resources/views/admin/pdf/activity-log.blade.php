<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Activity Log</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #14a2ba; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #333; }
        .header h2 { margin: 5px 0 0 0; font-size: 16px; color: #14a2ba; font-weight: normal; }
        .info { margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #14a2ba; color: white; padding: 8px 6px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-get { background-color: #dbeafe; color: #1e40af; }
        .badge-post { background-color: #d1fae5; color: #065f46; }
        .badge-put { background-color: #fef3c7; color: #92400e; }
        .badge-delete { background-color: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLN Indonesia Power</h1>
        <h2>Laporan Activity Log</h2>
    </div>
    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
        <strong>Total Aktivitas:</strong> {{ $logs->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">User</th>
                <th style="width: 10%;">Method</th>
                <th style="width: 30%;">Endpoint</th>
                <th style="width: 12%;">IP Address</th>
                <th style="width: 13%;">User Agent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($log->date)->format('d/m/Y H:i') }}</td>
                <td>{{ $log->usr }}</td>
                <td>
                    @php
                        $methodClass = 'badge-get';
                        if($log->method == 'POST') $methodClass = 'badge-post';
                        elseif($log->method == 'PUT') $methodClass = 'badge-put';
                        elseif($log->method == 'DELETE') $methodClass = 'badge-delete';
                    @endphp
                    <span class="badge {{ $methodClass }}">{{ $log->method }}</span>
                </td>
                <td style="font-size: 9px;">{{ $log->endpoint }}</td>
                <td>{{ $log->ipaddr }}</td>
                <td style="font-size: 8px;">{{ Str::limit($log->useragent, 25) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Dicetak oleh: {{ Auth::user()->name }} | PLN Indonesia Power &copy; {{ date('Y') }}
    </div>
</body>
</html>
