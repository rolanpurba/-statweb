<?php

namespace App\Support;

/**
 * Menggambar grafik batang / scatter sebagai HTML sederhana untuk Dompdf
 * (hanya pakai position:absolute, px, warna solid — tanpa JavaScript, tanpa SVG).
 *
 * Dipakai sebagai cadangan saat PNG dari Chart.js belum tersimpan (analisis lama),
 * lengkap dengan sumbu X/Y, angka skala, garis bantu, dan judul sumbu.
 */
class PdfChart
{
    private const W = 500;
    private const H = 258;
    private const PAD_L = 46;
    private const PAD_R = 14;
    private const PAD_T = 24;
    private const PAD_B = 40;

    /**
     * @param array<int,string>     $labels  label tiap batang (sumbu X)
     * @param array<int,int|float>  $values  tinggi tiap batang (sumbu Y)
     * @param array{color?:string,gap?:float,x_title?:string,y_title?:string} $opts
     */
    public static function bars(array $labels, array $values, array $opts = []): string
    {
        $n = count($values);
        if ($n === 0) {
            return '';
        }

        $color  = $opts['color'] ?? '#0F2C59';
        $gap    = $opts['gap'] ?? 0.08;
        $xTitle = $opts['x_title'] ?? '';
        $yTitle = $opts['y_title'] ?? 'Frekuensi';

        $plotW = self::W - self::PAD_L - self::PAD_R;
        $plotH = self::H - self::PAD_T - self::PAD_B;
        $left  = self::PAD_L;
        $top   = self::PAD_T;
        $base  = $top + $plotH; // posisi garis dasar (sumbu X)

        $maxVal = max(array_map('floatval', $values));
        [$yMax, $step] = self::niceScale($maxVal);

        $html = self::open();
        $html .= self::text($yTitle, 4, 4, 200, 'left', 8, true);

        // garis bantu + angka sumbu Y
        for ($v = 0; $v <= $yMax + 1e-9; $v += $step) {
            $y = $base - ($v / $yMax) * $plotH;
            if ($v > 0) {
                $html .= self::rect($left, $y, $plotW, 1, '#e5e7eb');
            }
            $html .= self::text(self::fmt($v), 0, $y - 5, $left - 6, 'right', 8, false, '#4b5563');
        }

        // batang, angka di atas batang, label sumbu X
        $slot = $plotW / $n;
        $barW = max(2, $slot * (1 - $gap));
        $maxChars = max(4, (int) floor($slot / 3.6));
        foreach ($values as $i => $value) {
            $value = (float) $value;
            $h = ($value / $yMax) * $plotH;
            $x = $left + $i * $slot + ($slot - $barW) / 2;

            if ($h > 0) {
                $html .= self::rect($x, $base - $h, $barW, $h, $color);
            }
            $html .= self::text(self::fmt($value), $left + $i * $slot, $base - $h - 11, $slot, 'center', 8, true, '#111827');

            $label = (string) ($labels[$i] ?? '');
            if (mb_strlen($label) > $maxChars * 2) {
                $label = mb_substr($label, 0, $maxChars * 2 - 1) . '…';
            }
            $html .= self::text($label, $left + $i * $slot, $base + 4, $slot, 'center', 7, false, '#4b5563', 22);
        }

        // sumbu (digambar terakhir supaya di atas garis bantu)
        $html .= self::rect($left, $top, 1, $plotH, '#374151');
        $html .= self::rect($left, $base, $plotW, 1, '#374151');

        if ($xTitle !== '') {
            $html .= self::text($xTitle, $left, self::H - 15, $plotW, 'center', 8, true);
        }

        return $html . '</div>';
    }

    /**
     * @param array<int,array{x:int|float,y:int|float}> $points
     * @param array{x_title?:string,y_title?:string,color?:string} $opts
     */
    public static function scatter(array $points, array $opts = []): string
    {
        if (count($points) === 0) {
            return '';
        }

        $color  = $opts['color'] ?? '#0F2C59';
        $xTitle = $opts['x_title'] ?? 'X';
        $yTitle = $opts['y_title'] ?? 'Y';

        $plotW = self::W - self::PAD_L - self::PAD_R;
        $plotH = self::H - self::PAD_T - self::PAD_B;
        $left  = self::PAD_L;
        $top   = self::PAD_T;
        $base  = $top + $plotH;

        $xs = array_map('floatval', array_column($points, 'x'));
        $ys = array_map('floatval', array_column($points, 'y'));
        [$xMin, $xMax, $xStep] = self::niceRange(min($xs), max($xs));
        [$yMin, $yMax, $yStep] = self::niceRange(min($ys), max($ys));
        $xTicks = (int) round(($xMax - $xMin) / $xStep);
        $yTicks = (int) round(($yMax - $yMin) / $yStep);

        $html = self::open();
        $html .= self::text($yTitle, 4, 4, 300, 'left', 8, true);

        // sumbu Y: garis bantu mendatar + angka
        for ($i = 0; $i <= $yTicks; $i++) {
            $yv = $yMin + $i * $yStep;
            $y = $base - ($i / $yTicks) * $plotH;
            if ($i > 0) {
                $html .= self::rect($left, $y, $plotW, 1, '#e5e7eb');
            }
            $html .= self::text(self::fmt($yv), 0, $y - 5, $left - 6, 'right', 8, false, '#4b5563');
        }

        // sumbu X: garis bantu tegak + angka
        for ($i = 0; $i <= $xTicks; $i++) {
            $xv = $xMin + $i * $xStep;
            $x = $left + ($i / $xTicks) * $plotW;
            if ($i > 0) {
                $html .= self::rect($x, $top, 1, $plotH, '#f3f4f6');
            }
            $html .= self::text(self::fmt($xv), $x - 25, $base + 4, 50, 'center', 8, false, '#4b5563');
        }

        foreach ($points as $pt) {
            $px = $left + ((floatval($pt['x']) - $xMin) / ($xMax - $xMin)) * $plotW;
            $py = $base - ((floatval($pt['y']) - $yMin) / ($yMax - $yMin)) * $plotH;
            $html .= '<div style="position:absolute;left:' . self::n($px - 3) . 'px;top:' . self::n($py - 3)
                . 'px;width:6px;height:6px;border-radius:3px;background:' . $color . ';"></div>';
        }

        $html .= self::rect($left, $top, 1, $plotH, '#374151');
        $html .= self::rect($left, $base, $plotW, 1, '#374151');
        $html .= self::text($xTitle, $left, self::H - 15, $plotW, 'center', 8, true);

        return $html . '</div>';
    }

    // ---------------------------------------------------------------- helpers

    private static function open(): string
    {
        return '<div style="position:relative;width:' . self::W . 'px;height:' . self::H
            . 'px;border:1px solid #e5e7eb;background:#ffffff;font-family:DejaVu Sans,Arial,sans-serif;">';
    }

    private static function rect(float $x, float $y, float $w, float $h, string $bg): string
    {
        return '<div style="position:absolute;left:' . self::n($x) . 'px;top:' . self::n($y)
            . 'px;width:' . self::n($w) . 'px;height:' . self::n($h) . 'px;background:' . $bg . ';"></div>';
    }

    private static function text(
        string $text,
        float $x,
        float $y,
        float $w,
        string $align,
        int $size,
        bool $bold,
        string $color = '#111827',
        ?int $maxHeight = null
    ): string {
        $style = 'position:absolute;left:' . self::n($x) . 'px;top:' . self::n($y) . 'px;width:' . self::n($w)
            . 'px;text-align:' . $align . ';font-size:' . $size . 'px;color:' . $color . ';line-height:1.15;'
            . ($bold ? 'font-weight:bold;' : '')
            . ($maxHeight ? 'height:' . $maxHeight . 'px;overflow:hidden;' : '');

        return '<div style="' . $style . '">' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    /** Skala sumbu yang "bulat": mengembalikan [batas atas, langkah]. */
    private static function niceScale(float $max): array
    {
        if ($max <= 0) {
            return [1.0, 1.0];
        }

        $magnitude = 1.0;
        while (true) {
            foreach ([1, 2, 5] as $m) {
                $step = $m * $magnitude;
                if (ceil($max / $step) <= 6) {
                    return [ceil($max / $step) * $step, $step];
                }
            }
            $magnitude *= 10;
        }
    }

    /** Rentang sumbu dengan angka "bulat": mengembalikan [batas bawah, batas atas, langkah]. */
    private static function niceRange(float $min, float $max, int $target = 5): array
    {
        if ($max - $min < 1e-12) {
            $min -= 1;
            $max += 1;
        }

        $raw = ($max - $min) / $target;
        $mag = 10 ** floor(log10($raw));
        $norm = $raw / $mag;
        $step = ($norm <= 1 ? 1 : ($norm <= 2 ? 2 : ($norm <= 5 ? 5 : 10))) * $mag;

        return [floor($min / $step) * $step, ceil($max / $step) * $step, $step];
    }

    private static function fmt(float $v): string
    {
        $s = number_format($v, abs($v) >= 100 ? 1 : 2, '.', '');

        return rtrim(rtrim($s, '0'), '.') ?: '0';
    }

    private static function n(float $v): string
    {
        return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') ?: '0';
    }
}
