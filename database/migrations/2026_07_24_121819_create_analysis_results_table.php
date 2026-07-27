<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('analysis_type'); // misal: descriptive, normality, ttest_one_sample, dll
            $table->string('analysis_label'); // label yang readable: "Statistik Deskriptif - Nilai Ujian"
            $table->json('parameters');  // input yang dipakai (kolom, method, test_value, dll)
            $table->json('result');      // hasil perhitungan lengkap
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
