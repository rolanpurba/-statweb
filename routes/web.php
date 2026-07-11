<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/datasets', [DatasetController::class, 'index'])->name('datasets.index');
    Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
    Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
