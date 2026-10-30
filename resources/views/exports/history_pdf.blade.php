<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Analisis — {{ $dataset->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        h1 { font-size: 18px; color: #1e3a5f; margin-bottom: 4px; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 10px; font-size: 11px; color: #6b7280; text-transform: uppercase; border: 1px solid #e5e7eb; }
        td { padding: 6px 10px; border: 1px solid #e5e7eb; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>

<h1>StatWeb — Riwayat Analisis</h1>
<div class="meta">
    Dataset: <strong>{{ $dataset->name }}</strong> &nbsp;|&nbsp;
    Total analisis: <strong>{{ $histories->count() }}</strong> &nbsp;|&nbsp;
    Digenerate: {{ now()->format('d M Y, H:i') }}
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Analisis</th>
        <th>Tipe</th>
        <th>Dijalankan</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($histories as $i => $history)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $history->analysis_label }}</td>
            <td>{{ str_replace('_', ' ', ucfirst($history->analysis_type)) }}</td>
            <td>{{ $history->created_at->format('d M Y, H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Digenerate oleh StatWeb &mdash; {{ now()->format('d M Y, H:i:s') }}
</div>

</body>
</html>
