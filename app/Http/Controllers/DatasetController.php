<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\DatasetColumn;
use App\Models\DatasetRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::where('user_id', Auth::id())->latest()->get();
        return view('datasets.index', compact('datasets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200',
        ]);

        $path = $request->file('file')->store('datasets', 'public');

        $dataset = Dataset::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'file_path' => $path,
        ]);

        $fullPath   = storage_path('app/public/' . $path);
        $rows       = Excel::toArray([], $fullPath);
        $headerRow  = $rows[0][0] ?? [];
        $dataRows   = array_slice($rows[0], 1);
        $sampleRows = array_slice($dataRows, 0, 20);

        // Deteksi tipe kolom & simpan ke dataset_columns
        foreach ($headerRow as $index => $columnName) {
            if (empty($columnName)) continue;

            $values = array_column($sampleRows, $index);
            $isNumeric = true;
            $hasAtLeastOneValue = false;

            foreach ($values as $value) {
                if ($value === null || $value === '') continue;
                $hasAtLeastOneValue = true;
                $normalizedValue = str_replace(',', '.', (string) $value);
                if (!is_numeric($normalizedValue)) {
                    $isNumeric = false;
                    break;
                }
            }

            if (!$hasAtLeastOneValue) $isNumeric = false;

            DatasetColumn::create([
                'dataset_id'  => $dataset->id,
                'column_name' => trim($columnName),
                'data_type'   => $isNumeric ? 'numeric' : 'categorical',
                'measure'     => $isNumeric ? 'scale' : 'nominal',
            ]);
        }

        // Simpan tiap baris data ke dataset_rows
        foreach ($dataRows as $rowIndex => $row) {
            $hasData = false;
            foreach ($row as $cell) {
                if ($cell !== null && $cell !== '') { $hasData = true; break; }
            }
            if (!$hasData) continue;

            $rowData = [];
            foreach ($headerRow as $colIndex => $colName) {
                if (empty($colName)) continue;
                $rowData[trim($colName)] = $row[$colIndex] ?? null;
            }

            DatasetRow::create([
                'dataset_id' => $dataset->id,
                'row_number' => $rowIndex + 1,
                'data'       => $rowData,
            ]);
        }

        return redirect()->route('datasets.show', $dataset->id)
            ->with('success', 'Dataset berhasil diupload!');
    }

    public function show(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        $columns  = $dataset->columns;
        $dataRows = $dataset->rows()->paginate(50);
        return view('datasets.show', compact('dataset', 'columns', 'dataRows'));
    }

    public function updateVariables(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $request->validate([
            'columns'             => 'required|array',
            'columns.*.id'        => 'required|integer',
            'columns.*.label'     => 'nullable|string|max:255',
            'columns.*.data_type' => 'required|in:numeric,categorical',
            'columns.*.decimals'  => 'required|integer|min:0|max:10',
            'columns.*.measure'   => 'required|in:nominal,ordinal,scale',
        ]);

        foreach ($request->columns as $colData) {
            DatasetColumn::where('id', $colData['id'])
                ->where('dataset_id', $dataset->id)
                ->update([
                    'label'     => $colData['label'] ?? null,
                    'data_type' => $colData['data_type'],
                    'decimals'  => $colData['decimals'],
                    'measure'   => $colData['measure'],
                ]);
        }

        return redirect()->route('datasets.show', $dataset->id)
            ->with('success', 'Informasi variabel berhasil disimpan.');
    }

    public function updateCell(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $request->validate([
            'row_id'      => 'required|integer',
            'column_name' => 'required|string',
            'value'       => 'nullable|string',
        ]);

        $row = DatasetRow::where('id', $request->row_id)
            ->where('dataset_id', $dataset->id)
            ->firstOrFail();

        $data = $row->data;
        $data[$request->column_name] = $request->value;
        $row->data = $data;
        $row->save();

        return response()->json(['success' => true, 'value' => $request->value]);
    }

    public function addRow(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $lastRow = DatasetRow::where('dataset_id', $dataset->id)
            ->orderBy('row_number', 'desc')
            ->first();

        $nextRowNumber = $lastRow ? $lastRow->row_number + 1 : 1;

        $emptyData = [];
        foreach ($dataset->columns as $col) {
            $emptyData[$col->column_name] = null;
        }

        $row = DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_number' => $nextRowNumber,
            'data'       => $emptyData,
        ]);

        return response()->json([
            'success'    => true,
            'row_id'     => $row->id,
            'row_number' => $row->row_number,
            'data'       => $row->data,
        ]);
    }

    public function deleteRow(Request $request, Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);

        $request->validate(['row_id' => 'required|integer']);

        DatasetRow::where('id', $request->row_id)
            ->where('dataset_id', $dataset->id)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function destroy(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        \Storage::disk('public')->delete($dataset->file_path);
        $dataset->delete();
        return redirect()->route('datasets.index')
            ->with('success', 'Dataset berhasil dihapus.');
    }
}
