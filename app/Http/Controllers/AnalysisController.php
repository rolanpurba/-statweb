<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;

class AnalysisController extends Controller
{
    public function create(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $numericColumns = $dataset->columns()->where('data_type', 'numeric')->get();
        $categoricalColumns = $dataset->columns()->where('data_type', 'categorical')->get();
        return view('analysis.create', compact('dataset', 'numericColumns', 'categoricalColumns'));
    }

    public function descriptive(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column' => 'required|string']);
        $columnName = $request->column;
        $values = $this->getNumericValues($dataset, $columnName);
        if (is_string($values)) return back()->withErrors(['column' => $values]);
        if (count($values) < 2) return back()->withErrors(['column' => 'Minimal dibutuhkan 2 baris data.']);
        $stats = [
            'n'       => count($values),
            'mean'    => Average::mean($values),
            'median'  => Average::median($values),
            'mode'    => $this->calculateMode($values),
            'std_dev' => Descriptive::standardDeviation($values, Descriptive::SAMPLE),
            'variance'=> Descriptive::sampleVariance($values),
            'min'     => min($values),
            'max'     => max($values),
            'range'   => max($values) - min($values),
            'q1'      => $this->quartileInclusive($values, 1),
            'q3'      => $this->quartileInclusive($values, 3),
        ];
        $histogram = $this->calculateHistogram($values);
        return view('analysis.descriptive', compact('dataset', 'columnName', 'stats', 'values', 'histogram'));
    }

    public function categorical(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column' => 'required|string']);
        $columnName = $request->column;
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);
        $columnIndex = array_search($columnName, $headerRow);
        if ($columnIndex === false) return back()->withErrors(['column' => 'Kolom tidak ditemukan.']);
        $frequencies = [];
        foreach ($dataRows as $row) {
            $val = trim((string) ($row[$columnIndex] ?? ''));
            if ($val === '') continue;
            $frequencies[$val] = ($frequencies[$val] ?? 0) + 1;
        }
        if (count($frequencies) === 0) return back()->withErrors(['column' => 'Tidak ada data valid.']);
        arsort($frequencies);
        $total = array_sum($frequencies);
        return view('analysis.categorical', compact('dataset', 'columnName', 'frequencies', 'total'));
    }

    public function scatter(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column_x' => 'required|string', 'column_y' => 'required|string|different:column_x']);
        $columnX = $request->column_x;
        $columnY = $request->column_y;
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);
        $indexX = array_search($columnX, $headerRow);
        $indexY = array_search($columnY, $headerRow);
        if ($indexX === false || $indexY === false) return back()->withErrors(['column_x' => 'Kolom tidak ditemukan.']);
        $points = [];
        foreach ($dataRows as $row) {
            $normX = str_replace(',', '.', (string) ($row[$indexX] ?? ''));
            $normY = str_replace(',', '.', (string) ($row[$indexY] ?? ''));
            if (is_numeric($normX) && is_numeric($normY)) {
                $points[] = ['x' => (float) $normX, 'y' => (float) $normY];
            }
        }
        if (count($points) === 0) return back()->withErrors(['column_x' => 'Tidak ada pasangan data numerik valid.']);
        return view('analysis.scatter', compact('dataset', 'columnX', 'columnY', 'points'));
    }

    public function normality(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column' => 'required|string']);
        $columnName = $request->column;
        $values = $this->getNumericValues($dataset, $columnName);
        if (is_string($values)) return back()->withErrors(['column' => $values]);
        if (count($values) < 3) return back()->withErrors(['column' => 'Minimal dibutuhkan 3 data untuk uji normalitas.']);
        $n = count($values);
        if ($n < 50) { $result = $this->shapiroWilk($values); $testName = 'Shapiro-Wilk'; }
        else { $result = $this->kolmogorovSmirnov($values); $testName = 'Kolmogorov-Smirnov (Lilliefors)'; }
        $qqPoints = $this->calculateQQPoints($values);
        return view('analysis.normality', compact('dataset', 'columnName', 'n', 'testName', 'result', 'qqPoints'));
    }

    public function tTestOneSample(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column' => 'required|string', 'test_value' => 'required|numeric']);
        $columnName = $request->column;
        $testValue = (float) $request->test_value;
        $values = $this->getNumericValues($dataset, $columnName);
        if (is_string($values)) return back()->withErrors(['column' => $values]);
        if (count($values) < 2) return back()->withErrors(['column' => 'Minimal dibutuhkan 2 baris data.']);
        $n = count($values);
        $mean = Average::mean($values);
        $stdDev = Descriptive::standardDeviation($values, Descriptive::SAMPLE);
        $se = $stdDev / sqrt($n);
        $t = ($mean - $testValue) / $se;
        $df = $n - 1;
        $pValue = 2 * (1 - $this->tCDF(abs($t), $df));
        $result = [
            'n' => $n, 'mean' => round($mean, 4), 'std_dev' => round($stdDev, 4),
            'se' => round($se, 4), 't' => round($t, 4), 'df' => $df,
            'p_value' => round($pValue, 4), 'test_value' => $testValue,
            'conclusion' => $pValue < 0.05
                ? "Terdapat perbedaan signifikan antara rata-rata sampel ({$mean}) dengan nilai uji ({$testValue}) pada α = 0.05 (tolak H₀)"
                : "Tidak terdapat perbedaan signifikan antara rata-rata sampel ({$mean}) dengan nilai uji ({$testValue}) pada α = 0.05 (gagal tolak H₀)",
            'significant' => $pValue < 0.05,
        ];
        return view('analysis.ttest_one_sample', compact('dataset', 'columnName', 'result'));
    }

    public function tTestIndependent(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column_value' => 'required|string', 'column_group' => 'required|string|different:column_value']);
        $columnValue = $request->column_value;
        $columnGroup = $request->column_group;
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);
        $indexValue = array_search($columnValue, $headerRow);
        $indexGroup = array_search($columnGroup, $headerRow);
        if ($indexValue === false || $indexGroup === false) return back()->withErrors(['column_value' => 'Kolom tidak ditemukan.']);
        $groups = [];
        foreach ($dataRows as $row) {
            $val = $row[$indexValue] ?? null;
            $group = trim((string) ($row[$indexGroup] ?? ''));
            if ($val === null || $val === '' || $group === '') continue;
            $norm = str_replace(',', '.', (string) $val);
            if (!is_numeric($norm)) continue;
            $groups[$group][] = (float) $norm;
        }
        if (count($groups) !== 2) return back()->withErrors(['column_group' => 'Kolom kelompok harus memiliki tepat 2 kategori. Ditemukan: ' . count($groups) . ' kategori.']);
        $groupNames = array_keys($groups);
        $g1 = $groups[$groupNames[0]];
        $g2 = $groups[$groupNames[1]];
        if (count($g1) < 2 || count($g2) < 2) return back()->withErrors(['column_group' => 'Setiap kelompok minimal harus memiliki 2 data.']);
        $n1 = count($g1); $n2 = count($g2);
        $mean1 = Average::mean($g1); $mean2 = Average::mean($g2);
        $var1 = Descriptive::sampleVariance($g1); $var2 = Descriptive::sampleVariance($g2);
        $se = sqrt($var1/$n1 + $var2/$n2);
        $t = ($mean1 - $mean2) / $se;
        $df = (int) floor((($var1/$n1 + $var2/$n2) ** 2) / ((($var1/$n1) ** 2)/($n1-1) + (($var2/$n2) ** 2)/($n2-1)));
        $pValue = 2 * (1 - $this->tCDF(abs($t), $df));
        $result = [
            'group1' => $groupNames[0], 'group2' => $groupNames[1],
            'n1' => $n1, 'n2' => $n2,
            'mean1' => round($mean1, 4), 'mean2' => round($mean2, 4),
            'std1' => round(sqrt($var1), 4), 'std2' => round(sqrt($var2), 4),
            't' => round($t, 4), 'df' => $df, 'p_value' => round($pValue, 4),
            'conclusion' => $pValue < 0.05
                ? "Terdapat perbedaan signifikan antara kelompok {$groupNames[0]} dan {$groupNames[1]} pada α = 0.05 (tolak H₀)"
                : "Tidak terdapat perbedaan signifikan antara kelompok {$groupNames[0]} dan {$groupNames[1]} pada α = 0.05 (gagal tolak H₀)",
            'significant' => $pValue < 0.05,
        ];
        return view('analysis.ttest_independent', compact('dataset', 'columnValue', 'columnGroup', 'result'));
    }

    public function tTestPaired(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate(['column_1' => 'required|string', 'column_2' => 'required|string|different:column_1']);
        $column1 = $request->column_1; $column2 = $request->column_2;
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);
        $index1 = array_search($column1, $headerRow);
        $index2 = array_search($column2, $headerRow);
        if ($index1 === false || $index2 === false) return back()->withErrors(['column_1' => 'Kolom tidak ditemukan.']);
        $val1 = []; $val2 = [];
        foreach ($dataRows as $row) {
            $v1 = $row[$index1] ?? null; $v2 = $row[$index2] ?? null;
            if ($v1 === null || $v1 === '' || $v2 === null || $v2 === '') continue;
            $n1 = str_replace(',', '.', (string) $v1);
            $n2 = str_replace(',', '.', (string) $v2);
            if (is_numeric($n1) && is_numeric($n2)) { $val1[] = (float) $n1; $val2[] = (float) $n2; }
        }
        if (count($val1) < 2) return back()->withErrors(['column_1' => 'Minimal dibutuhkan 2 pasangan data valid.']);
        $differences = array_map(fn($a, $b) => $a - $b, $val1, $val2);
        $n = count($differences);
        $dMean = Average::mean($differences);
        $dStd = Descriptive::standardDeviation($differences, Descriptive::SAMPLE);
        $se = $dStd / sqrt($n);
        $t = $dMean / $se;
        $df = $n - 1;
        $pValue = 2 * (1 - $this->tCDF(abs($t), $df));
        $result = [
            'n' => $n, 'mean1' => round(Average::mean($val1), 4), 'mean2' => round(Average::mean($val2), 4),
            'd_mean' => round($dMean, 4), 'd_std' => round($dStd, 4), 'se' => round($se, 4),
            't' => round($t, 4), 'df' => $df, 'p_value' => round($pValue, 4),
            'conclusion' => $pValue < 0.05
                ? "Terdapat perbedaan signifikan antara {$column1} dan {$column2} pada α = 0.05 (tolak H₀)"
                : "Tidak terdapat perbedaan signifikan antara {$column1} dan {$column2} pada α = 0.05 (gagal tolak H₀)",
            'significant' => $pValue < 0.05,
        ];
        return view('analysis.ttest_paired', compact('dataset', 'column1', 'column2', 'result'));
    }

    // ==================== MINGGU 5 ====================

    // One-Way ANOVA
    public function anova(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate([
            'column_value' => 'required|string',
            'column_group' => 'required|string|different:column_value',
        ]);

        $columnValue = $request->column_value;
        $columnGroup = $request->column_group;

        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);

        $indexValue = array_search($columnValue, $headerRow);
        $indexGroup = array_search($columnGroup, $headerRow);

        if ($indexValue === false || $indexGroup === false) {
            return back()->withErrors(['column_value' => 'Kolom tidak ditemukan.']);
        }

        $groups = [];
        foreach ($dataRows as $row) {
            $val = $row[$indexValue] ?? null;
            $group = trim((string) ($row[$indexGroup] ?? ''));
            if ($val === null || $val === '' || $group === '') continue;
            $norm = str_replace(',', '.', (string) $val);
            if (!is_numeric($norm)) continue;
            $groups[$group][] = (float) $norm;
        }

        if (count($groups) < 2) {
            return back()->withErrors(['column_group' => 'Minimal dibutuhkan 2 kelompok untuk ANOVA.']);
        }

        foreach ($groups as $g => $vals) {
            if (count($vals) < 2) {
                return back()->withErrors(['column_group' => "Kelompok '{$g}' hanya memiliki " . count($vals) . " data. Minimal 2 per kelompok."]);
            }
        }

        $k = count($groups); // jumlah kelompok
        $allValues = array_merge(...array_values($groups));
        $N = count($allValues); // total semua data
        $grandMean = Average::mean($allValues);

        // Sum of Squares Between (SSB)
        $ssb = 0;
        $groupStats = [];
        foreach ($groups as $name => $vals) {
            $ni = count($vals);
            $mi = Average::mean($vals);
            $ssb += $ni * (($mi - $grandMean) ** 2);
            $groupStats[$name] = [
                'n'    => $ni,
                'mean' => round($mi, 4),
                'std'  => round(Descriptive::standardDeviation($vals, Descriptive::SAMPLE), 4),
            ];
        }

        // Sum of Squares Within (SSW)
        $ssw = 0;
        foreach ($groups as $vals) {
            $mi = Average::mean($vals);
            foreach ($vals as $v) {
                $ssw += ($v - $mi) ** 2;
            }
        }

        $dfB = $k - 1;       // df between
        $dfW = $N - $k;      // df within
        $msb  = $ssb / $dfB; // Mean Square Between
        $msw  = $ssw / $dfW; // Mean Square Within
        $f    = $msb / $msw; // F statistic

        // p-value dari distribusi F menggunakan regularized incomplete beta
        $pValue = $this->fPValue($f, $dfB, $dfW);

        $result = [
            'k'          => $k,
            'N'          => $N,
            'grand_mean' => round($grandMean, 4),
            'ssb'        => round($ssb, 4),
            'ssw'        => round($ssw, 4),
            'dfb'        => $dfB,
            'dfw'        => $dfW,
            'msb'        => round($msb, 4),
            'msw'        => round($msw, 4),
            'f'          => round($f, 4),
            'p_value'    => round($pValue, 4),
            'conclusion' => $pValue < 0.05
                ? "Terdapat perbedaan rata-rata yang signifikan antar kelompok pada α = 0.05 (tolak H₀)"
                : "Tidak terdapat perbedaan rata-rata yang signifikan antar kelompok pada α = 0.05 (gagal tolak H₀)",
            'significant'=> $pValue < 0.05,
        ];

        return view('analysis.anova', compact('dataset', 'columnValue', 'columnGroup', 'groupStats', 'result'));
    }

    // Korelasi Pearson & Spearman
    public function correlation(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate([
            'column_x' => 'required|string',
            'column_y' => 'required|string|different:column_x',
            'method'   => 'required|in:pearson,spearman',
        ]);

        $columnX = $request->column_x;
        $columnY = $request->column_y;
        $method  = $request->method;

        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);

        $indexX = array_search($columnX, $headerRow);
        $indexY = array_search($columnY, $headerRow);

        if ($indexX === false || $indexY === false) {
            return back()->withErrors(['column_x' => 'Kolom tidak ditemukan.']);
        }

        $xValues = []; $yValues = [];
        foreach ($dataRows as $row) {
            $vx = str_replace(',', '.', (string) ($row[$indexX] ?? ''));
            $vy = str_replace(',', '.', (string) ($row[$indexY] ?? ''));
            if (is_numeric($vx) && is_numeric($vy)) {
                $xValues[] = (float) $vx;
                $yValues[] = (float) $vy;
            }
        }

        if (count($xValues) < 3) {
            return back()->withErrors(['column_x' => 'Minimal dibutuhkan 3 pasangan data untuk uji korelasi.']);
        }

        $n = count($xValues);

        if ($method === 'pearson') {
            $r = $this->pearsonR($xValues, $yValues);
            $methodName = 'Pearson';
        } else {
            $r = $this->spearmanR($xValues, $yValues);
            $methodName = 'Spearman';
        }

        // t-test untuk signifikansi korelasi
        $t = $r * sqrt($n - 2) / sqrt(1 - $r * $r);
        $df = $n - 2;
        $pValue = 2 * (1 - $this->tCDF(abs($t), $df));

        // Interpretasi kekuatan korelasi
        $absR = abs($r);
        if ($absR >= 0.8) $strength = 'Sangat Kuat';
        elseif ($absR >= 0.6) $strength = 'Kuat';
        elseif ($absR >= 0.4) $strength = 'Sedang';
        elseif ($absR >= 0.2) $strength = 'Lemah';
        else $strength = 'Sangat Lemah';

        $direction = $r >= 0 ? 'Positif' : 'Negatif';

        $result = [
            'n'          => $n,
            'r'          => round($r, 4),
            't'          => round($t, 4),
            'df'         => $df,
            'p_value'    => round($pValue, 4),
            'strength'   => $strength,
            'direction'  => $direction,
            'conclusion' => $pValue < 0.05
                ? "Terdapat korelasi {$direction} yang signifikan ({$strength}) antara {$columnX} dan {$columnY} (r = " . round($r, 4) . ", p < 0.05, tolak H₀)"
                : "Tidak terdapat korelasi yang signifikan antara {$columnX} dan {$columnY} (r = " . round($r, 4) . ", p ≥ 0.05, gagal tolak H₀)",
            'significant'=> $pValue < 0.05,
        ];

        // Scatter plot points untuk visualisasi
        $points = array_map(fn($x, $y) => ['x' => $x, 'y' => $y], $xValues, $yValues);

        return view('analysis.correlation', compact('dataset', 'columnX', 'columnY', 'method', 'methodName', 'result', 'points'));
    }

    // Chi-square Test
    public function chiSquare(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $request->validate([
            'column_1' => 'required|string',
            'column_2' => 'required|string|different:column_1',
        ]);

        $column1 = $request->column_1;
        $column2 = $request->column_2;

        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);

        $index1 = array_search($column1, $headerRow);
        $index2 = array_search($column2, $headerRow);

        if ($index1 === false || $index2 === false) {
            return back()->withErrors(['column_1' => 'Kolom tidak ditemukan.']);
        }

        // Buat contingency table
        $contingency = [];
        $rows1 = []; $cols2 = [];
        foreach ($dataRows as $row) {
            $v1 = trim((string) ($row[$index1] ?? ''));
            $v2 = trim((string) ($row[$index2] ?? ''));
            if ($v1 === '' || $v2 === '') continue;
            $contingency[$v1][$v2] = ($contingency[$v1][$v2] ?? 0) + 1;
            $rows1[$v1] = true;
            $cols2[$v2] = true;
        }

        if (count($rows1) < 2 || count($cols2) < 2) {
            return back()->withErrors(['column_1' => 'Setiap kolom harus memiliki minimal 2 kategori berbeda.']);
        }

        $rowLabels = array_keys($rows1);
        $colLabels = array_keys($cols2);
        sort($rowLabels);
        sort($colLabels);

        // Hitung row totals, col totals, grand total
        $N = 0;
        $observed = [];
        $rowTotals = [];
        $colTotals = [];

        foreach ($rowLabels as $r) {
            $rowTotals[$r] = 0;
            foreach ($colLabels as $c) {
                $observed[$r][$c] = $contingency[$r][$c] ?? 0;
                $rowTotals[$r] += $observed[$r][$c];
                $colTotals[$c] = ($colTotals[$c] ?? 0) + $observed[$r][$c];
                $N += $observed[$r][$c];
            }
        }

        // Hitung chi-square
        $chiSq = 0;
        $expected = [];
        foreach ($rowLabels as $r) {
            foreach ($colLabels as $c) {
                $exp = ($rowTotals[$r] * $colTotals[$c]) / $N;
                $expected[$r][$c] = round($exp, 2);
                if ($exp > 0) {
                    $chiSq += (($observed[$r][$c] - $exp) ** 2) / $exp;
                }
            }
        }

        $df = (count($rowLabels) - 1) * (count($colLabels) - 1);
        $pValue = $this->chiSquarePValue($chiSq, $df);

        // Cramér's V (ukuran effect size)
        $cramersV = sqrt($chiSq / ($N * min(count($rowLabels) - 1, count($colLabels) - 1)));

        $result = [
            'chi_sq'    => round($chiSq, 4),
            'df'        => $df,
            'p_value'   => round($pValue, 4),
            'n'         => $N,
            'cramers_v' => round($cramersV, 4),
            'conclusion'=> $pValue < 0.05
                ? "Terdapat hubungan yang signifikan antara {$column1} dan {$column2} pada α = 0.05 (tolak H₀)"
                : "Tidak terdapat hubungan yang signifikan antara {$column1} dan {$column2} pada α = 0.05 (gagal tolak H₀)",
            'significant'=> $pValue < 0.05,
        ];

        return view('analysis.chi_square', compact(
            'dataset', 'column1', 'column2', 'result',
            'rowLabels', 'colLabels', 'observed', 'expected',
            'rowTotals', 'colTotals', 'N'
        ));
    }

    // ==================== PRIVATE HELPERS ====================

    private function pearsonR(array $x, array $y): float
    {
        $n = count($x);
        $meanX = Average::mean($x);
        $meanY = Average::mean($y);
        $num = 0; $denX = 0; $denY = 0;
        for ($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $meanX;
            $dy = $y[$i] - $meanY;
            $num += $dx * $dy;
            $denX += $dx ** 2;
            $denY += $dy ** 2;
        }
        $denom = sqrt($denX * $denY);
        return $denom > 0 ? $num / $denom : 0;
    }

    private function spearmanR(array $x, array $y): float
    {
        // Convert ke ranking
        $rankX = $this->rank($x);
        $rankY = $this->rank($y);
        return $this->pearsonR($rankX, $rankY);
    }

    private function rank(array $values): array
    {
        $n = count($values);
        $indexed = array_map(fn($v, $i) => ['val' => $v, 'idx' => $i], $values, range(0, $n - 1));
        usort($indexed, fn($a, $b) => $a['val'] <=> $b['val']);

        $ranks = array_fill(0, $n, 0);
        $i = 0;
        while ($i < $n) {
            $j = $i;
            while ($j < $n - 1 && $indexed[$j]['val'] == $indexed[$j+1]['val']) $j++;
            $avgRank = ($i + $j + 2) / 2; // 1-indexed average rank
            for ($k = $i; $k <= $j; $k++) {
                $ranks[$indexed[$k]['idx']] = $avgRank;
            }
            $i = $j + 1;
        }
        return $ranks;
    }

    private function fPValue(float $f, int $df1, int $df2): float
    {
        // P(F > f) = I(df2/(df2+df1*f), df2/2, df1/2)
        $x = $df2 / ($df2 + $df1 * $f);
        return $this->regularizedIncompleteBeta($x, $df2 / 2.0, $df1 / 2.0);
    }

    private function chiSquarePValue(float $chiSq, int $df): float
    {
        // P(X^2 > x) = 1 - regularizedGammaP(df/2, x/2)
        return 1 - $this->regularizedGammaP($df / 2.0, $chiSq / 2.0);
    }

    private function regularizedGammaP(float $a, float $x): float
    {
        if ($x <= 0) return 0.0;
        if ($x < $a + 1) {
            // Series expansion
            $sum = 1.0 / $a;
            $term = $sum;
            for ($i = 1; $i <= 200; $i++) {
                $term *= $x / ($a + $i);
                $sum += $term;
                if (abs($term) < 1e-12 * abs($sum)) break;
            }
            return $sum * exp(-$x + $a * log($x) - $this->logGamma($a));
        } else {
            // Continued fraction
            $b = $x + 1 - $a;
            $C = 1e30;
            $D = 1.0 / $b;
            $f = $D;
            for ($i = 1; $i <= 200; $i++) {
                $an = -$i * ($i - $a);
                $b += 2;
                $D = $an * $D + $b;
                if (abs($D) < 1e-30) $D = 1e-30;
                $C = $b + $an / $C;
                if (abs($C) < 1e-30) $C = 1e-30;
                $D = 1.0 / $D;
                $del = $D * $C;
                $f *= $del;
                if (abs($del - 1) < 1e-12) break;
            }
            return 1 - $f * exp(-$x + $a * log($x) - $this->logGamma($a));
        }
    }

    private function tCDF(float $t, int $df): float
    {
        $x = $df / ($df + $t * $t);
        $ibeta = $this->regularizedIncompleteBeta($x, $df / 2.0, 0.5);
        return 1 - 0.5 * $ibeta;
    }

    private function regularizedIncompleteBeta(float $x, float $a, float $b): float
    {
        if ($x <= 0) return 0.0;
        if ($x >= 1) return 1.0;
        $lbeta = $this->logBeta($a, $b);
        $front = exp(log($x) * $a + log(1 - $x) * $b - $lbeta) / $a;
        if ($x > ($a + 1) / ($a + $b + 2)) {
            return 1 - $this->regularizedIncompleteBeta(1 - $x, $b, $a);
        }
        $C = 1.0; $D = 1 - ($a + $b) * $x / ($a + 1);
        if (abs($D) < 1e-30) $D = 1e-30;
        $D = 1 / $D; $f = $D;
        for ($m = 1; $m <= 200; $m++) {
            $num = $m * ($b - $m) * $x / (($a + 2*$m - 1) * ($a + 2*$m));
            $D = 1 + $num * $D; if (abs($D) < 1e-30) $D = 1e-30;
            $C = 1 + $num / $C; if (abs($C) < 1e-30) $C = 1e-30;
            $D = 1/$D; $f *= $C * $D;
            $num = -($a + $m) * ($a + $b + $m) * $x / (($a + 2*$m) * ($a + 2*$m + 1));
            $D = 1 + $num * $D; if (abs($D) < 1e-30) $D = 1e-30;
            $C = 1 + $num / $C; if (abs($C) < 1e-30) $C = 1e-30;
            $D = 1/$D; $delta = $C * $D; $f *= $delta;
            if (abs($delta - 1) < 1e-10) break;
        }
        return $front * $f;
    }

    private function logBeta(float $a, float $b): float
    {
        return $this->logGamma($a) + $this->logGamma($b) - $this->logGamma($a + $b);
    }

    private function logGamma(float $x): float
    {
        $g = 7;
        $c = [0.99999999999980993, 676.5203681218851, -1259.1392167224028, 771.32342877765313, -176.61502916214059, 12.507343278686905, -0.13857109526572012, 9.9843695780195716e-6, 1.5056327351493116e-7];
        if ($x < 0.5) return log(M_PI / sin(M_PI * $x)) - $this->logGamma(1 - $x);
        $x -= 1; $a = $c[0]; $t = $x + $g + 0.5;
        for ($i = 1; $i < 9; $i++) $a += $c[$i] / ($x + $i);
        return 0.5 * log(2 * M_PI) + ($x + 0.5) * log($t) - $t + log($a);
    }

    private function getNumericValues(Dataset $dataset, string $columnName): array|string
    {
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);
        $columnIndex = array_search($columnName, $headerRow);
        if ($columnIndex === false) return 'Kolom tidak ditemukan.';
        $values = [];
        foreach ($dataRows as $row) {
            $val = $row[$columnIndex] ?? null;
            if ($val !== null && $val !== '') {
                $normalized = str_replace(',', '.', (string) $val);
                if (is_numeric($normalized)) $values[] = (float) $normalized;
            }
        }
        if (count($values) === 0) return 'Tidak ada data numerik valid di kolom ini.';
        return $values;
    }

    private function shapiroWilk(array $values): array
    {
        $x = $values; sort($x); $n = count($x);
        $mean = Average::mean($x);
        $m = [];
        for ($i = 1; $i <= $n; $i++) $m[] = $this->normalQuantile(($i - 0.375) / ($n + 0.25));
        $mDotM = array_sum(array_map(fn($mi) => $mi * $mi, $m));
        $sqrtMDotM = sqrt($mDotM);
        $numerator = 0; $k = (int) floor($n / 2);
        for ($i = 0; $i < $k; $i++) $numerator += ($m[$n-1-$i] / $sqrtMDotM) * ($x[$n-1-$i] - $x[$i]);
        $denominator = array_sum(array_map(fn($xi) => ($xi - $mean) ** 2, $x));
        $W = $denominator > 0 ? min(1.0, max(0.0, ($numerator ** 2) / $denominator)) : 1;
        $mu = 0.0038915*($n**3) - 0.083751*($n**2) - 0.31082*$n - 1.5861;
        $sigma = exp(0.0030302*($n**2) - 0.082676*$n - 0.4803);
        $pValue = min(1.0, max(0.0001, 1 - $this->normalCDF((log(1-$W) - $mu) / $sigma)));
        return ['statistic' => round($W, 4), 'p_value' => round($pValue, 4),
            'conclusion' => $pValue > 0.05 ? 'Data berdistribusi normal (p > 0.05, gagal tolak H₀)' : 'Data tidak berdistribusi normal (p ≤ 0.05, tolak H₀)',
            'normal' => $pValue > 0.05];
    }

    private function kolmogorovSmirnov(array $values): array
    {
        $x = $values; sort($x); $n = count($x);
        $mean = Average::mean($x);
        $stdDev = Descriptive::standardDeviation($x, Descriptive::SAMPLE);
        $dMax = 0;
        foreach ($x as $i => $xi) {
            $z = $stdDev > 0 ? ($xi - $mean) / $stdDev : 0;
            $theoretical = $this->normalCDF($z);
            $dMax = max($dMax, abs(($i+1)/$n - $theoretical), abs($i/$n - $theoretical));
        }
        $dStar = $dMax * (sqrt($n) - 0.01 + 0.85/sqrt($n));
        if ($dStar < 0.302) $pValue = 0.999;
        elseif ($dStar < 0.5) $pValue = max(0.2, 2*exp(-2*$dStar*$dStar*1.1));
        else $pValue = max(0.001, 2*exp(-2*$dStar*$dStar));
        $pValue = min(1.0, max(0.001, $pValue));
        return ['statistic' => round($dMax, 4), 'p_value' => round($pValue, 4),
            'conclusion' => $pValue > 0.05 ? 'Data berdistribusi normal (p > 0.05, gagal tolak H₀)' : 'Data tidak berdistribusi normal (p ≤ 0.05, tolak H₀)',
            'normal' => $pValue > 0.05];
    }

    private function calculateQQPoints(array $values): array
    {
        $x = $values; sort($x); $n = count($x);
        $mean = Average::mean($x);
        $stdDev = Descriptive::standardDeviation($x, Descriptive::SAMPLE);
        $points = [];
        for ($i = 0; $i < $n; $i++) {
            $p = ($i + 1 - 0.375) / ($n + 0.25);
            $points[] = ['x' => round($mean + $stdDev * $this->normalQuantile($p), 4), 'y' => round($x[$i], 4)];
        }
        return $points;
    }

    private function normalQuantile(float $p): float
    {
        if ($p <= 0) return -8; if ($p >= 1) return 8;
        $a = [-3.969683028665376e+01, 2.209460984245205e+02, -2.759285104469687e+02, 1.383577518672690e+02, -3.066479806614716e+01, 2.506628277459239e+00];
        $b = [-5.447609879822406e+01, 1.615858368580409e+02, -1.556989798598866e+02, 6.680131188771972e+01, -1.328068155288572e+01];
        $c = [-7.784894002430293e-03, -3.223964580411365e-01, -2.400758277161838e+00, -2.549732539343734e+00, 4.374664141464968e+00, 2.938163982698783e+00];
        $d = [7.784695709041462e-03, 3.224671290700398e-01, 2.445134137142996e+00, 3.754408661907416e+00];
        $pLow = 0.02425; $pHigh = 1 - $pLow;
        if ($p < $pLow) { $q = sqrt(-2*log($p)); return (((((($c[0]*$q+$c[1])*$q+$c[2])*$q+$c[3])*$q+$c[4])*$q+$c[5])/((((($d[0]*$q+$d[1])*$q+$d[2])*$q+$d[3])*$q+1))); }
        elseif ($p <= $pHigh) { $q = $p-0.5; $r = $q*$q; return (((((($a[0]*$r+$a[1])*$r+$a[2])*$r+$a[3])*$r+$a[4])*$r+$a[5])*$q/(((((($b[0]*$r+$b[1])*$r+$b[2])*$r+$b[3])*$r+$b[4])*$r+1))); }
        else { $q = sqrt(-2*log(1-$p)); return -(((((($c[0]*$q+$c[1])*$q+$c[2])*$q+$c[3])*$q+$c[4])*$q+$c[5])/((((($d[0]*$q+$d[1])*$q+$d[2])*$q+$d[3])*$q+1))); }
    }

    private function normalCDF(float $z): float
    {
        $t = 1/(1+0.2316419*abs($z));
        $d = 0.3989422820*exp(-$z*$z/2);
        $p = $d*$t*(0.3193815+$t*(-0.3565638+$t*(1.7814779+$t*(-1.8212560+$t*1.3302744))));
        return $z > 0 ? 1-$p : $p;
    }

    private function calculateMode(array $values): string
    {
        $counts = array_count_values(array_map(fn($v) => (string) $v, $values));
        $maxCount = max($counts);
        if ($maxCount <= 1) return 'Tidak ada modus (semua nilai unik)';
        foreach ($values as $v) { if ($counts[(string) $v] === $maxCount) return (string) $v; }
        return '-';
    }

    private function quartileInclusive(array $values, int $quartile): float
    {
        sort($values); $n = count($values);
        if ($n === 1) return $values[0];
        $position = ($quartile/4)*($n-1);
        $lo = (int) floor($position); $hi = (int) ceil($position);
        $frac = $position - $lo;
        if ($lo === $hi) return $values[$lo];
        return $values[$lo] + $frac * ($values[$hi] - $values[$lo]);
    }

    private function calculateHistogram(array $values): array
    {
        $n = count($values); $min = min($values); $max = max($values);
        $binCount = max(5, (int) ceil(1+log($n, 2)));
        $range = $max - $min;
        if ($range == 0) return ['labels' => [(string) $min], 'values' => [$n]];
        $binWidth = $range/$binCount;
        $bins = array_fill(0, $binCount, 0); $labels = [];
        for ($i = 0; $i < $binCount; $i++) {
            $lower = $min+($i*$binWidth); $upper = $lower+$binWidth;
            $labels[] = round($lower, 2).' - '.round($upper, 2);
        }
        foreach ($values as $val) {
            $idx = (int) floor(($val-$min)/$binWidth);
            if ($idx >= $binCount) $idx = $binCount-1;
            $bins[$idx]++;
        }
        return ['labels' => $labels, 'values' => $bins];
    }
}
