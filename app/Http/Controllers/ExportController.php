<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportAnalysisPdf(Request $request, Dataset $dataset, AnalysisResult $analysisResult)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        if ($analysisResult->dataset_id !== $dataset->id) abort(403);

        $chartImage = $request->input('chart_image');

        $pdf = Pdf::loadView('exports.analysis_pdf', compact('dataset', 'analysisResult', 'chartImage'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'StatWeb_' . str_replace(' ', '_', $analysisResult->analysis_label) . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
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
