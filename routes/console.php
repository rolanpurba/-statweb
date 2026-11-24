<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('statweb:prune-guests {--hours=48 : Jam ke belakang untuk menghapus guest user}', function () {
    $hours = max(1, (int) $this->option('hours'));
    $cutoff = now()->subHours($hours);

    $guests = \App\Models\User::where('is_guest', true)
        ->where('created_at', '<', $cutoff)
        ->with('datasets.analysisResults')
        ->get();

    $count = 0;
    foreach ($guests as $guest) {
        foreach ($guest->datasets as $dataset) {
            if ($dataset->file_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($dataset->file_path);
            }
            foreach ($dataset->analysisResults as $analysis) {
                if ($analysis->chart_path) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($analysis->chart_path);
                }
            }
        }
        $guest->delete();
        $count++;
    }

    $this->info("Berhasil membersihkan {$count} akun guest dan file terkait (lebih lama dari {$hours} jam).");
})->purpose('Membersihkan akun guest dan file dataset/chart terkait yang sudah kedaluwarsa');
