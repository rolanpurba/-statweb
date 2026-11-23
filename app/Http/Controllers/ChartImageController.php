<?php

namespace App\Http\Controllers;

use App\Models\AnalysisResult;
use App\Models\Dataset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ChartImageController extends Controller
{
    private const MAX_BYTES = 3 * 1024 * 1024; // 3 MB

    /**
     * Terima gambar grafik (PNG base64 dari canvas.toDataURL) lalu simpan ke storage.
     */
    public function store(Request $request, AnalysisResult $analysisResult): JsonResponse
    {
        if ($analysisResult->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'chart_image' => ['required', 'string', 'max:4500000'],
        ]);

        $prefix = 'data:image/png;base64,';
        $data = $validated['chart_image'];

        if (!str_starts_with($data, $prefix)) {
            return response()->json(['message' => 'Format gambar harus PNG (data URL).'], 422);
        }

        $binary = base64_decode(substr($data, strlen($prefix)), true);

        if ($binary === false
            || strlen($binary) > self::MAX_BYTES
            || !str_starts_with($binary, "\x89PNG\r\n\x1a\n")) {
            return response()->json(['message' => 'Gambar tidak valid.'], 422);
        }

        $path = "charts/{$analysisResult->id}.png";
        Storage::disk('local')->put($path, $binary);
        $analysisResult->update(['chart_path' => $path]);

        return response()->json(['ok' => true]);
    }

    /**
     * Tampilkan gambar grafik yang tersimpan (dipakai di halaman riwayat).
     */
    public function show(Dataset $dataset, AnalysisResult $analysisResult): BinaryFileResponse
    {
        if ($dataset->user_id !== Auth::id()) abort(403);
        if ($analysisResult->dataset_id !== $dataset->id) abort(403);

        $disk = Storage::disk('local');
        if (!$analysisResult->chart_path || !$disk->exists($analysisResult->chart_path)) abort(404);

        return response()->file($disk->path($analysisResult->chart_path), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
