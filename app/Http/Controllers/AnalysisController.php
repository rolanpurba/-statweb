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
    // Tampilkan form pilih kolom untuk dianalisis
    public function create(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) {
            abort(403);
        }

        $numericColumns = $dataset->columns()->where('data_type', 'numeric')->get();
        $categoricalColumns = $dataset->columns()->where('data_type', 'categorical')->get();

        return view('analysis.create', compact('dataset', 'numericColumns', 'categoricalColumns'));
    }

    // Proses hitung statistik deskriptif
    public function descriptive(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'column' => 'required|string',
        ]);

        $columnName = $request->column;

        // Baca file dataset
        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);

        // Cari index kolom yang dipilih
        $columnIndex = array_search($columnName, $headerRow);

        if ($columnIndex === false) {
            return back()->withErrors(['column' => 'Kolom tidak ditemukan.']);
        }

        // Ambil semua nilai numerik dari kolom itu, buang yang kosong/bukan angka
        $values = [];
        foreach ($dataRows as $row) {
            $val = $row[$columnIndex] ?? null;
            if ($val !== null && $val !== '') {
                // Normalisasi format angka Indonesia (koma) ke format standar (titik)
                $normalized = str_replace(',', '.', (string) $val);
                if (is_numeric($normalized)) {
                    $values[] = (float) $normalized;
                }
            }
        }

        if (count($values) === 0) {
            return back()->withErrors(['column' => 'Tidak ada data numerik valid di kolom ini.']);
        }

        if (count($values) < 2) {
            return back()->withErrors(['column' => 'Data numerik valid di kolom ini hanya ' . count($values) . ' baris. Minimal dibutuhkan 2 baris data untuk menghitung standar deviasi dan variansi.']);
        }

        // Hitung statistik deskriptif
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

        // Hitung data histogram untuk visualisasi
        $histogram = $this->calculateHistogram($values);

        return view('analysis.descriptive', compact('dataset', 'columnName', 'stats', 'values', 'histogram'));
    }

    // Proses hitung frekuensi kategori (untuk bar chart data kategorik)
    public function categorical(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'column' => 'required|string',
        ]);

        $columnName = $request->column;

        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1);

        $columnIndex = array_search($columnName, $headerRow);

        if ($columnIndex === false) {
            return back()->withErrors(['column' => 'Kolom tidak ditemukan.']);
        }

        // Hitung frekuensi tiap kategori
        $frequencies = [];
        foreach ($dataRows as $row) {
            $val = trim((string) ($row[$columnIndex] ?? ''));
            if ($val === '') continue;

            $frequencies[$val] = ($frequencies[$val] ?? 0) + 1;
        }

        if (count($frequencies) === 0) {
            return back()->withErrors(['column' => 'Tidak ada data valid di kolom ini.']);
        }

        // Urutkan dari frekuensi terbesar
        arsort($frequencies);

        $total = array_sum($frequencies);

        return view('analysis.categorical', compact('dataset', 'columnName', 'frequencies', 'total'));
    }

    /**
     * Hitung modus. Kalau multimodal, ambil nilai yang pertama kali muncul
     * (konsisten dengan perilaku default Excel MODE/MODE.SNGL).
     */
    private function calculateMode(array $values): string
    {
        $counts = array_count_values(array_map(fn($v) => (string) $v, $values));
        $maxCount = max($counts);

        // Kalau semua nilai unik (nggak ada yang berulang), Excel nunjukin #N/A
        if ($maxCount <= 1) {
            return 'Tidak ada modus (semua nilai unik)';
        }

        foreach ($values as $v) {
            if ($counts[(string) $v] === $maxCount) {
                return (string) $v;
            }
        }

        return '-';
    }

    /**
     * Hitung kuartil pakai metode interpolasi linear,
     * sama persis dengan QUARTILE.INC di Excel.
     */
    private function quartileInclusive(array $values, int $quartile): float
    {
        sort($values);
        $n = count($values);

        if ($n === 1) {
            return $values[0];
        }

        // Posisi dalam array (0-indexed), rumus QUARTILE.INC Excel
        $position = ($quartile / 4) * ($n - 1);

        $lowerIndex = (int) floor($position);
        $upperIndex = (int) ceil($position);
        $fraction = $position - $lowerIndex;

        if ($lowerIndex === $upperIndex) {
            return $values[$lowerIndex];
        }

        return $values[$lowerIndex] + $fraction * ($values[$upperIndex] - $values[$lowerIndex]);
    }

    /**
     * Hitung bins untuk histogram menggunakan aturan Sturges,
     * metode standar untuk menentukan jumlah interval yang wajar.
     */
    private function calculateHistogram(array $values): array
    {
        $n = count($values);
        $min = min($values);
        $max = max($values);

        // Aturan Sturges: k = 1 + log2(n), dibulatkan, minimal 5 bin
        $binCount = max(5, (int) ceil(1 + log($n, 2)));

        $range = $max - $min;
        if ($range == 0) {
            // Semua nilai sama, bikin 1 bin saja
            return [
                'labels' => [(string) $min],
                'values' => [$n],
            ];
        }

        $binWidth = $range / $binCount;

        $bins = array_fill(0, $binCount, 0);
        $labels = [];

        for ($i = 0; $i < $binCount; $i++) {
            $lower = $min + ($i * $binWidth);
            $upper = $lower + $binWidth;
            $labels[] = round($lower, 2) . ' - ' . round($upper, 2);
        }

        foreach ($values as $val) {
            $binIndex = (int) floor(($val - $min) / $binWidth);
            // Nilai maksimum masuk ke bin terakhir (bukan bin baru di luar range)
            if ($binIndex >= $binCount) {
                $binIndex = $binCount - 1;
            }
            $bins[$binIndex]++;
        }

        return [
            'labels' => $labels,
            'values' => $bins,
        ];
    }
}
