<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\DatasetColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DatasetController extends Controller
{
    // Tampilkan halaman form upload + list dataset yang sudah ada
    public function index()
    {
        $datasets = Dataset::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('datasets.index', compact('datasets'));
    }

    // Proses upload file
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        // Simpan file ke storage/app/public/datasets
        $path = $request->file('file')->store('datasets', 'public');

        // Simpan record dataset ke database
        $dataset = Dataset::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'file_path' => $path,
        ]);

        // Baca isi file buat ambil nama kolom
        $fullPath = storage_path('app/public/' . $path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];

        // Ambil beberapa baris data buat deteksi tipe kolom (numerik/kategorik)
        $sampleRows = array_slice($rows[0], 1, 20); // ambil 20 baris pertama buat sample

        foreach ($headerRow as $index => $columnName) {
            if (empty($columnName)) continue;

            // Kumpulkan nilai dari kolom ini di beberapa baris sample
            $values = array_column($sampleRows, $index);
            $isNumeric = true;
            $hasAtLeastOneValue = false;

            foreach ($values as $value) {
                if ($value === null || $value === '') continue;
                $hasAtLeastOneValue = true;

                // Normalisasi format angka Indonesia (koma sebagai desimal) ke format standar (titik)
                $normalizedValue = str_replace(',', '.', (string) $value);

                if (!is_numeric($normalizedValue)) {
                    $isNumeric = false;
                    break;
                }
            }

            // Kalau semua nilai sample kosong, anggap kategorik (default aman)
            if (!$hasAtLeastOneValue) {
                $isNumeric = false;
            }

            DatasetColumn::create([
                'dataset_id' => $dataset->id,
                'column_name' => trim($columnName),
                'data_type' => $isNumeric ? 'numeric' : 'categorical',
            ]);
        }

        return redirect()->route('datasets.show', $dataset->id)
            ->with('success', 'Dataset berhasil diupload!');
    }

    // Tampilkan detail dataset (preview data + kolom)
    public function show(Dataset $dataset)
    {
        // Pastikan user cuma bisa lihat dataset miliknya sendiri
        if ($dataset->user_id !== Auth::id()) {
            abort(403);
        }

        $fullPath = storage_path('app/public/' . $dataset->file_path);
        $rows = Excel::toArray([], $fullPath);
        $headerRow = $rows[0][0] ?? [];
        $dataRows = array_slice($rows[0], 1, 50); // preview 50 baris pertama

        $columns = $dataset->columns;

        return view('datasets.show', compact('dataset', 'headerRow', 'dataRows', 'columns'));
    }

    // Hapus dataset
    public function destroy(Dataset $dataset)
    {
        if ($dataset->user_id !== Auth::id()) {
            abort(403);
        }

        \Storage::disk('public')->delete($dataset->file_path);
        $dataset->delete();

        return redirect()->route('datasets.index')
            ->with('success', 'Dataset berhasil dihapus.');
    }
}
