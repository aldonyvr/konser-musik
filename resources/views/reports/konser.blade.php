<!DOCTYPE html>
<html>
<head>
    <title>Laporan Konser</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Konser</h1>
        <h2>{{ $report['konser_info']['title'] ?? 'N/A' }}</h2>
    </div>

    <div class="section">
        <h3>Informasi Konser</h3>
        <table class="table">
            <tr>
                <th>Tanggal</th>
                <td>{{ $report['konser_info']['tanggal'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <td>{{ $report['konser_info']['lokasi'] ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Statistik Penjualan Tiket</h3>
        <table class="table">
            <tr>
                <th>Kategori</th>
                <th>Jumlah Tiket</th>
                <th>Pendapatan</th>
            </tr>
            @foreach($report['ticket_stats']['by_type'] as $type => $count)
            <tr>
                <td>{{ $type }}</td>
                <td>{{ $count }}</td>
                <td>Rp {{ number_format($report['ticket_stats']['revenue_by_type'][$type], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td>Total</td>
                <td>{{ $report['ticket_stats']['total_tickets'] }}</td>
                <td>Rp {{ number_format($report['ticket_stats']['total_revenue'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
