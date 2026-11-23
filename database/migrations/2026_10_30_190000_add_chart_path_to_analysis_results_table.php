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
        Schema::table('analysis_results', function (Blueprint $table) {
            // Path gambar grafik (PNG) di disk "local", misal: charts/12.png
            $table->string('chart_path')->nullable()->after('result');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_results', function (Blueprint $table) {
            $table->dropColumn('chart_path');
        });
    }
};
