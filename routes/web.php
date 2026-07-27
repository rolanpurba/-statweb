<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Dataset routes
    Route::get('/datasets', [DatasetController::class, 'index'])->name('datasets.index');
    Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
    Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');

    // Variable View (POST only — form ada di dalam show.blade.php)
    Route::post('/datasets/{dataset}/variables', [DatasetController::class, 'updateVariables'])->name('datasets.update_variables');

    // Data View editable (AJAX)
    Route::post('/datasets/{dataset}/update-cell', [DatasetController::class, 'updateCell'])->name('datasets.update_cell');
    Route::post('/datasets/{dataset}/add-row', [DatasetController::class, 'addRow'])->name('datasets.add_row');
    Route::delete('/datasets/{dataset}/delete-row', [DatasetController::class, 'deleteRow'])->name('datasets.delete_row');

    // Analysis routes
    Route::get('/datasets/{dataset}/analysis', [AnalysisController::class, 'create'])->name('analysis.create');
    Route::post('/datasets/{dataset}/analysis/descriptive', [AnalysisController::class, 'descriptive'])->name('analysis.descriptive');
    Route::post('/datasets/{dataset}/analysis/categorical', [AnalysisController::class, 'categorical'])->name('analysis.categorical');
    Route::post('/datasets/{dataset}/analysis/scatter', [AnalysisController::class, 'scatter'])->name('analysis.scatter');
    Route::post('/datasets/{dataset}/analysis/normality', [AnalysisController::class, 'normality'])->name('analysis.normality');
    Route::post('/datasets/{dataset}/analysis/ttest-one-sample', [AnalysisController::class, 'tTestOneSample'])->name('analysis.ttest_one_sample');
    Route::post('/datasets/{dataset}/analysis/ttest-independent', [AnalysisController::class, 'tTestIndependent'])->name('analysis.ttest_independent');
    Route::post('/datasets/{dataset}/analysis/ttest-paired', [AnalysisController::class, 'tTestPaired'])->name('analysis.ttest_paired');
    Route::post('/datasets/{dataset}/analysis/anova', [AnalysisController::class, 'anova'])->name('analysis.anova');
    Route::post('/datasets/{dataset}/analysis/correlation', [AnalysisController::class, 'correlation'])->name('analysis.correlation');
    Route::post('/datasets/{dataset}/analysis/chi-square', [AnalysisController::class, 'chiSquare'])->name('analysis.chi_square');
    Route::post('/datasets/{dataset}/analysis/regression-simple', [AnalysisController::class, 'regressionSimple'])->name('analysis.regression_simple');
    Route::post('/datasets/{dataset}/analysis/regression-multiple', [AnalysisController::class, 'regressionMultiple'])->name('analysis.regression_multiple');
    Route::get('/datasets/{dataset}/history', [AnalysisController::class, 'history'])->name('analysis.history');
    Route::get('/datasets/{dataset}/history/{analysisResult}', [AnalysisController::class, 'historyShow'])->name('analysis.history_show');
});

Route::get('/dashboard', function () {
    $userId = Auth::id();
    $datasets = \App\Models\Dataset::where('user_id', $userId)->with('columns')->latest()->get();
    $totalDatasets = $datasets->count();
    $totalRows = \App\Models\DatasetRow::whereIn('dataset_id', $datasets->pluck('id'))->count();
    $latestDataset = $datasets->first();
    $recentDatasets = $datasets->take(5);
    return view('dashboard', compact('totalDatasets', 'totalRows', 'latestDataset', 'recentDatasets'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
