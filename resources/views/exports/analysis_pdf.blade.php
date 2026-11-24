<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $analysisResult->analysis_label }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        h1 { font-size: 18px; color: #162032; margin-bottom: 4px; }
        h2 { font-size: 14px; color: #374151; margin-top: 20px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 10px; font-size: 11px; color: #6b7280; text-transform: uppercase; border: 1px solid #e5e7eb; }
        td { padding: 6px 10px; border: 1px solid #e5e7eb; vertical-align: top; }
        td:first-child { font-weight: bold; color: #374151; width: 40%; }
        .badge-yes { background: #fee2e2; color: #b91c1c; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-no { background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .note { font-size: 10px; color: #9ca3af; font-style: italic; margin-bottom: 10px; }
        .caption { font-size: 10px; color: #4b5563; background: #f9fafb; border: 1px solid #e5e7eb; padding: 6px 8px; margin: 8px 0 14px 0; line-height: 1.4; }
        .chart-box { position: relative; border: 1px solid #e5e7eb; background: #fafafa; margin-bottom: 8px; }
        .dot { position: absolute; width: 6px; height: 6px; border-radius: 50%; background: #162032; opacity: 0.75; }
        .dash-line { position: absolute; height: 0; border-top: 1.5px dashed #DC2626; transform-origin: 0 0; }
    </style>
</head>
<body>

<h1>StatWeb — Hasil Analisis Statistik</h1>
<div class="meta">
    Dataset: <strong>{{ $dataset->name }}</strong> &nbsp;|&nbsp;
    Analisis: <strong>{{ $analysisResult->analysis_label }}</strong> &nbsp;|&nbsp;
    Tanggal: {{ $analysisResult->created_at->format('d M Y, H:i') }}
</div>

<h2>Parameter</h2>
<table>
    <tbody>
    @foreach ($analysisResult->parameters as $key => $val)
        <tr>
            <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
            <td>{{ is_array($val) ? implode(', ', $val) : $val }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Hasil</h2>
@php $result = $analysisResult->result; @endphp

{{-- ============ DESCRIPTIVE ============ --}}
@if ($analysisResult->analysis_type === 'descriptive')
    @php $stats = $result['stats']; @endphp
    <table>
        <tbody>
        @foreach ([
            'n' => 'N (Jumlah Data)', 'mean' => 'Mean', 'median' => 'Median',
            'mode' => 'Modus', 'std_dev' => 'Standar Deviasi',
            'variance' => 'Variansi', 'min' => 'Minimum',
            'max' => 'Maksimum', 'range' => 'Range',
            'q1' => 'Q1 (Kuartil 1)', 'q3' => 'Q3 (Kuartil 3)'
        ] as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Histogram: pakai PNG dari Chart.js kalau ada, kalau tidak pakai grafik cadangan (PdfChart) --}}
    @php
        $hist = $result['histogram'] ?? null;
        $colName = $analysisResult->parameters['column'] ?? 'Nilai';
    @endphp
    @if (!empty($chartImage) || $hist)
        <h2>Histogram</h2>
        @if (!empty($chartImage))
            <img src="{{ $chartImage }}" style="width: 100%; max-width: 500px; height: auto;">
        @else
            {!! \App\Support\PdfChart::bars($hist['labels'], $hist['values'], [
                'color'   => '#162032',
                'gap'     => 0.04,
                'x_title' => $colName . ' (rentang nilai per kelas)',
                'y_title' => 'Frekuensi (jumlah data)',
            ]) !!}
        @endif
        <p class="caption">
            <strong>Cara membaca:</strong> Sumbu X (mendatar) = rentang nilai <strong>{{ $colName }}</strong>
            yang dibagi menjadi {{ count($hist['labels'] ?? []) }} kelas. Sumbu Y (tegak) = frekuensi, yaitu jumlah data
            pada tiap kelas. Angka di atas batang adalah jumlah data kelas tersebut. Batang yang lebih tinggi berarti
            lebih banyak data berada di rentang itu. Total N = {{ $stats['n'] ?? '-' }} data.
        </p>
    @endif

    {{-- ============ CATEGORICAL ============ --}}
@elseif ($analysisResult->analysis_type === 'categorical')
    <table>
        <thead>
        <tr>
            <th>Kategori</th>
            <th>Frekuensi</th>
            <th>Persentase</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($result['frequencies'] as $cat => $freq)
            <tr>
                <td style="font-weight:normal">{{ $cat }}</td>
                <td>{{ $freq }}</td>
                <td>{{ number_format(($freq / $result['total']) * 100, 1) }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @php
        $cats = $result['frequencies'] ?? [];
        $catName = $analysisResult->parameters['column'] ?? 'Kategori';
    @endphp
    <h2>Bar Chart</h2>
    @if (!empty($chartImage))
        <img src="{{ $chartImage }}" style="width: 100%; max-width: 500px; height: auto;">
    @else
        {!! \App\Support\PdfChart::bars(array_map('strval', array_keys($cats)), array_values($cats), [
            'color'   => '#F6C90E',
            'gap'     => 0.4,
            'x_title' => $catName,
            'y_title' => 'Frekuensi (jumlah data)',
        ]) !!}
    @endif
    <p class="caption">
        <strong>Cara membaca:</strong> Sumbu X (mendatar) = kategori <strong>{{ $catName }}</strong>.
        Sumbu Y (tegak) = frekuensi, yaitu jumlah data pada tiap kategori. Angka di atas batang adalah jumlah datanya.
        Batang yang lebih tinggi berarti kategori itu lebih banyak muncul. Total N = {{ $result['total'] ?? array_sum($cats) }} data.
    </p>

    {{-- ============ LAINNYA ============ --}}
@else
    {{-- Scatter Plot (scatter, correlation, regression_simple) --}}
    @if (in_array($analysisResult->analysis_type, ['scatter', 'correlation', 'regression_simple']))
        @php
            $pts = $result['points'] ?? null;
            $xName = $analysisResult->parameters['column_x'] ?? 'X';
            $yName = $analysisResult->parameters['column_y'] ?? 'Y';
            $scatterTitle = match ($analysisResult->analysis_type) {
                'regression_simple' => 'Diagram Pencar dan Garis Regresi',
                'correlation'       => 'Diagram Pencar (Scatter Plot)',
                default             => 'Scatter Plot',
            };
        @endphp

        @if (!empty($chartImage) || ($pts && count($pts) > 0))
            <h2>{{ $scatterTitle }}</h2>
            @if (!empty($chartImage))
                <img src="{{ $chartImage }}" style="width: 100%; max-width: 500px; height: auto;">
            @else
                {!! \App\Support\PdfChart::scatter($pts, ['x_title' => $xName, 'y_title' => $yName]) !!}
            @endif
            <p class="caption">
                <strong>Cara membaca:</strong> Sumbu X (mendatar) = <strong>{{ $xName }}</strong>, sumbu Y (tegak) =
                <strong>{{ $yName }}</strong>. Setiap titik mewakili satu pasangan data.
                @if ($analysisResult->analysis_type === 'regression_simple')
                    Garis menunjukkan hasil regresi (prediksi {{ $yName }} dari {{ $xName }}); makin dekat titik ke garis,
                    makin baik model menjelaskan data.
                @else
                    Titik yang naik dari kiri ke kanan menandakan hubungan positif, yang turun menandakan hubungan negatif;
                    makin rapat titik membentuk pola garis, makin kuat hubungannya.
                @endif
            </p>
        @endif
    @endif

    {{-- Q-Q Plot (normality) --}}
    @if ($analysisResult->analysis_type === 'normality')
        @if (!empty($chartImage))
            <h2>Q-Q Plot</h2>
            <img src="{{ $chartImage }}" style="width: 100%; max-width: 500px; height: auto;">
            <p class="caption">
                <strong>Cara membaca:</strong> Sumbu X (mendatar) = nilai yang diharapkan jika data berdistribusi normal
                (kuantil teoretis), sumbu Y (tegak) = nilai data yang sebenarnya. Titik yang mengikuti garis diagonal
                berarti data mendekati distribusi normal; titik yang menyimpang jauh dari garis berarti data kurang normal.
            </p>
        @elseif (isset($result['qqPoints']) && count($result['qqPoints']) > 0)
            <h2>Q-Q Plot</h2>
            @php
                $qq = $result['qqPoints'];
                $boxW = 460; $boxH = 240; $pad = 20;
                $xs = array_column($qq, 'x'); $ys = array_column($qq, 'y');
                $allVals = array_merge($xs, $ys);
                $vMin = min($allVals); $vMax = max($allVals); $vRange = ($vMax - $vMin) ?: 1;
                $sx = fn($x) => round($pad + (($x - $vMin) / $vRange) * ($boxW - 2 * $pad), 2);
                $sy = fn($y) => round($boxH - $pad - (($y - $vMin) / $vRange) * ($boxH - 2 * $pad), 2);
                $lx1 = $sx($vMin); $ly1 = $sy($vMin);
                $lx2 = $sx($vMax); $ly2 = $sy($vMax);
                $lineLen = round(sqrt(($lx2-$lx1)**2 + ($ly2-$ly1)**2), 2);
                $lineAngle = round(atan2($ly2-$ly1, $lx2-$lx1) * 180 / M_PI, 2);
            @endphp
            <div class="chart-box" style="width: {{ $boxW }}px; height: {{ $boxH }}px;">
                <div style="position: absolute; left: {{ $pad }}px; top: {{ $boxH - $pad }}px; width: {{ $boxW - 2*$pad }}px; height: 1px; background: #cbd5e1;"></div>
                <div style="position: absolute; left: {{ $pad }}px; top: {{ $pad }}px; width: 1px; height: {{ $boxH - 2*$pad }}px; background: #cbd5e1;"></div>
                <div class="dash-line" style="left: {{ $lx1 }}px; top: {{ $ly1 }}px; width: {{ $lineLen }}px; transform: rotate({{ $lineAngle }}deg);"></div>
                @foreach ($qq as $pt)
                    <div class="dot" style="left: {{ $sx($pt['x']) - 3 }}px; top: {{ $sy($pt['y']) - 3 }}px;"></div>
                @endforeach
            </div>
            <p style="font-size: 8px; color: #9ca3af; margin-top: 4px;">Titik mendekati garis diagonal = data mendekati distribusi normal</p>
        @endif
    @endif

    {{-- Tabel hasil angka --}}
    @php $displayResult = isset($result['result']) ? $result['result'] : $result; @endphp
    <table>
        <tbody>
        @foreach ($displayResult as $key => $val)
            @if (!is_array($val))
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td>
                        @if (is_bool($val))
                            @if ($val)
                                <span class="badge-yes">Ya (Signifikan)</span>
                            @else
                                <span class="badge-no">Tidak Signifikan</span>
                            @endif
                        @else
                            {{ $val }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    {{-- Koefisien regresi berganda --}}
    @if ($analysisResult->analysis_type === 'regression_multiple' && isset($result['coefficients']))
        <h2>Koefisien Regresi</h2>
        <table>
            <thead>
            <tr>
                <th>Variabel</th>
                <th>Koefisien</th>
                <th>Std Error</th>
                <th>t</th>
                <th>p-value</th>
                <th>Signifikan</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result['coefficients'] as $coef)
                <tr>
                    <td style="font-weight:normal">{{ $coef['label'] }}</td>
                    <td>{{ $coef['coefficient'] }}</td>
                    <td>{{ $coef['se'] }}</td>
                    <td>{{ $coef['t'] }}</td>
                    <td>{{ $coef['p_value'] }}</td>
                    <td>
                        @if ($coef['significant'])
                            <span class="badge-yes">Ya *</span>
                        @else
                            <span class="badge-no">Tidak</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif

<div class="footer">
    Digenerate oleh StatWeb &mdash; {{ now()->format('d M Y, H:i:s') }}
</div>

</body>
</html>
