<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ExportController extends Controller
{
    public function exportAnalysisPdf(Request $request, Dataset $dataset, AnalysisResult $analysisResult)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        if ($analysisResult->dataset_id !== $dataset->id) abort(403);

        // Dompdf gagal membaca data URI base64 yang panjang, jadi gambar selalu diberikan sebagai FILE di disk.
        // Prioritas: gambar yang baru dikirim form (halaman hasil), kalau tidak ada pakai PNG tersimpan (riwayat).
        $tmpFile = null;
        $chartImage = null;

        $posted = $request->input('chart_image');
        $prefix = 'data:image/png;base64,';
        if (is_string($posted) && str_starts_with($posted, $prefix)) {
            $binary = base64_decode(substr($posted, strlen($prefix)), true);
            if ($binary !== false && str_starts_with($binary, "\x89PNG\r\n\x1a\n")) {
                $dir = storage_path('app/private/tmp');
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $tmpFile = $dir . DIRECTORY_SEPARATOR . 'chart-' . Str::uuid() . '.png';
                file_put_contents($tmpFile, $binary);
                $chartImage = $this->pdfFilePath($tmpFile);
            }
        }

        $chartImage ??= $this->storedChartPath($analysisResult);

        try {
            $pdf = Pdf::loadView('exports.analysis_pdf', compact('dataset', 'analysisResult', 'chartImage'));
            $pdf->setPaper('a4', 'portrait');
            $output = $pdf->output(); // render sekarang, selagi file sementara masih ada
        } finally {
            if ($tmpFile && is_file($tmpFile)) {
                @unlink($tmpFile);
            }
        }

        $filename = 'StatWeb_' . str_replace(' ', '_', $analysisResult->analysis_label) . '_' . now()->format('Ymd_His') . '.pdf';

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename, Str::ascii($filename)),
        ]);
    }

    // Lokasi PNG grafik yang tersimpan di server, dalam bentuk yang dimengerti Dompdf (garis miring biasa).
    private function storedChartPath(AnalysisResult $analysisResult): ?string
    {
        $disk = Storage::disk('local');
        if (!$analysisResult->chart_path || !$disk->exists($analysisResult->chart_path)) {
            return null;
        }

        return $this->pdfFilePath($disk->path($analysisResult->chart_path));
    }

    private function pdfFilePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    public function exportHistoryPdf(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $histories = AnalysisResult::where('dataset_id', $dataset->id)->latest()->get();

        $pdf = Pdf::loadView('exports.history_pdf', compact('dataset', 'histories'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'StatWeb_' . str_replace(' ', '_', $dataset->name) . '_Riwayat_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportDatasetCsv(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $columns = $dataset->columns;
        $rows = $dataset->rows()->orderBy('row_number')->get();

        $filename = 'StatWeb_' . str_replace(' ', '_', $dataset->name) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($columns, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns->pluck('column_name')->toArray(), ';');
            foreach ($rows as $row) {
                $rowData = [];
                foreach ($columns as $col) {
                    $rowData[] = $row->data[$col->column_name] ?? '';
                }
                fputcsv($file, $rowData, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
