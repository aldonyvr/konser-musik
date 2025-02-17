<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Konser</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .stats { margin-bottom: 30px; }
        .stats-item { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan Konser</h1>
        <h2>{{ $report['konser_info']['title'] }}</h2>
        <p>Tanggal: {{ $report['konser_info']['tanggal'] }}</p>
        <p>Lokasi: {{ $report['konser_info']['lokasi'] }}</p>
    </div>

    <div class="stats">
        <div class="stats-item">
            <strong>Total Tiket Terjual:</strong> 
            {{ $report['ticket_stats']['total_tickets'] }}
        </div>
        <div class="stats-item">
            <strong>Total Pendapatan:</strong> 
            Rp {{ number_format($report['ticket_stats']['total_revenue']) }}
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Tipe Tiket</th>
                <th>Jumlah Terjual</th>
                <th>Total Pendapatan</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['ticket_stats']['by_type'] as $type => $count)
            <tr>
                <td>{{ $type }}</td>
                <td class="text-center">{{ $count }}</td>
                <td class="text-right">
                    Rp {{ number_format($report['ticket_stats']['revenue_by_type'][$type]) }}
                </td>
                <td class="text-right">
                    {{ round(($count / $report['ticket_stats']['total_tickets']) * 100, 1) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
